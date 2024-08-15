<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetResource\Pages;
use App\Filament\Resources\TimesheetResource\RelationManagers;
use App\Models\Activity;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\IssueHour;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * TimesheetResource class.
 *
 * This class represents a resource for managing timesheets.
 * It extends the Resource class and provides methods for defining forms, tables, relations, and pages.
 */
class TimesheetResource extends Resource
{
    protected static ?string $model = IssueHour::class;
    protected static ?string $navigationIcon = 'fas fa-calendar-check';
    protected static ?int $navigationSort = 4;

    /**
     * Retrieves the navigation label for the TimesheetResource.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Timesheet');
    }

    /**
     * Get the plural label for the TimesheetResource.
     *
     * @return string|null The plural label for the TimesheetResource.
     */
    public static function getPluralLabel(): ?string
    {
        $singular = static::getNavigationLabel();
        return __('%s', $singular) ?? __('Timesheets');
    }

    /**
     * Retrieve the navigation group for the TimesheetResource.
     *
     * @return string|null The navigation group for the TimesheetResource, or null if not set.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('Timesheet');
    }

    /**
     * Determines if the TimesheetResource should be registered in the navigation.
     *
     * @return bool Returns true if the TimesheetResource should be registered in the navigation, false otherwise.
     */
    public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'List Timesheet Data' permission.
        $user = Auth::user();
        $permission = 'List Timesheet Data';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission);
        }
        return false;
    }

    /**
     * Create a form for the TimesheetResource.
     *
     * @param Form $form The form instance.
     * @return Form The form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('activity_id')
                            ->label(__('Activity'))
                            ->searchable()
                            ->reactive()
                            ->options(function ($get, $set) {
                                return Activity::all()->pluck('name', 'id')->toArray();
                            }),
                        TextInput::make('value')
                            ->label(__('Time to log'))
                            ->numeric()
                            ->required(),

                        Textarea::make('comment')
                            ->label(__('Comment'))
                            ->rows(3),
                    ])
            ]);
    }

    /**
     * Define the table for the TimesheetResource.
     *
     * @param Table $table The table instance.
     * @return Table The modified table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Owner'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => view('components.user-avatar', ['user' => $record->user]))
                    ->searchable(),

                TextColumn::make('value')
                    ->label(__('Hours'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('comment')
                    ->label(__('Comment'))
                    ->limit(50)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('activity.name')
                    ->label(__('Activity'))
                    ->sortable(),

                TextColumn::make('issue.name')
                    ->label(__('Issue'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteAction::make(),
            ]);
    }

    /**
     * Get the relations for the TimesheetResource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns an array of pages for the TimesheetResource.
     *
     * @return array An array of pages with their corresponding routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimesheets::route('/'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
