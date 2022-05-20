<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Email\Event\DonationEvent;
use D4rk0snet\FiscalReceipt\Service\FiscalReceiptService;
use Hyperion\Doctrine\Service\DoctrineService;

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
        $entity = DoctrineService::getEntityManager()->getRepository(DonationEntity::class)->find($donationUuid);

        if ($entity === null) {
            return;
        }

        $entity->setStripePaymentIntentId($stripePaymentIntent->id);
        $entity->setIsPaid(true);
        DoctrineService::getEntityManager()->flush();

        // Send email event with data needed
        DonationEvent::send(
            email: $stripePaymentIntent->metadata->email,
            fiscalReceiptUrl: FiscalReceiptService::getURl($donationUuid),
            lang: $stripePaymentIntent->metadata->lang,
        );
    }
}
