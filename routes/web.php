<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Compra\chartPurchase;
use App\Http\Controllers\Compra\compraController;
use App\Http\Controllers\Config\configController;
use App\Http\Controllers\Config\emailVerifyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Faturacao\chartInvoice;
use App\Http\Controllers\Faturacao\faturacaoController;
use App\Http\Controllers\Funcionarios\funcionarioController;
use App\Http\Controllers\PDV\AnalisOrderController;
use App\Http\Controllers\PDV\CaixaController;
use App\Http\Controllers\PDV\chartPdvController;
use App\Http\Controllers\PDV\ListPriceController;
use App\Http\Controllers\PDV\OrdersController;
use App\Http\Controllers\PDV\PointSaleController;
use App\Http\Controllers\PDV\pontoVendaController;
use App\Http\Controllers\Public\clientsController;
use App\Http\Controllers\Public\MethodsPaymentController;
use App\Http\Controllers\Public\movementsController;
use App\Http\Controllers\Public\productsController;
use App\Http\Controllers\Public\searchController;
use App\Http\Controllers\Public\stockController;
use App\Http\Controllers\Public\suppliersController;
use App\Http\Controllers\Public\TransferController;
use App\Http\Controllers\StartController;
use App\Models\company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::controller(StartController::class)->group(function () {
    Route::get('gettingStarted', 'index')->name('startCompany');
    Route::post('saveCompany', 'saveCompany');
    Route::get('welcome/{company}', 'welcome')->name('welcome');
    Route::post('activeLicense', 'activeLicense');
});

