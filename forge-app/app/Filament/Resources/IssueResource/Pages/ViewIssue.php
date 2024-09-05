<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Exports\IssueHoursExport;
use App\Filament\Resources\IssueResource;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\IssueHour;
use App\Models\IssueSubscriber;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class responsible for viewing and managing issue records in the application.
 */
class ViewIssue extends ViewRecord implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = IssueResource::class;

    protected static string $view = 'filament.resources.issues.view';

    public string $tab = 'comments';

    protected $listeners = ['doDeleteComment'];

    public $selectedCommentId;

    /**
     * Mounts the ViewIssues page.
     *
     * @param int | string $record The record to be mounted.
     * @return void
     */
    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->form->fill();
    }

    /**
     * Retrieves an array of actions that can be performed on the current issue record.
     *
     * This method defines various actions such as subscribing/unsubscribing to the issue,
     * sharing the issue, logging hours, and exporting logged hours.
     *
     * @return array The array of actions available for the current issue record.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleSubscribe')
                ->label(
                    fn () => $this->record->subscribers()->where('users.id', auth::user()->id)->count() ?
                        __('Unsubscribe')
                        : __('Subscribe')
                )
                ->color(
                    fn () => $this->record->subscribers()->where('users.id', auth::user()->id)->count() ?
                        'danger'
                        : 'success'
                )
                ->icon('fas fa-bell')
                ->button()
                ->action(function () {
                    if (
                        $sub = IssueSubscriber::where('user_id', auth::user()->id)
                            ->where('issue_id', $this->record->id)
                            ->first()
                    ) {
                        $sub->delete();
                        $this->notify('success', __('You unsubscribed from the issue'));
                    } else {
                        IssueSubscriber::create([
                            'user_id' => auth::user()->id,
                            'issue_id' => $this->record->id
                        ]);
                        $this->notify('success', __('You subscribed to the issue'));
                    }
                    $this->record->refresh();
                }),
            Action::make('share')
                ->label(__('Share'))
                ->color('secondary')
                ->button()
                ->icon('heroicon-o-share')
                ->action(fn () => $this->dispatchBrowserEvent('shareIssue', [
                    'url' => route('filament.resources.issues.share', $this->record->code)
                ])),
            EditAction::make(),
            Action::make('logHours')
                ->label(__('Log time'))
                ->icon('fas fa-stopwatch')
                ->color('warning')
                ->modalWidth('sm')
                ->modalHeading(__('Log worked time'))
                ->modalDescription(__('Use the following form to add your worked time in this issue.'))
                ->modalSubmitActionLabel(__('Log'))
                ->visible(fn () => in_array(
                    auth::user()->id,
                    [$this->record->owner_id, $this->record->responsible_id]
                ))
                ->form([
                    TextInput::make('time')
                        ->label(__('Time to log'))
                        ->numeric()
                        ->required(),
                    Select::make('activity_id')
                        ->label(__('Activity'))
                        ->searchable()
                        ->reactive()
                        ->options(function ($get, $set) {
                            return Activity::all()->pluck('name', 'id')->toArray();
                        }),
                    Textarea::make('comment')
                        ->label(__('Comment'))
                        ->rows(3),
                ])
                ->action(function (Collection $records, array $data): void {
                    $value = $data['time'];
                    $comment = $data['comment'];
                    IssueHour::create([
                        'issue_id' => $this->record->id,
                        'activity_id' => $data['activity_id'],
                        'user_id' => auth::user()->id,
                        'value' => $value,
                        'comment' => $comment
                    ]);
                    $this->record->refresh();
                    $this->notify('success', __('Time logged into issue'));
                }),
            ActionGroup::make([
                Action::make('exportLogHours')
                    ->label(__('Export time logged'))
                    ->icon('fas fa-download')
                    ->color('warning')
                    ->visible(
                        fn () => $this->record->watchers->where('id', auth::user()->id)->count()
                            && $this->record->hours()->count()
                    )
                    ->action(fn () => Excel::download(
                        new IssueHoursExport($this->record),
                        'time_' . str_replace('-', '_', $this->record->code) . '.csv',
                        \Maatwebsite\Excel\Excel::CSV,
                        ['Content-Type' => 'text/csv']
                    )),
            ])
                ->visible(fn () => (in_array(
                    auth::user()->id,
                    [$this->record->owner_id, $this->record->responsible_id]
                )) || (
                    $this->record->watchers->where('id', auth::user()->id)->count()
                    && $this->record->hours()->count()
                ))
                ->color('secondary'),
        ];
    }

    /**
     * Selects a tab and sets the current tab property.
     *
     * @param string $tab The name of the tab to be selected.
     * @return void
     */
    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }

    /**
     * Returns the schema for the form.
     *
     * @return array The form schema containing the necessary fields and configurations.
     */
    protected function getFormSchema(): array
    {
        return [
            RichEditor::make('comment')
                ->hiddenlabel()
                ->placeholder(__('Type a new comment'))
                ->required()
        ];
    }

    /**
     * Submits a comment.
     *
     * @return void
     */
    public function submitComment(): void
    {
        $data = $this->form->getState();
        if ($this->selectedCommentId) {
            Comment::where('id', $this->selectedCommentId)
                ->update([
                    'content' => $data['comment']
                ]);
        } else {
            Comment::create([
                'user_id' => auth::user()->id,
                'issue_id' => $this->record->id,
                'content' => $data['comment']
            ]);
        }
        $this->record->refresh();
        $this->cancelEditComment();
        $this->notify('success', __('Comment saved'));
    }

    /**
     * Determines whether the user is an administrator of the system.
     *
     * @return boolean
     */
    public function isAdministrator(): bool
    {
        return $this->record
                ->project
                ->users()
                ->where('users.id', auth::user()->id)
                ->where('role', 'administrator')
                ->count() != 0;
    }

    /**
     * Edits a comment.
     *
     * @param int $commentId The ID of the comment to be edited.
     * @return void
     */
    public function editComment(int $commentId): void
    {
        $this->form->fill([
            'comment' => $this->record->comments->where('id', $commentId)->first()?->content
        ]);
        $this->selectedCommentId = $commentId;
    }

    /**
     * Deletes a comment.
     *
     * @param int $commentId The ID of the comment to be deleted.
     * @return void
     */
    public function deleteComment(int $commentId): void
    {
        Notification::make()
            ->warning()
            ->title(__('Delete confirmation'))
            ->body(__('Are you sure you want to delete this comment?'))
            ->actions([
                Action::make('confirm')
                    ->label(__('Confirm'))
                    ->color('danger')
                    ->button()
                    ->close()
                    ->dispatch(fn () => $this->emit('doDeleteComment', $commentId)),
                Action::make('cancel')
                    ->label(__('Cancel'))
                    ->close()
            ])
            ->persistent()
            ->send();
    }

    /**
     * Deletes a comment.
     *
     * @param int $commentId The ID of the comment to be deleted.
     * @return void
     */
    public function doDeleteComment(int $commentId): void
    {
        Comment::where('id', $commentId)->delete();
        $this->record->refresh();
        $this->notify('success', __('Comment deleted'));
    }

    /**
     * Cancels the editing of a comment.
     *
     * @return void
     */
    public function cancelEditComment(): void
    {
        $this->form->fill();
        $this->selectedCommentId = null;
    }

    /**
     * Overrides the getFormStatePath method to set its access level to public.
     *
     * @return string
     */
    public function getFormStatePath(): string
    {
        return 'form';
    }
}
