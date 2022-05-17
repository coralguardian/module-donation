<?php

namespace D4rk0snet\Donation\Action;

use Hyperion\Stripe\Service\SubscriptionService;
use Stripe\PaymentIntent;

class SubscriptionPaymentSuccess
{
    public static function doAction(PaymentIntent $paymentIntent)
    {
        if ($paymentIntent->metadata->type !== 'recurringSubscription') {
            return;
        }

        SubscriptionService::createSubscription(
            customerId: $paymentIntent->customer,
            amount: $paymentIntent->amount
        );
    }
}
