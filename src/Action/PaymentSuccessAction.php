<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Adoption\Entity\AdoptionEntity;
use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Email\Event\DonationEvent;
use Hyperion\Doctrine\Service\DoctrineService;

class PaymentSuccessAction
{
    /**
     * @todo: Gérer le cas ensuite pour les entreprises
     */
    public static function doAction($stripePaymentIntent)
    {
        if($stripePaymentIntent['metadata']['type'] !== 'donation') {
            return;
        }

        // Save Payment reference in order
        $donationUuid = $stripePaymentIntent['metadata']['donation_uuid'];

        /** @var AdoptionEntity $entity */
        $entity = DoctrineService::getEntityManager()->getRepository(DonationEntity::class)->find($donationUuid);
        if($entity === null) {
            return;
        }
        $entity->setStripePaymentIntentId($stripePaymentIntent['id']);
        DoctrineService::getEntityManager()->flush();

        // Send email event with data needed
        DonationEvent::send(
            email: $entity->getEmail(),
            lang: $entity->getLang()->value,
        );
    }
}