<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BobIdController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth']], function () {
    //    Route::get('/session-log/fake', 'SessionLogController@fake');
    Route::get('/sessions/json', 'SessionController@getSessionsJson');

    Route::group(['namespace' => 'Api'], function () {

        Route::get('/sessions/{id}/finish', 'SessionController@finishSession');

        Route::get('/proxies/{id}/restore', 'BobIdController@restore');
        Route::Resource('/proxies', 'ProxyController');

        Route::get('/bob-ids/{id}/restore', [BobIdController::class, 'restore']);
        Route::Resource('/bob-ids', 'BobIdController');
//        Route::post('/bob-ids', [BobIdController::class, 'index']);
        Route::get('/bob-ids/get-by-network-id/{id}', [BobIdController::class, 'getByNetworkId']);

        Route::Resource('/users', 'UserController');

        //BobReports


        Route::group(['namespace' => 'BobReport'], function () {
            Route::get('/bob-reports/create-month', 'CreateMonthController');

            Route::get('/bob-reports/update-bankroll-start', 'BankrollController@updateBankrollStart');
            Route::post('/bob-reports/edit-bankroll', 'BankrollController@editBankroll');
            Route::get('/bob-reports/update-month-bankroll-start', 'BankrollController@updateMonthBankrollStart');

            Route::get('/bob-reports/hands-chart-data', 'HandsGraphController@handsChartData');
            Route::get('/bob-reports/hands-by-day-chart-data', 'HandsGraphController@handsByDayChartData');
            Route::get('/bob-reports/networks-hands-chart-data', 'HandsGraphController@networksHandsChartData');

            Route::get('/bob-reports/profit-chart-data', 'ProfitGraphController@profitChartData');
            Route::get('/bob-reports/networks-profit-chart-data', 'ProfitGraphController@networksProfitChartData');
        });
        // Info from BobReports
        Route::get('/bob-reports-redirect', 'BobReportController@bobReportRedirect');

        // Graphs


        Route::apiResource('/bob-reports', 'BobReportController');

        // Accounts
        //graphs
        Route::get('/accounts/profit-by-months', 'AccountController@profitByMonths');
        Route::get('/accounts/hands-by-months', 'AccountController@handsByMonths');

        Route::get('/accounts/created', 'AccountController@created');
        Route::get('/accounts/for-hm', 'AccountController@forHm');
        Route::get('/accounts/timetable', 'AccountController@timetable');
        Route::get('/accounts/prepare-form-data/{id}', 'AccountController@prepareFormData');
        // resource
        Route::Resource('/accounts', 'AccountController');


        // Helpers
        Route::get('/get-brains-list', 'HelpersController@getBrainsList');
        Route::get('/get-currency-list', 'HelpersController@getCurrencyList');
        Route::get('/get-network-list', 'HelpersController@getNetworkList');
        Route::get('/get-affiliate-list', 'HelpersController@getAffiliateList');
        Route::get('/get-year-list', 'HelpersController@getYearList');
        Route::get('/get-month-list', 'HelpersController@getMonthList');
        Route::get('/get-computer-list', 'HelpersController@getComputerList');
        Route::get('/get-account-list', 'HelpersController@getAccountList');
        Route::get('/get-mobile-room-list', 'HelpersController@getMobileRoomList');
        Route::get('/get-timetable-select-lists', 'HelpersController@getTimeTableSelectLists');

        //MobileAccounts
        Route::get('/get-active-mobile-clubs', 'MobileAccountController@getActiveMobileClubs');
        Route::get('/get-proxies-for-mobile-club', 'MobileAccountController@getProxiesForMobileClub');
        Route::get('/get-active-mobile-accounts', 'MobileAccountController@getActiveMobileAccounts');
        Route::get('/get-banned-mobile-accounts', 'MobileAccountController@getBannedMobileAccounts');
        Route::get('/mobile-accounts/{mobileAccount}/ban', 'MobileAccountController@ban');
        Route::get('/mobile-accounts/{mobileAccount}/unban', 'MobileAccountController@unban');
        Route::apiResource('/mobile-accounts', 'MobileAccountController');


        Route::get('/cashouts/{cashout}/success', 'CashoutController@success');
        Route::Resource('/cashouts', 'CashoutController')->except(['index', 'show']);
        Route::Resource('/deposits', 'DepositController')->except(['index', 'show']);
        Route::apiResource('/people', 'PersonController');
        Route::apiResource('/affiliates', 'AffiliateController');
        Route::put('/networks/toggle-timetable/{id}', 'NetworkController@toggleTimetable');
        Route::apiResource('/networks', 'NetworkController');


        Route::get('/get-proxies-for-room', 'RoomController@getProxiesForRoom');
        Route::Resource('/rooms', 'RoomController');
        Route::apiResource('/proxy-providers', 'ProxyProviderController');
        Route::post('/currencies/store-rate', 'CurrencyController@storeRate');
        Route::get('/currencies/{currency}/create-rate', 'CurrencyController@createRate');
        Route::apiResource('/brains', 'BrainController');
        Route::apiResource('/currencies', 'CurrencyController');
        Route::apiResource('/computers', 'ComputerController');
        Route::apiResource('/notes', 'NoteController');
        Route::apiResource('/contracts', 'ContractController');

        Route::get('/profiles/by-network-id/{id}', 'ProfileController@getByNetworkId');
        Route::get('/profiles/get-all-lists', 'ProfileController@getAllProfilesLists');
        Route::apiResource('/profiles', 'ProfileController');

        Route::apiResource('/mobile-leagues', 'MobileLeagueController');
        Route::Resource('/mobile-clubs', 'MobileClubController');
        Route::Resource('/future-cashouts', 'FutureCashoutController');

        //Pages
        Route::post('/cashier', 'CashierController@index')->name('cashier');


    });
});
