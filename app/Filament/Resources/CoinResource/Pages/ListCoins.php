<?php

namespace App\Filament\Resources\CoinResource\Pages;

use App\Filament\Resources\CoinResource;
use App\Jobs\ImportCoinsJob;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListCoins extends ListRecords
{
    protected static string $resource = CoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Toplu Veri İçe Aktarma Aksiyonu (CSV/Excel)
            Action::make('import_csv')
                ->label('Toplu İçe Aktar (CSV)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('csv_file')
                        ->label('Sikke Veri Dosyası (CSV)')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->disk('local')
                        ->directory('imports')
                        ->required()
                        ->helperText('Başlık satırı: period, ruler, mint, catalog_reference, title, denomination, metal, weight_g, diameter_mm, obverse_legend, reverse_legend, rarity_grade'),
                ])
                ->action(function (array $data): void {
                    $filePath = Storage::disk('local')->path($data['csv_file']);

                    // Kuyruk (Queue) destekli içe aktarma işini tetikle
                    ImportCoinsJob::dispatch($filePath, auth()->id());

                    Notification::make()
                        ->title('İçe Aktarma Kuyruğa Eklendi')
                        ->body('Sikke listesi arka planda işleniyor. Tamamlandığında bildirim alacaksınız.')
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Yeni Sikke Ekle')
                ->icon('heroicon-o-plus'),
        ];
    }
}

