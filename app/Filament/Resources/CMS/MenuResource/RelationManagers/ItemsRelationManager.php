<?php

namespace App\Filament\Resources\CMS\MenuResource\RelationManagers;

use App\Models\CMS\Menu;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webid\Druid\Enums\MenuItemTarget;
use Webid\Druid\Models\Page;
use Webid\Druid\Models\Post;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        $targetOptions = [];
        foreach (MenuItemTarget::cases() as $target) {
            $targetOptions[$target->value] = $target->getLabel();
        }

        /** @var int $menuId */
        $menuId = $this->ownerRecord->getKey();

        return $form
            ->schema([
                Select::make('parent_item_id')
                    ->label(__('Parent'))
                    ->placeholder(__('Select a parent item'))
                    ->options(fn () => Menu::all()
                        ->when($menuId, fn ($query) => $query->where('menu_id', $menuId))
                        ->pluck('label', 'id')
                        ->map(function ($label, $id) {
                            return $label ?? 'Item ID #' . $id;
                        })
                        ->toArray()),
                TextInput::make('order')
                    ->label(__('Order'))
                    ->numeric()
                    ->nullable()
                    ->default(0),
                Select::make('target')
                    ->label(__('Target'))
                    ->options($targetOptions)
                    ->default(MenuItemTarget::SELF->value),
                TextInput::make('label')
                    ->label(__('Label'))
                    ->required(),
                Section::make('link')
                    ->schema([
                        Select::make('type')
                            ->label(__('Type'))
                            ->live()
                            ->required()
                            ->options(
                                [
                                    'page' => __('Link to an existing page'),
                                    'custom' => __('Custom URL'),
                                ],
                            ),
                        TextInput::make('custom_url')
                            ->url()
                            ->required(fn (Get $get): bool => $get('type') === 'custom')
                            ->visible(fn (Get $get): bool => $get('type') === 'custom'),
                        MorphToSelect::make('model')
                            ->label(__('Model'))
                            ->visible(fn (Get $get): bool => $get('type') === 'page')
                            ->required(fn (Get $get): bool => $get('type') === 'page')
                            ->types([
                                Type::make(Page::class)
                                    ->titleAttribute('title'),
                                Type::make(Post::class)
                                    ->titleAttribute('title'),
                            ]),

                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
