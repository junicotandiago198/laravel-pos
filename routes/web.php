<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenditureController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn () => redirect()->route('login'));

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('home');
    })->name('dashboard');
});
Route::group(['middleware' => 'auth'], function () {
    // Data Kategori
    Route::get('category/data', [CategoryController::class, 'data'])->name('category.data');
    Route::resource('category', CategoryController::class);

    // Data Product
    Route::get('product/data', [ProductController::class, 'data'])->name('product.data');
    Route::post('product/delete-selected', [ProductController::class, 'deleteSelected'])->name('product.delete_selected');
    Route::post('product/cetak-barcode', [ProductController::class, 'cetakBarcode'])->name('product.cetak_barcode');
    Route::resource('product', ProductController::class);

    // Data Member
    Route::get('member/data', [MemberController::class, 'data'])->name('member.data');
    Route::post('member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
    Route::resource('member', MemberController::class);

    // Data Supplier
    Route::get('supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::resource('supplier', SupplierController::class);

    // Data Pengeluaran
    Route::get('pengeluaran/data', [ExpenditureController::class, 'data'])->name('pengeluaran.data');
    Route::resource('pengeluaran', ExpenditureController::class);
});
