
<?php

use App\Http\Controllers\api\template\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/lang/{locale}', [ApplicationController::class, 'changeLocale']);
    Route::get('/system-font/{direction}', [ApplicationController::class, "font"]);
    Route::get('/contries', [ApplicationController::class, "contries"]);
    Route::get('/provinces', [ApplicationController::class, "provinces"]);
    Route::get('/districts', [ApplicationController::class, "districts"]);
});
