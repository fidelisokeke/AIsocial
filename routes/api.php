<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Post;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by Laravel automatically from bootstrap/app.php
| and are grouped with the "api" middleware group.
|
*/
// Register
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);
     $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
});

// Login
Route::post('/login', function (Request $request) {
    try {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Login error: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred during login.',
        ], 500);
    }
});

// Logout
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
});


//create post API route
Route::middleware('auth:sanctum')->post('/posts', function(Request $request) {
    $validated = $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    $post = Post::create([
        'user_id' => $request->user()->id,
        'content' => $validated['content'],
    ]);

    return response()->json($post);
});


//api to get the feed
Route::middleware('auth:sanctum')->get('/posts', function() {
    $posts = Post::with('user')->latest()->get();
    return response()->json($posts);
});


// 🔒 Protected route example
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
