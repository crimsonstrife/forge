<?php

namespace App\Filament\Resources\FeedbackPosts;

use App\Filament\Resources\FeedbackPosts\Pages\EditFeedbackPost;
use App\Filament\Resources\FeedbackPosts\Pages\ListFeedbackPosts;
use App\Models\FeedbackPost;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackPostResource extends Resource
{
    protected static ?string $model = FeedbackPost::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Support';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post')->schema([
                Forms\Components\TextInput::make('key')->disabled(),
                Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                Forms\Components\Select::make('status_id')->relationship('status', 'name')->required(),
                Forms\Components\Select::make('category_id')->relationship('category', 'name'),
                Forms\Components\Textarea::make('body')->rows(8)->required()->columnSpanFull(),
                Forms\Components\Toggle::make('is_pinned'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable()->badge(),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('board.name')->label('Board'),
                Tables\Columns\TextColumn::make('status.name')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('category.name')->placeholder('-'),
                Tables\Columns\TextColumn::make('identity.display_name')->label('Author'),
                Tables\Columns\TextColumn::make('net_score')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('comment_count')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('last_activity_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('board_id')->relationship('board', 'name'),
                Tables\Filters\SelectFilter::make('status_id')->relationship('status', 'name'),
                Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name'),
            ])
            ->defaultSort('last_activity_at', 'desc')
            ->recordActions([
                Actions\Action::make('togglePin')
                    ->label(fn (FeedbackPost $record) => $record->is_pinned ? 'Unpin' : 'Pin')
                    ->action(fn (FeedbackPost $record) => $record->forceFill([
                        'is_pinned' => ! $record->is_pinned,
                        'pinned_at' => $record->is_pinned ? null : now(),
                    ])->save()),
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeedbackPosts::route('/'),
            'edit' => EditFeedbackPost::route('/{record}/edit'),
        ];
    }
}
