<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//CRUD Users
Route::post('/create-user', [UserController::class, 'createUser']);
Route::get('/list-users', [UserController::class, 'listUsers']);

// productos que ha comprado un usuario
Route::get('/get-user-products/{id}', [UserController::class, 'getUserProducts']);


//CRUD Products
Route::post('/create-product', [ProductController::class, 'createProduct']);
Route::get('/list-products', [ProductController::class, 'listProducts']);
Route::delete('/delete-product/{product_id}', [ProductController::class, 'deleteProduct']);
Route::put('/update-product/{product_id}', [ProductController::class, 'updateProduct']);

// usuarios que han comprado un producto espesifico
Route::get('/get-users-by-product/{id}', [ProductController::class, 'getUsersByProduct']);

// New Order
Route::post('/new-order', [ProductController::class, 'createNewOrder']);

// getAllOrders
Route::get('/get-all-orders', [ProductController::class, 'getAllOrders']);

// getProductsByOrder
Route::get('/get-products-by-order/{orderCode}', [ProductController::class, 'getProductsByOrder']);