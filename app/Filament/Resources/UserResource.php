<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Forms\Components\Select;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Ad'),
                Forms\Components\TextInput::make('surname')
                    ->required()
                    ->maxLength(255)
                    ->label('Soyad'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required()
                    ->label('E-posta'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->revealable()
                    ->label('Şifre')
                    ->dehydrateStateUsing(function ($state) {
                        return !empty($state) ? Hash::make($state) : null;
                    })
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser),
                \Filament\Forms\Components\Select::make('roles')
                    ->label('Roller')
                    ->required()
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->preload()
                    ->required(),
                DateTimePicker::make('start_date')
                    ->label('Oluşturulma Tarihi')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Durum')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('Kullanıcı ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Ad'),
                Tables\Columns\TextColumn::make('surname')
                    ->searchable()
                    ->label('Soyad'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('E-posta'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roller')
                    ->separator(', '),
                TextColumn::make('created_at')->label('Oluşturulma Tarihi')->dateTime(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Durum'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('deactivate')
                    ->label('Devre Dışı Bırak')
                    ->icon('heroicon-o-x-circle')
                    ->action(function ($record) {
                        $record->update(['is_active' => false]);
                    })
                    ->hidden(fn($record) => !$record->is_active),
                Action::make('activate')
                    ->label('Aktif Et')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $record->update(['is_active' => true]);
                    })
                    ->hidden(fn($record) => $record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
