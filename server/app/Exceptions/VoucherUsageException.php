<?php

namespace App\Exceptions;

use App\Models\Voucher;
use Exception;

class VoucherUsageException extends Exception
{
    private Voucher $voucher;

    public function __construct(string $message, Voucher $voucher, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->voucher = $voucher;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }
}
