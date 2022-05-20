<?php

namespace D4rk0snet\Donation\Enums;

enum PaymentMethod : string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
}