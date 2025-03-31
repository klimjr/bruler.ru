<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Models\Product;
use App\Filament\Resources\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->oldDiscount = $this->record->getOriginal('discount');
    }

    protected function afterSave(): void
    {
        $newDiscount = (float) $this->record->discount;
        if ($this->oldDiscount != $newDiscount) {
            $products = Product::where('category_id', $this->record->id)->get();
            foreach ($products as $product) {
                if (($newDiscount == null || $newDiscount == 0) && $this->oldDiscount == $product->discount) {
                    $product->discount = $newDiscount;
                }

                if ($newDiscount > 0 && $newDiscount > $product->discount) {
                    $test[] = $product;
                    $product->discount = $newDiscount;
                }
                $product->save();
            }
        }
    }
}
