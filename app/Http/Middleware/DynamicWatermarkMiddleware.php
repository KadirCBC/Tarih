<?php

namespace App\Http\Middleware;

use App\Services\WatermarkEngineService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicWatermarkMiddleware
{
    public function __construct(
        protected WatermarkEngineService $watermarkEngine
    ) {}

    /**
     * Görsel yanıtlarını yakalayarak orijinal dosyayı bozmadan dinamik olarak filigran basar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Eğer istek bir resim yoluna yapılmışsa veya görsel teslim ediliyorsa
        $path = $request->route('path') ?? $request->query('image');

        if ($path && ($request->is('*storage/collections/*') || $request->is('*images/coins/*') || $request->is('*watermark/*'))) {
            return $this->watermarkEngine->renderWatermarkedImage($path);
        }

        return $response;
    }
}

