<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// All other API routes...

// Add your Zoho route here. It will NOT have CSRF protection by default.
Route::post('/zoho/lead-capture', [App\Http\Controllers\Api\RawLeadApiController::class, 'receiveFromZoho']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});