<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Coralguardian\Event\SubscriptionOrder;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Service\ProductService;
use Hyperion\Stripe\Service\SubscriptionService;
use Stripe\PaymentIntent;
use Stripe\Product;

class SubscriptionPaymentSuccess
{
    public static function doAction(PaymentIntent $stripePaymentIntent)
    {
        if ($stripePaymentIntent->metadata->type !== "recurring_donation") {
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

        $stripeSearchResults = ProductService::getProductByMetadata(['metadata' => ['key' => DonationRecurrencyEnum::MONTHLY->value]]);
        if($stripeSearchResults->count() === 0) {
            throw new \Exception("Impossible de trouver l'article dans stripe !");
        }

        /** @var Product $stripeProduct */
        $stripeProduct = $stripeSearchResults->first();

        $subscription = SubscriptionService::createSubscription(
            customerId: $stripePaymentIntent->customer,
            amount: $stripePaymentIntent->amount,
            defaultPaymentMethod: $stripePaymentIntent->payment_method,
            productId: $stripeProduct->id
        );

        $entity->setSubscriptionId($subscription->id);
        DoctrineService::getEntityManager()->flush();

        // Send email event with data needed
        SubscriptionOrder::sendEvent($entity);
    }
}
