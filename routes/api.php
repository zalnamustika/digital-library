<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::post('/login-demo', function (Request $request) {
    $request->validate(['email'=>'required|email']);

    $user = \App\Models\User::firstOrCreate(
        ['email' => $request->email],
        ['name' => 'Demo User', 'password' => bcrypt('password')]
    );

    $token = $user->createToken('demo')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/loans', [LoanController::class, 'borrow']);
    Route::post('/loans/{id}/return', [LoanController::class, 'returnLoan']);
});

