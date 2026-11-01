<?php

namespace App\Http\Controllers;

use App\Services\WatermarkEngineService;
use Illuminate\Http\Request;

class WatermarkedImageController extends Controller
{
    public function __construct(
        protected WatermarkEngineService $watermarkEngine
    ) {}

    /**
     * Dinamik Filigranlı Sikke Görseli Sunumu.
     * GET /watermark/{path}
     */
    public function show(string $path)
    {
        // Path traversal güvenliği
        if (str_contains($path, '..')) {
            abort(403, 'Geçersiz dosya yolu.');
        }

        return $this->watermarkEngine->renderWatermarkedImage($path);
    }
}

