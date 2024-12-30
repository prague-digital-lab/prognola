<?php

use App\Http\Controllers\Api\Auth\Login;
use App\Http\Controllers\Api\Auth\Logout;
use App\Http\Controllers\Api\Auth\Register;
use App\Http\Controllers\Api\Auth\ResendVerificationEmail;
use App\Http\Controllers\Api\BankAccount\BankAccountDestroy;
use App\Http\Controllers\Api\BankAccount\BankAccountIndex;
use App\Http\Controllers\Api\BankAccount\BankAccountShow;
use App\Http\Controllers\Api\BankAccount\BankAccountStore;
use App\Http\Controllers\Api\BankAccount\BankAccountSyncFio;
use App\Http\Controllers\Api\BankAccount\BankAccountSyncMoneta;
use App\Http\Controllers\Api\BankAccount\BankAccountUpdate;
use App\Http\Controllers\Api\BankPayment\BankPaymentDestroy;
use App\Http\Controllers\Api\BankPayment\BankPaymentIndex;
use App\Http\Controllers\Api\BankPayment\BankPaymentShow;
use App\Http\Controllers\Api\BankPayment\BankPaymentStore;
use App\Http\Controllers\Api\BankPayment\BankPaymentUpdate;
use App\Http\Controllers\Api\BankPayment\Import\ImportKbCsv;
use App\Http\Controllers\Api\Bootstrap\BootstrapWorkspace;
use App\Http\Controllers\Api\CounterBankAccount\CounterBankAccountIndex;
use App\Http\Controllers\Api\CounterBankAccount\CounterBankAccountShow;
use App\Http\Controllers\Api\CounterBankAccount\CounterBankAccountUpdate;
use App\Http\Controllers\Api\Expense\ExpenseDestroy;
use App\Http\Controllers\Api\Expense\ExpenseIndex;
use App\Http\Controllers\Api\Expense\ExpensePairWithBankPayment;
use App\Http\Controllers\Api\Expense\ExpenseScanStore;
use App\Http\Controllers\Api\Expense\ExpenseShow;
use App\Http\Controllers\Api\Expense\ExpenseStore;
use App\Http\Controllers\Api\Expense\ExpenseUnpairBankPayment;
use App\Http\Controllers\Api\Expense\ExpenseUpdate;
use App\Http\Controllers\Api\ExpenseCategory\ExpenseCategoryIndex;
use App\Http\Controllers\Api\ExportExpensesScans;
use App\Http\Controllers\Api\ExportExpensesScansUrl;
use App\Http\Controllers\Api\Finance\Cashflow;
use App\Http\Controllers\Api\Finance\StatsIncome;
use App\Http\Controllers\Api\Finance\TwilioIncomingDocument;
use App\Http\Controllers\Api\Income\IncomeDestroy;
use App\Http\Controllers\Api\Income\IncomeIndex;
use App\Http\Controllers\Api\Income\IncomePairWithBankPayment;
use App\Http\Controllers\Api\Income\IncomeShow;
use App\Http\Controllers\Api\Income\IncomeStore;
use App\Http\Controllers\Api\Income\IncomeUnpairBankPayment;
use App\Http\Controllers\Api\Income\IncomeUpdate;
use App\Http\Controllers\Api\IncomeCategory\IncomeCategoryIndex;
use App\Http\Controllers\Api\Organisation\OrganisationIndex;
use App\Http\Controllers\Api\Organisation\OrganisationShow;
use App\Http\Controllers\Api\Organisation\OrganisationStore;
use App\Http\Controllers\Api\Organisation\OrganisationUpdate;
use App\Http\Controllers\Api\Scan\ScanDestroy;
use App\Http\Controllers\Api\Scan\ScanIndex;
use App\Http\Controllers\Api\Scan\ScanStore;
use App\Http\Controllers\Api\Scan\ScanUpdate;
use App\Http\Controllers\Api\User\UserUpdate;
use App\Http\Controllers\Api\Workspace\RefreshInboxEmail;
use App\Http\Controllers\Api\Workspace\User\UserIndex;
use App\Http\Controllers\Api\Workspace\User\UserInvite;
use App\Http\Controllers\Api\Workspace\User\UserJoinWorkspace;
use App\Http\Controllers\Api\Workspace\User\UserRevokeInvite;
use App\Http\Controllers\Api\Workspace\WorkspaceDiscordSettingsTest;
use App\Http\Controllers\Api\Workspace\WorkspaceIndex;
use App\Http\Controllers\Api\Workspace\WorkspaceShow;
use App\Http\Controllers\Api\Workspace\WorkspaceStore;
use App\Http\Controllers\Api\Workspace\WorkspaceUpdateDiscordSettings;
use App\Http\Controllers\Api\Workspace\WorkspaceUpdateEmailSettings;
use App\Http\Controllers\PersonalAccessToken\PersonalAccessTokenDestroy;
use App\Http\Controllers\PersonalAccessToken\PersonalAccessTokenIndex;
use App\Http\Controllers\PersonalAccessToken\PersonalAccessTokenStore;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::post('/login', Login::class);
Route::post('/register', Register::class);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/email/verify/resend', ResendVerificationEmail::class);

    // User email verification
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

        $request->fulfill();

        return response()->json([
            'message' => 'User email verified.',
        ]);

    })
        ->middleware(['signed'])
        ->name('verification.verify');

    // User
    Route::get('/user', function (Request $request) {
        return Auth::user()->append('profile_photo_url');
    });

    Route::patch('/user', UserUpdate::class);

    Route::post('/logout', Logout::class);

    // Personal access tokens
    Route::get('/personal_access_tokens', PersonalAccessTokenIndex::class);
    Route::post('/personal_access_tokens', PersonalAccessTokenStore::class);
    Route::delete('/personal_access_tokens/{uuid}', PersonalAccessTokenDestroy::class);

    // Bootstrap data
    Route::get('/{workspace}/bootstrap', BootstrapWorkspace::class);

    // Stats
    Route::get('/{workspace}/stats/cashflow', Cashflow::class);
    Route::get('/{workspace}/stats/income', StatsIncome::class);

    // Expenses
    Route::get('/{workspace}/expenses', ExpenseIndex::class);
    Route::post('/{workspace}/expenses', ExpenseStore::class);
    Route::get('/{workspace}/expenses/{uuid}', ExpenseShow::class);
    Route::patch('/{workspace}/expenses/{uuid}', ExpenseUpdate::class);
    Route::delete('/{workspace}/expenses/{uuid}', ExpenseDestroy::class);

    Route::post('/{workspace}/expenses/{uuid}/bank_payments', ExpensePairWithBankPayment::class);
    Route::delete('{workspace}/expenses/{uuid}/bank_payments', ExpenseUnpairBankPayment::class);

    Route::post('{workspace}/expenses/{uuid}/scans', ExpenseScanStore::class);

    // Income category
    Route::get('/{workspace}/income_categories', IncomeCategoryIndex::class);

    // Expense category
    Route::get('/{workspace}/expense_categories', ExpenseCategoryIndex::class);

    // Incomes
    Route::get('/{workspace}/incomes', IncomeIndex::class);
    Route::post('/{workspace}/incomes', IncomeStore::class);
    Route::get('/{workspace}/incomes/{uuid}', IncomeShow::class);
    Route::patch('/{workspace}/incomes/{uuid}', IncomeUpdate::class);
    Route::delete('/{workspace}/incomes/{uuid}', IncomeDestroy::class);

    Route::post('/{workspace}/incomes/{uuid}/bank_payments', IncomePairWithBankPayment::class);
    Route::delete('/{workspace}/incomes/{uuid}/bank_payments', IncomeUnpairBankPayment::class);

    // Scans
    Route::get('/{workspace}/scans', ScanIndex::class);
    Route::post('/{workspace}/scans', ScanStore::class);
    //    Route::get('/{workspace}/scans/{uuid}', ScanShow::class);
    Route::patch('/{workspace}/scans/{uuid}', ScanUpdate::class);
    Route::delete('/{workspace}/scans/{uuid}', ScanDestroy::class);

    // Bank accounts
    Route::get('/{workspace}/bank_accounts', BankAccountIndex::class);
    Route::post('/{workspace}/bank_accounts', BankAccountStore::class);
    Route::get('/{workspace}/bank_accounts/{uuid}', BankAccountShow::class);
    Route::patch('/{workspace}/bank_accounts/{uuid}', BankAccountUpdate::class);
    Route::delete('/{workspace}/bank_accounts/{uuid}', BankAccountDestroy::class);
    Route::post('/{workspace}/bank_accounts/{uuid}/sync-fio', BankAccountSyncFio::class);
    Route::post('/{workspace}/bank_accounts/{uuid}/sync-moneta', BankAccountSyncMoneta::class);

    // Import bank payments
    Route::post('/{workspace}/bank_accounts/{uuid}/import/kb-csv', ImportKbCsv::class);

    // Bank payments
    Route::get('/{workspace}/bank_payments', BankPaymentIndex::class);
    Route::post('/{workspace}/bank_payments', BankPaymentStore::class);
    Route::get('/{workspace}/bank_payments/{uuid}', BankPaymentShow::class);
    Route::patch('/{workspace}/bank_payments/{uuid}', BankPaymentUpdate::class);
    Route::delete('/{workspace}/bank_payments/{uuid}', BankPaymentDestroy::class);

    // Counter bank accounts
    Route::get('/{workspace}/counter_bank_accounts', CounterBankAccountIndex::class);
    Route::get('/{workspace}/counter_bank_accounts/{uuid}', CounterBankAccountShow::class);
    Route::patch('/{workspace}/counter_bank_accounts/{uuid}', CounterBankAccountUpdate::class);

    // Organisations
    Route::get('/{workspace}/organisations', OrganisationIndex::class);
    Route::post('/{workspace}/organisations', OrganisationStore::class);
    Route::get('/{workspace}/organisations/{uuid}', OrganisationShow::class);
    Route::patch('/{workspace}/organisations/{uuid}', OrganisationUpdate::class);
    //    Route::delete('/organisations/{id}', OrganisationDestroy::class);

    // Workspaces
    Route::get('/workspaces', WorkspaceIndex::class);
    Route::post('/workspaces', WorkspaceStore::class);
    Route::get('/workspaces/{workspace}', WorkspaceShow::class);
    Route::patch('/workspaces/{workspace}/inbox-settings', WorkspaceUpdateEmailSettings::class);
    Route::post('/workspaces/{workspace}/refresh-email', RefreshInboxEmail::class);

    Route::patch('/workspaces/{workspace}/discord-settings', WorkspaceUpdateDiscordSettings::class);
    Route::post('/workspaces/{workspace}/discord-settings/test', WorkspaceDiscordSettingsTest::class);

    Route::post('/{workspace}/accept-invite', UserJoinWorkspace::class);

    // Workspace members settings
    Route::get('/{workspace}/users', UserIndex::class);
    Route::post('/{workspace}/users', UserInvite::class);
    Route::patch('/{workspace}/users/{uuid}/revoke-invite', UserRevokeInvite::class);
    Route::patch('/{workspace}/users', \App\Http\Controllers\Api\Workspace\User\UserUpdate::class);

    Route::get('/{workspace}/download/expenses/scans/url', ExportExpensesScansUrl::class);
});

// Finance document inbox email upload
Route::post('/twilio/incoming-document/z1828377-7239-4bc4-8eb7-1d32e49303ef', TwilioIncomingDocument::class);

// Export (Signed route)
Route::get('/{workspace}/download/expenses/scans/zip', ExportExpensesScans::class)->name('scans.download');
