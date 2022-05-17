<?php

namespace D4rk0snet\Donation\Enums;

enum DonationRecurrencyEnum : string
{
case MONTHLY = 'monthly';
case ONESHOT = 'oneshot';

    public function getStripeProductId()
    {
        return match ($this) {
            DonationRecurrencyEnum::MONTHLY => getenv('STRIPE_MODE') === 'test' ? 'prod_LhmfY9jyxdr3wK' : 'prod_LS3idKzaxwfh2n',
            DonationRecurrencyEnum::ONESHOT => getenv('STRIPE_MODE') === 'test' ? 'prod_LVr0bZLDLS6lPP' : 'prod_LS3EGi1t7qWSGP'
        };
    }
}
