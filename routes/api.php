<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\GenericApiController;
use App\Http\Controllers\UploadApiController;

// Public Data Routes
Route::get('/{model}', [GenericApiController::class, 'index']);
Route::get('/{model}/{id}', [GenericApiController::class, 'show']);

// Authentication
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('admin_token')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
});

// Protected Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
    Route::get('/user/me', fn(Request $r) => response()->json($r->user()));

    // All CRUD with file upload support (POST for both create & update to support multipart)
    Route::post('/{model}', [UploadApiController::class, 'store']);
    Route::post('/{model}/{id}', [UploadApiController::class, 'update']);
    Route::delete('/{model}/{id}', [UploadApiController::class, 'destroy']);
});
