<?php

namespace App\Filament\Resources\CoinResource\Pages;

use App\Filament\Resources\CoinResource;
use App\Services\ImageOptimizationService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditCoin extends EditRecord
{
    protected static string $resource = CoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $coin = $this->record;
        $imageService = app(ImageOptimizationService::class);

        foreach ($coin->images as $image) {
            $disk = Storage::disk('public');
            if ($disk->exists($image->image_path) && !str_ends_with($image->image_path, '.webp')) {
                try {
                    $optimizedPath = $imageService->optimizeAndStore(
                        file: $disk->path($image->image_path),
                        folder: 'coins',
                        maxWidth: 1600,
                        quality: 85
                    );
                    $disk->delete($image->image_path);
                    $image->update(['image_path' => $optimizedPath]);
                } catch (\Throwable $e) {
                    // Orijinal dosya korunur
                }
            }
        }
    }
}

