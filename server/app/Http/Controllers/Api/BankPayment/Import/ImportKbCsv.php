<?php

namespace App\Http\Controllers\Api\BankPayment\Import;

use App\Http\Controllers\Controller;
use App\Models\BankPayment;
use app\Services\BankPayment\BankPaymentInternalTransferService;
use app\Services\BankPayment\Fio\FioBankPaymentService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\Csv\Exception;
use League\Csv\Reader;

class ImportKbCsv extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_account = $workspace->bank_accounts()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $file = $request->file('file');

        // 1) Update balance and synced_at
        $csv = Reader::createFromString($file->getContent())->setDelimiter(';');
        $payment_csv_rows = Collection::make($csv->getRecords());

        $date_to = Carbon::parse($payment_csv_rows[8][1]);

        if ($bank_account->synced_at === null || $bank_account->synced_at < $date_to) {
            $balance = $payment_csv_rows[15][1];
            $balance = Str::replace('+', '', $balance);
            $balance = Str::replace(' ', '', $balance);
            $balance = Str::replace(',', '.', $balance);

            $bank_account->synced_at = $date_to;
            $bank_account->current_amount = $balance;
            $bank_account->save();
        }

        // 2) Import payments
        $csv = Reader::createFromString($file->getContent())->setDelimiter(';');
        $csv->setHeaderOffset(16);

        $payment_csv_rows = Collection::make($csv->getRecords())->slice(15);

        $created_payments_count = 0;
        $found_payments_count = 0;

        $payments = $bank_account->bank_payments;

        foreach ($payment_csv_rows as $payment_csv_row) {
            $searched_payment = $payments->where('external_id', $payment_csv_row['Identifikace transakce'])
                ->first();

            if ($searched_payment === null) {
                $bankPayment = new BankPayment;
                $bankPayment->uuid = Str::uuid();
                $bankPayment->workspace_id = $workspace->id;

                $created_payments_count++;
            } else {
                $bankPayment = $searched_payment;
                $found_payments_count++;
            }

            $amount = Str::replace('+', '', $payment_csv_row['Castka']);
            $amount = Str::replace(' ', '', $amount);
            $amount = Str::replace(',', '.', $amount);

            $sender_comment_arr = [
                @$payment_csv_row['Popis pro prijemce'],
                @$payment_csv_row['AV pole 1'],
                @$payment_csv_row['AV pole 2'],
                @$payment_csv_row['AV pole 3'],
                @$payment_csv_row['AV pole 4'],
            ];

            $sender_comment = implode("\n", $sender_comment_arr);

            $bankPayment->bank_account_id = @$bank_account->id;
            $bankPayment->amount = $amount;
            $bankPayment->type = (float) $amount > 0 ? BankPayment::TYPE_INCOME : BankPayment::TYPE_EXPENSE;
            $bankPayment->description = @$payment_csv_row['Popis prikazce'];
            $bankPayment->sender_comment = @$sender_comment;
            $bankPayment->external_id = $payment_csv_row['Identifikace transakce'];
            $bankPayment->issued_at = Carbon::parse($payment_csv_row['Datum splatnosti']);
            $bankPayment->variable_symbol = @$payment_csv_row['VS'];

            $counter_account_array = explode('/', $payment_csv_row['Protiucet/Kod banky']);

            $bankPayment->counter_account_number = $counter_account_array[0];

            if ($bankPayment->counter_account_number === '-') {
                $bankPayment->counter_account_number = null;
            }

            $bankPayment->counter_bank_number = $counter_account_array[1];
            $bankPayment->save();

            $bankPayment->syncOrCreateCounterBankAccount();
        }

        (new BankPaymentInternalTransferService())->findAndUpdateBankPaymentTransfers($bank_account);

        return response([
            'created_payments_count' => $created_payments_count,
            'found_payments_count' => $found_payments_count,
        ]);
    }
}
