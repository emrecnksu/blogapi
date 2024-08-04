<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Models\Tag;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->label('Başlık')
                    ->reactive()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->label('Slug')
                    ->unique(Post::class, 'slug', ignoreRecord: true)
                    ->disabled(fn ($record) => $record !== null),
                Textarea::make('content')
                    ->required()
                    ->label('İçerik'),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('tags', null)),
                Select::make('tags')
                    ->label('Etiketler')
                    ->multiple()
                    ->options(function (callable $get) {
                        $categoryId = $get('category_id');
                        if ($categoryId) {
                            return Tag::where('category_id', $categoryId)->pluck('name', 'id');
                        }
                        return Tag::all()->pluck('name', 'id');
                    })
                    ->relationship('tags', 'name')
                    ->required(),
                DateTimePicker::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->required(),
                DateTimePicker::make('end_date')
                    ->label('Bitiş Tarihi')
                    ->nullable(),
                TextInput::make('image')
                    ->label('Resim URL')
                    ->url(),
                Toggle::make('status')
                    ->label('Durum')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->hidden(fn () => !auth()->user()->hasRole('super-admin')),
                Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Gönderi ID'),
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->disk('public'),
                TextColumn::make('title')->label('Başlık'),
                TextColumn::make('slug')->label('Slug'),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('tags')
                    ->label('Etiketler')
                    ->getStateUsing(function ($record) {
                        return $record->tags->pluck('name')->implode(', ');
                    })
                    ->sortable(),
                TextColumn::make('user.name')->label('Kullanıcı'),
                TextColumn::make('user.roles')
                    ->label('Rol')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->getStateUsing(fn ($record) => $record->user->roles->pluck('name')->toArray()),
                TextColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Aktif' : 'Pasif';
                    })
                    ->colors([
                        'text-green-600' => 'Aktif',
                        'text-red-600' => 'Pasif',
                    ]),
                TextColumn::make('created_at')->label('Oluşturulma Tarihi')->dateTime(),
                TextColumn::make('start_date')->label('Yayınlanma Tarihi')->dateTime(),
                TextColumn::make('end_date')->label('Bitiş Tarihi')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('approve')
                ->label('Onayla')
                ->icon('heroicon-o-check-circle')
                ->action(function (Post $record) {
                    $record->update(['status' => true]);
                })
                ->visible(fn (Post $record) => !$record->status && auth()->user()->hasRole('super-admin')),
                Tables\Actions\Action::make('disapprove')
                ->label('Pasifleştir')
                ->icon('heroicon-o-x-circle')
                ->action(function (Post $record) {
                    $record->update(['status' => false]);
                })
                ->visible(fn (Post $record) => $record->status && auth()->user()->hasRole('super-admin')),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

