<?php

namespace D4rk0snet\Donation\Enums;

enum DonationRecurrencyEnum : string
{
    case MONTHLY = 'monthly';
    case ONESHOT = 'oneshot';

    public function getStripeProductId()
    {
        return match($this) {
            DonationRecurrencyEnum::MONTHLY => 'xxx',
            DonationRecurrencyEnum::ONESHOT => 'xxx'
        };
    }
}