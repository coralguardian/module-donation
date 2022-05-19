<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Email\Event\DonationEvent;
use D4rk0snet\FiscalReceipt\Endpoint\GetFiscalReceiptEndpoint;

class PaymentSuccessAction
{
    /**
     * @todo: Gérer le cas ensuite pour les entreprises
     */
    public static function doAction($stripePaymentIntent)
    {
        if ($stripePaymentIntent->metadata->type !== 'donation') {
            return;
        }

        // Save Payment reference in order
        $donationUuid = $stripePaymentIntent->metadata->adoption_uuid;
        $fiscalReceiptUrl = GetFiscalReceiptEndpoint::getUrl()."?".GetFiscalReceiptEndpoint::ORDER_UUID_PARAM."=".$donationUuid;

        // Send email event with data needed
        DonationEvent::send(
            email: $stripePaymentIntent->metadata->email,
            fiscalReceiptUrl: $fiscalReceiptUrl,
            lang: $stripePaymentIntent->metadata->lang,
        );
    }
}
