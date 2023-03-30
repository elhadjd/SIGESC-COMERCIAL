<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Compra\compraController;
use App\Http\Controllers\Config\configController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Faturacao\faturacaoController;
use App\Http\Controllers\Funcionarios\funcionarioController;
use App\Http\Controllers\PDV\AnalisOrderController;
use App\Http\Controllers\PDV\CaixaController;
use App\Http\Controllers\PDV\ListPriceController;
use App\Http\Controllers\PDV\OrdersController;
use App\Http\Controllers\PDV\PointSaleController;
use App\Http\Controllers\PDV\pontoVendaController;
use App\Http\Controllers\public\clientsController;
use App\Http\Controllers\public\movementsController;
use App\Http\Controllers\Public\productsController;
use App\Http\Controllers\public\stockController;
use App\Http\Controllers\Public\suppliersController;
use App\Models\produtos;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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

if (App::environment('local')) {
    URL::forceRootUrl(config('app.url'));
    URL::forceScheme('https');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}


// Route::middleware('license')->group(function () {

Route::get('/databases', function () {
    return ([['name' => 'test'], ['name' => 'Loja2'], ['name' => 'Loja3']]);
});

Route::prefix('auth')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/logar', [LoginController::class, 'login'])->name('logar');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'Dashboard'])->name('dashboard');

    Route::prefix('PDV')->group(function () {
        Route::controller(pontoVendaController::class)->group(function () {
            Route::get('Home', 'Index')->name('pontodevenda');
            Route::get('getOperation','getOperation');
            Route::post('SaveOperation','SaveOperation');
        });

        Route::prefix('caixa')->group(function () {
            Route::controller(CaixaController::class)->group(function () {
                Route::get('buscar', 'get');
                Route::get('getSessions/{caixa}','sessions');
                Route::get('getCaixa/{session}','show');
                Route::post('opiningControl/{session?}','opiningControl');
                Route::post('/clossSession/{session}','clossSession');
                Route::post('updateSession/{session}','updateSession');
            });
        });

        Route::controller(ListPriceController::class)->group(function () {
            Route::post('AddListPrice', 'create');
            Route::put('DeleteListPrice/{id}/{product}','destroy');
            Route::post('updateListPrice/{listPrice}/{product}','update');
        });

        Route::controller(PointSaleController::class)->group(function(){
            Route::get('Pos/{caixa}','index')->name('pos');
            Route::get('menuPos','menuPos');
            Route::get('getTypeOperation','getTypeOperation');
            Route::post('addOperation','addOperation');
            Route::post('PasswordCash/{session}','PasswordCash');
        });

        Route::controller(OrdersController::class)->group(function()
        {
            Route::post('ValidatePayment','ValidatePayment');
            Route::get('getOrders','getOrders');
            Route::put('CancelInvoice/{order}','CancelInvoice');
        });

        Route::controller(AnalisOrderController::class)->group(function () {
            Route::get('getRelatorByMonth/{mouth?}/{year?}','getRelatorByMonth');
            Route::get('analisDay/{day}','analisDay');
            Route::get('IntervalDateRelator/{month?}/{year?}/{inicio}/{fin}', 'IntervalDateRelator');
        });
    });

    Route::prefix('compra')->group(function () {
        Route::controller(compraController::class)->group(function () {
            Route::get('Home', 'Index')->name('compra');
        });
    });

    Route::prefix('config')->group(function () {
        Route::controller(configController::class)->group(function () {
            Route::get('Home', 'Index')->name('configuracoes');
        });
    });


    Route::prefix('Faturacao')->group(function () {
        Route::controller(faturacaoController::class)->group(function () {
            Route::get('Home', 'Index')->name('faturacao');
            Route::get('getInvoices','getInvoices');
            Route::post('NewOrder','NewOrder');
            Route::get('/getItems/{invoice?}','getInvoices');
            Route::post('AddItem/{product}/{invoice}','AddItem');
            Route::delete('deleteItem/{invoice}/{item}','deleteItem');
        });
    });

    Route::prefix('Stock')->group(function () {
        Route::controller(stockController::class)->group(function () {
            Route::get('Home', 'Index')->name('Stock');
            Route::get('GetAgroup','GetAgroup');
            Route::get('getProductAgroup/{type}/{id}','getProductAgroup');
        });
    });

    Route::prefix('Funcionarios')->group(function () {
        Route::controller(funcionarioController::class)->group(function () {
            Route::get('Home', 'Index')->name('funcionarios');
        });
    });

    Route::controller(suppliersController::class)->group(function () {
        Route::get('/supplier', 'get');
        Route::get('/supplier/{supplier}', 'show');
        Route::post('/AddProductSupplier/{product}/{supplier}', 'AddProductSupplier');
        Route::post('/deleteSupplierProduct/{product}/{supplier}', 'deleteSupplierProduct');
    });

    Route::controller(productsController::class)->group(function () {
        Route::get('/products', 'get');
        Route::get('/products/{product}', 'show');
        Route::post('/new_product', 'create');
        Route::post('/update/{product}', 'update');
        Route::post('/saveCategory/{product}/{category?}', 'addCategoryProduct');
    });

    Route::controller(clientsController::class)->group(function () {
        Route::get('clients','get');
    });

    Route::controller(movementsController::class)->group(function(){
        Route::get('/movements/{product}/{movement_type}','get');
    });

    Route::controller(stockController::class)->group(function(){
        Route::get('getArmagens','get');
        Route::post('updateQuantity/{product}','update');
    });


});
// });

// require __DIR__ . '/auth.php';
