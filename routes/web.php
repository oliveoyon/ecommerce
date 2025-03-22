<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductMgmtController;


use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome'); // You can replace 'welcome' with your desired view
});


Route::prefix('user')->group(function () {
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [UserLoginController::class, 'login']);
});

Route::prefix('user')->middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/logout', [UserLoginController::class, 'logout'])->name('user.logout');
});

// Admin Authentication Routes
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
});


// Admin Dashboard Route (for authenticated admin users only)

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('category-management', [CategoryController::class, 'categories'])->name('dashboard.categories');
    Route::post('categories', [CategoryController::class, 'categoryAdd'])->name('categories.add');  // Add Category
    Route::put('categories/{category}', [CategoryController::class, 'categoryUpdate'])->name('categories.update');  // Update Category
    Route::delete('categories/{category}', [CategoryController::class, 'categoryDelete'])->name('categories.delete');  // Delete Category

    Route::get('subcategory-management', [CategoryController::class, 'subcategories'])->name('dashboard.subcategories');
    Route::post('subcategories', [CategoryController::class, 'subcategoryAdd'])->name('subcategories.add');  // Add District
    Route::put('subcategories/{subcategory}', [CategoryController::class, 'subcategoryUpdate'])->name('subcategories.update');  // Update District
    Route::delete('subcategories/{subcategory}', [CategoryController::class, 'subcategoryDelete'])->name('subcategories.delete');  // Delete District

    Route::get('unit-management', [CategoryController::class, 'units'])->name('dashboard.units');
    Route::post('units', [CategoryController::class, 'unitAdd'])->name('units.add');  // Add Unit
    Route::put('units/{unit}', [CategoryController::class, 'unitUpdate'])->name('units.update');  // Update Unit
    Route::delete('units/{unit}', [CategoryController::class, 'unitDelete'])->name('units.delete');  // Delete Unit

    Route::get('brand-management', [CategoryController::class, 'brands'])->name('dashboard.brands');
    Route::post('brands', [CategoryController::class, 'brandAdd'])->name('brands.add');  // Add Brand
    Route::put('brands/{brand}', [CategoryController::class, 'brandUpdate'])->name('brands.update');  // Update Brand
    Route::delete('brands/{brand}', [CategoryController::class, 'brandDelete'])->name('brands.delete');  // Delete Brand

    Route::get('color-management', [ProductMgmtController::class, 'colors'])->name('dashboard.colors');
    Route::post('colors', [ProductMgmtController::class, 'colorAdd'])->name('colors.add');  // Add Color
    Route::put('colors/{color}', [ProductMgmtController::class, 'colorUpdate'])->name('colors.update');  // Update Color
    Route::delete('colors/{color}', [ProductMgmtController::class, 'colorDelete'])->name('colors.delete');  // Delete Color

    Route::get('size-management', [ProductMgmtController::class, 'sizes'])->name('dashboard.sizes');
    Route::post('sizes', [ProductMgmtController::class, 'sizeAdd'])->name('sizes.add');  // Add Size
    Route::put('sizes/{size}', [ProductMgmtController::class, 'sizeUpdate'])->name('sizes.update');  // Update Size
    Route::delete('sizes/{size}', [ProductMgmtController::class, 'sizeDelete'])->name('sizes.delete');  // Delete Size

    Route::get('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});






// Profile Route for authenticated users
// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

// Ensure to load other authentication-related routes
// require __DIR__.'/auth.php';
