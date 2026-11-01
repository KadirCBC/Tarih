<?php

namespace App\Filament\Resources;

use App\Enums\CoinMetal;
use App\Enums\ConditionGrade;
use App\Enums\ImageType;
use App\Enums\RarityGrade;
use App\Filament\Resources\CoinResource\Pages;
use App\Models\Coin;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CoinResource extends Resource
{
    protected static ?string $model = Coin::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationLabel = 'Sikkeler';
    protected static ?string $modelLabel = 'Sikke';
    protected static ?string $pluralModelLabel = 'Sikke Kataloğu';
    protected static ?string $navigationGroup = 'Nümizmatik Katalog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Temel Künye ve Taksonomi
                Section::make('Temel Nümizmatik Bilgileri')
                    ->description('Sikkenin başlığı, katalog referansı ve bağımlı dönem/hükümdar seçimleri.')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('title')
                                ->label('Sikke Başlığı')
                                ->placeholder('Örn: Trajan Gümüş Denarius - Arab Adquis')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                            TextInput::make('catalog_reference')
                                ->label('Katalog No (RIC / BMC / Sear)')
                                ->placeholder('Örn: RIC II 142; RSC 26')
                                ->required()
                                ->maxLength(255),
                        ]),

                        Grid::make(3)->schema([
                            // Bağımlı Seçim: Dönem
                            Select::make('period_id')
                                ->label('Tarihsel Dönem')
                                ->relationship('period', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('ruler_id', null))
                                ->required(),

                            // Bağımlı Seçim: Hükümdar (Döneme göre filtrelenir)
                            Select::make('ruler_id')
                                ->label('Hükümdar / Otorite')
                                ->relationship(
                                    name: 'ruler',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query, Get $get) => $query->when(
                                        $get('period_id'),
                                        fn ($q, $periodId) => $q->where('period_id', $periodId)
                                    ),
                                )
                                ->searchable()
                                ->preload()
                                ->helperText('Dönem seçildiğinde hükümdarlar filtrelenir.'),

                            // Darphane Seçimi
                            Select::make('mint_id')
                                ->label('Antik Darphane')
                                ->relationship('mint', 'name')
                                ->searchable()
                                ->preload(),
                        ]),

                        Grid::make(4)->schema([
                            TextInput::make('denomination')
                                ->label('Nominal Birim')
                                ->placeholder('Örn: Denarius, Tetradrahmi')
                                ->required(),

                            Select::make('metal')
                                ->label('Maden Türü')
                                ->options(CoinMetal::class)
                                ->required(),

                            TextInput::make('weight_g')
                                ->label('Ağırlık (Gram)')
                                ->numeric()
                                ->suffix('g'),

                            TextInput::make('diameter_mm')
                                ->label('Çap (Milimetre)')
                                ->numeric()
                                ->suffix('mm'),
                        ]),

                        Select::make('rarity_grade')
                            ->label('Nadirlik Derecesi')
                            ->options(RarityGrade::class)
                            ->default(RarityGrade::C)
                            ->required(),
                    ]),

                // 2. Lejant ve Epigrafi Alanları
                Section::make('Lejant ve İkonografi Çözümlemeleri')
                    ->description('Sikkenin avers ve revers orijinal kitabeleri ve Türkçe akademik çevirileri.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            // Ön Yüz (Avers)
                            Section::make('Ön Yüz (Avers)')
                                ->schema([
                                    TextInput::make('obverse_legend')
                                        ->label('Orijinal Sikke Yazıtı (Lejant)')
                                        ->placeholder('Örn: IMP CAES NERVA TRAIAN AVG GERM')
                                        ->extraInputAttributes(['style' => 'font-family: serif; letter-spacing: 1px;']),

                                    Textarea::make('obverse_legend_trans')
                                        ->label('Lejant Türkçe Açıklaması / Tercümesi')
                                        ->placeholder('Örn: Başkomutan Sezar Nerva Traianus Augustus, Cermen Fatihi.')
                                        ->rows(2),

                                    Textarea::make('obverse_desc')
                                        ->label('Ön Yüz İkonografik Tasviri')
                                        ->placeholder('Örn: Defne çelenkli Trajan büstü sağa bakar.')
                                        ->rows(2),
                                ]),

                            // Arka Yüz (Revers)
                            Section::make('Arka Yüz (Revers)')
                                ->schema([
                                    TextInput::make('reverse_legend')
                                        ->label('Orijinal Sikke Yazıtı (Lejant)')
                                        ->placeholder('Örn: ARAB ADQ COS V P P S P Q R OPTIMO PRINC')
                                        ->extraInputAttributes(['style' => 'font-family: serif; letter-spacing: 1px;']),

                                    Textarea::make('reverse_legend_trans')
                                        ->label('Lejant Türkçe Açıklaması / Tercümesi')
                                        ->placeholder('Örn: Arabistan Ele Geçirildi. Senato ve Roma Halkından En Kusursuz Öndere.')
                                        ->rows(2),

                                    Textarea::make('reverse_desc')
                                        ->label('Arka Yüz İkonografik Tasviri')
                                        ->placeholder('Örn: Ayakta duran Arabia personifikasyonu, sol alanda deve.')
                                        ->rows(2),
                                ]),
                        ]),
                    ]),

                // 3. Çoklu Görsel Yükleme (WebP Optimize)
                Section::make('Sikke Fotoğrafları')
                    ->description('Ön yüz, arka yüz ve sertifikalı kapsül görselleri.')
                    ->collapsible()
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Fotoğraf Dosyası')
                                    ->disk('public')
                                    ->directory('coins')
                                    ->image()
                                    ->imageEditor()
                                    ->required(),

                                Select::make('image_type')
                                    ->label('Görsel Türü')
                                    ->options(ImageType::class)
                                    ->default(ImageType::BOTH)
                                    ->required(),

                                Toggle::make('is_primary')
                                    ->label('Kapak / Ana Görsel')
                                    ->default(false),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['image_type'] ?? 'Görsel')
                            ->defaultItems(1),
                    ]),

                // 4. Kondisyon ve Müzayede Değer Matrisi (Repeater Tablosu)
                Section::make('Kondisyon ve Piyasa Değer Matrisi')
                    ->description('Poor, Fine, VF, XF ve UNC kondisyonları için taban ve tavan USD değerleri.')
                    ->collapsible()
                    ->schema([
                        Repeater::make('valuations')
                            ->relationship('valuations')
                            ->schema([
                                Select::make('condition_grade')
                                    ->label('Kondisyon')
                                    ->options(ConditionGrade::class)
                                    ->required(),

                                TextInput::make('min_value_usd')
                                    ->label('Taban Değer ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),

                                TextInput::make('max_value_usd')
                                    ->label('Tavan Değer ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),

                                DatePicker::make('last_updated_at')
                                    ->label('Son Güncelleme')
                                    ->default(now())
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('+ Kondisyon Değeri Ekle'),
                    ]),

                // 5. Tarihsel ve Nümizmatik Notlar
                Section::make('Tarihsel ve Nümizmatik Açıklamalar')
                    ->collapsible()
                    ->schema([
                        RichEditor::make('historical_notes')
                            ->label('Akademik Açıklamalar ve Darb Koşulları')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryImage.url')
                    ->label('Kapak')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-coin.png')),

                TextColumn::make('catalog_reference')
                    ->label('Katalog No')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('monospace')
                    ->weight('bold'),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('period.name')
                    ->label('Dönem')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ruler.name')
                    ->label('Hükümdar')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('denomination')
                    ->label('Birim')
                    ->sortable(),

                BadgeColumn::make('metal')
                    ->label('Maden')
                    ->colors([
                        'warning' => CoinMetal::GOLD->value,
                        'secondary' => CoinMetal::SILVER->value,
                        'danger' => CoinMetal::BRONZE->value,
                    ]),

                BadgeColumn::make('rarity_grade')
                    ->label('Nadirlik')
                    ->colors([
                        'success' => RarityGrade::C->value,
                        'info' => RarityGrade::R1->value,
                        'primary' => RarityGrade::R2->value,
                        'warning' => RarityGrade::R3->value,
                        'danger' => [RarityGrade::R4->value, RarityGrade::R5->value],
                    ]),

                TextColumn::make('view_count')
                    ->label('Görüntülenme')
                    ->sortable()
                    ->numeric(),

                TextColumn::make('updated_at')
                    ->label('Son Düzenleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->label('Dönem')
                    ->relationship('period', 'name'),

                SelectFilter::make('ruler_id')
                    ->label('Hükümdar')
                    ->relationship('ruler', 'name'),

                SelectFilter::make('metal')
                    ->label('Maden')
                    ->options(CoinMetal::class),

                SelectFilter::make('rarity_grade')
                    ->label('Nadirlik')
                    ->options(RarityGrade::class),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoins::route('/'),
            'create' => Pages\CreateCoin::route('/create'),
            'edit' => Pages\EditCoin::route('/{record}/edit'),
        ];
    }
}

