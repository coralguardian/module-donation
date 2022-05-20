<?php

namespace D4rk0snet\Donation\Enums;

enum PaymentMethod : string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';

    public function getMethodName()
    {
        return match($this) {
            self::CREDIT_CARD => __("CARTE BANCAIRE", "fiscalreceipt"),
            self::BANK_TRANSFER => __("VIREMENT_BANCAIRE", "fiscalreceipt")
        };
    }
}