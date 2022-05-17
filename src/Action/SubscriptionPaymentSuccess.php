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
            customerEmail: $paymentIntent->metadata->email,
            firstname: $paymentIntent->metadata->firstname,
            lastname: $paymentIntent->metadata->lastname,
            paymentMethodId: $paymentIntent->payment_method,
            amount: $paymentIntent->metadata->amount
        );
    }

}