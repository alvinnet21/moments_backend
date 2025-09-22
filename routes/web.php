<?php

use Illuminate\Support\Facades\Route;

// Swagger UI (static file under public/docs)
Route::get('/docs', function () {
    return redirect('/docs/index.html');
});

Route::get('/docs/openapi.yaml', function () {
    $path = base_path('docs/openapi.yaml');
    if (!file_exists($path)) {
        abort(404);
    }
    // Serve with a broadly compatible YAML content type
    return response()->file($path, ['Content-Type' => 'text/yaml; charset=utf-8']);
});

// Serve company logo for emails/docs
Route::get('/logo.png', function () {
    $path = base_path('logo.png');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'image/png']);
});
