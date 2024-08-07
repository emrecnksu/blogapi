<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TextContent;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TextContentResource\Pages;
use App\Filament\Resources\TextContentResource\RelationManagers;

class TextContentResource extends Resource
{
    protected static ?string $model = TextContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options([
                        'privacy_policy' => 'Gizlilik Politikası',
                        'kvkk' => 'KVKK',
                    ])
                    ->required(),
                Textarea::make('text_content')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('text_content')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTextContents::route('/'),
            'create' => Pages\CreateTextContent::route('/create'),
            'edit' => Pages\EditTextContent::route('/{record}/edit'),
        ];
    }
}