Route::prefix('auth')->group(function () {
    Route::post('/saveCompany', [LoginController::class, 'saveCompany']);
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/logar', [LoginController::class, 'login'])->name('logar');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});

Route::get('/databases', function () {
    return ([['name' => 'test'], ['name' => 'Loja2'], ['name' => 'Loja3']]);
});

Route::middleware('auth')->group(function () {
    Route::get('LicenseBlocked', function () {
        return Inertia::render('LicenseBlocked', [
            'license' => company::find(Auth::user()->company_id)->license()->first()
        ]);
    })->name('LicenseBlocked');

    Route::middleware('license')->group(function () {
        Route::get('/', [DashboardController::class, 'Dashboard'])->name('dashboard');
        Route::controller(searchController::class)->group(function () {
            Route::get('search/{table}/{column}/{tex}', 'filter');
        });

        Route::prefix('config')->group(function () {
            Route::controller(configController::class)->group(function () {
                Route::get('Home', 'Index')->name('configuracoes');
                Route::get('getConfig', 'getConfig');
                Route::post('newUser', 'newUser');
                Route::get('users', 'users');
                Route::post('SaveUser/{user?}', 'SaveUser');
                Route::post('saveCompany', 'saveCompany');
                Route::get('getLoginRegister','getLoginRegister');
            });
        });

        Route::middleware('currencyCompany')->group(function(){
            Route::prefix('PDV')->group(function () {
                Route::controller(pontoVendaController::class)->group(function () {
                    Route::get('Home', 'Index')->name('pontodevenda');
                    Route::get('getOperation', 'getOperation');
                    Route::post('SaveOperation', 'SaveOperation');
                });

                Route::prefix('caixa')->group(function () {
                    Route::controller(CaixaController::class)->group(function () {
                        Route::get('buscar', 'get');
                        Route::get('getSessions/{caixa}', 'sessions');
                        Route::get('getCaixa/{session}', 'show');
                        Route::post('opiningControl/{session?}', 'opiningControl');
                        Route::post('/clossSession/{session}', 'clossSession');
                        Route::post('updateSession/{session}', 'updateSession');
                        Route::post('savePoint/{caixa?}','savePoint');
                        Route::delete('/deleteCash/{caixa}','deleteCash');
                    });
                });

                Route::controller(PointSaleController::class)->group(function () {
                    Route::get('Pos/{caixa}', 'index')->name('pos');
                    Route::get('menuPos', 'menuPos');
                    Route::get('getTypeOperation', 'getTypeOperation');
                    Route::post('addOperation', 'addOperation');
                    Route::post('PasswordCash/{session}', 'PasswordCash');
                    Route::post('get/CancelInvoice/{user}/{invoice}', 'CancelInvoice');
                    Route::get('getUsersAuthorized', 'getUsersAuthorized');
                });

                Route::controller(OrdersController::class)->group(function () {
                    Route::get('getOrderSingleUser/{session}', 'getOrderSingleUser');
                    Route::post('ValidatePayment', 'ValidatePayment');
                    Route::get('getOrders/{order?}/{column?}', 'getOrders');
                    Route::put('CancelInvoice/{order}', 'CancelInvoice');
                    Route::get('printInvoice/{session}/{type}','printInvoice');
                    Route::get('groupBy/{event}/{column}','groupBy');
                    Route::post('/checkInvoice','checkInvoiceDupleDelete');
                });

                Route::controller(AnalisOrderController::class)->group(function () {
                    Route::get('getRelatorByMonth/{mouth?}/{year?}', 'getRelatorByMonth');
                    Route::get('analisDay/{day}', 'analisDay');
                    Route::get('IntervalDateRelator/{month?}/{year?}/{inicio}/{fin}', 'IntervalDateRelator');
                });

                Route::prefix('analis')->group(function(){
                    Route::controller(chartPdvController::class)->group(function(){
                        Route::get('/','index');
                        Route::get('/user','users');
                        Route::get('/state','state');
                        Route::get('/data/{type}','data');
                        Route::get('/dataSub/{type}/{start}/{end}','data');
                    });
                });
            });

            Route::prefix('compra')->group(function () {
                Route::controller(compraController::class)->group(function () {
                    Route::get('Home', 'Index')->name('compra');
                    Route::get('getPurchases', 'getPurchases');
                    Route::get('getPurchases/{order}', 'Order');
                    Route::post('NewPurchase', 'NewPurchase');
                    Route::post('ChangeDatePurchase/{type}/{order}', 'ChangeDatePurchase');
                    Route::post('AddItemPuchase/{product}/{order}', 'AddItemPuchase');
                    Route::post('UpdateItems/{item}', 'UpdateItems');
                    Route::post('addSupplier/{order}/{supplier}', 'addSupplier');
                    Route::delete('/deleteItem/{order}/{item}', 'deleteItem');
                    Route::post('confirmOrder/{order}/{type}', 'confirmOrder');
                    Route::post('cancelInvoice/{order}/{type}', 'confirmOrder');
                    Route::post('sendPayment/{order}', 'savePayment');
                    Route::get('/purchur/getPayments', 'getPayments');
                    Route::get('getPayments/{order}', 'payments');
                });

                Route::prefix('analis')->group(function(){
                    Route::controller(chartPurchase::class)->group(function(){
                        Route::get('/','index');
                        Route::get('/user','users');
                        Route::get('/state','state');
                        Route::get('/data/{type}','data');
                        Route::get('/dataSub/{type}/{start}/{end}','data');
                    });
                });
            });

            Route::controller(emailVerifyController::class)->group(function(){
                Route::post('sendCodeVerifyEmail/{model}','sendCodeVerifyEmail');
                Route::post('validateCode/{company}/{code}','validateCode');
            });

            Route::controller(configController::class)->group(function () {
                Route::post('/UpdatePassword/{user}', 'UpdatePassword');
            });

            Route::prefix('Faturacao')->group(function () {
                Route::controller(faturacaoController::class)->group(function () {
                    Route::get('Home', 'Index')->name('faturacao');
                    Route::get('getInvoices', 'getInvoices');
                    Route::post('NewOrder/{name?}', 'NewOrder');
                    Route::post('addClient/{order}/{client}', 'addClient');
                    Route::get('/getItems/{invoice?}', 'getInvoices');
                    Route::post('AddItem/{product}/{invoice}', 'AddItem');
                    Route::delete('deleteItem/{invoice}/{item}', 'deleteItem');
                    Route::post('/UpdateRows/{invoice}', 'UpdateRows');
                    Route::post('/ChangeDateInvoice/{type}/{invoice}', 'ChangeDateInvoice');
                    Route::post('/ConfirmOrder/{invoice}/{type}', 'ConfirmOrder');
                    Route::post('sendPayment/{invoice}', 'InvoicePayment');
                    Route::post('cancelInvoice/{invoice}/{type}', 'ConfirmOrder');
                    Route::get('getPayments/{invoice}', 'getPayments');
                });

                Route::prefix('analis')->group(function(){
                    Route::controller(chartInvoice::class)->group(function(){
                        Route::get('/','index');
                        Route::get('/user','users');
                        Route::get('/state','state');
                        Route::get('/data/{type}','data');
                        Route::get('/dataSub/{type}/{start}/{end}','data');
                    });
                });
            });

            Route::controller(ListPriceController::class)->group(function () {
                Route::post('AddListPrice', 'create');
                Route::put('DeleteListPrice/{id}/{product}', 'destroy');
                Route::post('updateListPrice/{listPrice}/{product}', 'update');
            });

            Route::prefix('Stock')->group(function () {

                Route::controller(stockController::class)->group(function () {
                    Route::get('Home', 'Index')->name('Stock');
                    Route::get('GetAgroup', 'GetAgroup');
                    Route::get('getProductAgroup/{type}/{id}', 'getProductAgroup');
                    Route::get('getRelatorProductsByMonth/{month}/{year}', 'getRelatorProductsByMonth');
                    Route::get('IntervalDateInventory/{month}/{year}/{from}/{to}', 'IntervalDateInventory');
                    Route::get('getCatalog', 'getCatalog');
                    Route::post('saveStore', 'saveStore');
                });

                Route::controller(TransferController::class)->group(function () {
                    Route::get('getTransfers', 'get');
                    Route::post('newTransfer', 'create');
                    Route::get('selectOrder/{order}', 'show');
                    Route::post('addItems/{product}/{order}', 'addItem');
                    Route::post('updateTransfer/{item}', 'update');
                    Route::delete('deleteItem/{order}/{item}', 'DeleteItem');
                    Route::post('addLocalDestine/{order}/{store}', 'addLocalDestine');
                    Route::post('addDateOrder/{type}/{order}', 'addDateOrder');
                    Route::post('saveTransfer/{order}/{type}', 'saveTransfer');
                    Route::post('cancelTransfer/{order}/{type}', 'saveTransfer');
                });
            });

            Route::prefix('Funcionarios')->group(function () {
                Route::controller(funcionarioController::class)->group(function () {
                    Route::get('Home', 'Index')->name('funcionarios');
                    Route::get('getWorkers', 'get');
                    Route::get('getDepartments', 'departments');
                    Route::post('saverWorker/{worker}', 'saverWorker');
                    Route::post('newWorker', 'newWorker');
                    Route::post('/saveDepartment', 'saveDepartment');
                    Route::get('AnalisWorker/{month}', 'AnalisWorker');
                    Route::post('saveExpense', 'saveExpense');
                });
            });

            Route::controller(suppliersController::class)->group(function () {
                Route::get('/suppliers', 'get');
                Route::post('/NewSupplier', 'create');
                Route::get('/suppliers/{supplier}', 'show');
                Route::post('/supplier/update/{supplier}', 'update');
                Route::post('/AddProductSupplier/{product}/{supplier}', 'AddProductSupplier');
                Route::post('/deleteSupplierProduct/{product}/{supplier}', 'deleteSupplierProduct');
                Route::post('/deleteSupplier/{supplier}', 'deleteSupplier');
                Route::get('suppliers/relations/{supplier}', 'relations');
            });

            Route::controller(productsController::class)->group(function () {
                Route::get('/products/{page}/{type}', 'get');
                Route::get('/products/{product}', 'show');
                Route::post('/new_product/{name?}', 'create');
                Route::post('/update/{product}', 'update');
                Route::post('/saveCategory/{product}/{category?}', 'addCategoryProduct');
                Route::delete('/deleteProduct/{product}', 'deleteProduct');
                Route::post('/uploadImageCatalog/{product}','uploadImageCatalog');
                Route::delete('/deleteImageInCatalogProduct/{image}','deleteImageInCatalog');
                Route::post('/publishProduct/{product}','publishProduct');
            });

            Route::controller(clientsController::class)->group(function () {
                Route::post('createClient', 'createClient');
                Route::get('clients', 'get');
                Route::get('clients/credit', 'getClientsCredit');
                Route::get('clientOrders/{client}', 'clientOrders');
                Route::post('updateClient/{client}', 'updateClient');
                Route::post('deleteClient/{client}', 'delete');
            });

            Route::controller(movementsController::class)->group(function () {
                Route::get('/movements/{product}/{movement_type}', 'get');
            });

            Route::controller(stockController::class)->group(function () {
                Route::get('getArmagens', 'get');
                Route::post('updateQuantity/{product}', 'update');
            });

            Route::controller(MethodsPaymentController::class)->group(function () {
                Route::get('getMethods', 'getMethods');
                Route::get('getPayments/{invoice}', 'getPayments');
                Route::get('getPayments', 'getPaymentOrders');
            });
        });
    });
});
Route::get('getActivity', [configController::class, 'getActivity']);
