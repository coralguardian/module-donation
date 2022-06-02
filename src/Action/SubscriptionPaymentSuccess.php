<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Coralguardian\Event\SubscriptionOrder;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Service\SubscriptionService;
use Stripe\PaymentIntent;

class SubscriptionPaymentSuccess
{
    public static function doAction(PaymentIntent $stripePaymentIntent)
    {
        if ($stripePaymentIntent->metadata->type !== 'recurring_donation') {
            return;
        }

        // Save Payment reference in order
        $donationUuid = $stripePaymentIntent->metadata->donation_uuid;
        /** @var RecurringDonationEntity $entity */
        $entity = DoctrineService::getEntityManager()->getRepository(DonationEntity::class)->find($donationUuid);

        if ($entity === null) {
            return;
        }

        $entity->setStripePaymentIntentId($stripePaymentIntent->id);
        $entity->setIsPaid(true);

        $subscription = SubscriptionService::createSubscription(
            customerId: $stripePaymentIntent->customer,
            amount: $stripePaymentIntent->amount
        );

        $entity->setSubscriptionId($subscription->id);
        DoctrineService::getEntityManager()->flush();

        // Send email event with data needed
        SubscriptionOrder::sendEvent($entity);
    }
}
