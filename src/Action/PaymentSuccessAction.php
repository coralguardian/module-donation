<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Coralguardian\Event\DonationEvent;
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
        $donationUuid = $stripePaymentIntent->metadata->donation_uuid;
        /** @var DonationEntity $entity */
        $entity = DoctrineService::getEntityManager()->getRepository(DonationEntity::class)->find($donationUuid);

        if ($entity === null) {
            return;
        }

        $entity->setStripePaymentIntentId($stripePaymentIntent->id);
        $entity->setIsPaid(true);
        DoctrineService::getEntityManager()->flush();

        // Send email event with data needed
        DonationEvent::send(
            email: $entity->getCustomer()->getEmail(),
            fiscalReceiptUrl: FiscalReceiptService::getURl($donationUuid),
            lang: $stripePaymentIntent->metadata->lang,
        );
    }
}
