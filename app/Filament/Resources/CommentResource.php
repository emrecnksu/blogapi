<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\CommentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommentResource\RelationManagers;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('post_id')
                ->required()
                ->label('Gönderi ID'),
            TextInput::make('user_id')
                ->required()
                ->label('Kullanıcı ID'),
            Textarea::make('content')
                ->required()
                ->label('İçerik'),
            DateTimePicker::make('start_date')
                ->label('Oluşturulma Tarihi')
                ->required(),
            Toggle::make('approved')
                ->label('Onay Durumu')
                ->inline(false)
                ->onColor('success')
                ->offColor('danger'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')->label('Yorum ID'),
            TextColumn::make('post_id')->label('Gönderi ID'),
            TextColumn::make('user_id')->label('Kullanıcı ID'),
            TextColumn::make('content')->label('İçerik'),
            ToggleColumn::make('approved')->label('Onay Durumu')
                ->onColor('success')
                ->offColor('danger'),
            TextColumn::make('created_at')->label('Oluşturulma Tarihi')->dateTime(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}