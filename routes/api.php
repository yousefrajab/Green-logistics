<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ListingApiController;

/*
|--------------------------------------------------------------------------
| API Routes (بوابة المطورين والربط البرمجي لـ جود)
|--------------------------------------------------------------------------
*/

// مسار آمن ومقفل بحارس Sanctum يستقبل طلبات نشر الفائض من كاشير الفنادق مباشرة [1, 8]
Route::middleware('auth:sanctum')->post('/v1/listings', [ListingApiController::class, 'store']);