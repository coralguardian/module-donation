<?php

namespace D4rk0snet\Donation\Service;

use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Service\BillingService;
use Hyperion\Stripe\Service\CustomerService;
use Hyperion\Stripe\Service\StripeService;
use Hyperion\Stripe\Service\SubscriptionService;
use Stripe\PaymentIntent;

class DonationService
{
    public static function createDonation(DonationModel $donationModel) : DonationEntity
    {
        // Sauvegarde en bdd
        $donation = new DonationEntity(
            firstname: $donationModel->getFirstname(),
            lastname: $donationModel->getLastname(),
            address: $donationModel->getAddress(),
            city: $donationModel->getCity(),
            postalCode: $donationModel->getPostalCode(),
            email: $donationModel->getEmail(),
            donationStart: new \DateTime(),
            amount: $donationModel->getAmount(),
            lang: $donationModel->getLang()
        );

        DoctrineService::getEntityManager()->persist($donation);
        DoctrineService::getEntityManager()->flush();

        return $donation;
    }

    public static function createInvoiceAndGetPaymentIntent(DonationModel $donationModel) : PaymentIntent
    {
        $customerId = CustomerService::getOrCreateCustomer(
            email: $donationModel->getEmail(),
            firstName: $donationModel->getFirstname(),
            lastName: $donationModel->getLastname(),
            metadata: ['type' => 'individual']
        )->id;

        $price  = BillingService::createCustomPrice(
            $donationModel->getAmount(),
            DonationRecurrencyEnum::ONESHOT->getStripeProductId()
        );

        BillingService::createLineItem(
            customerId: $customerId,
            priceId: $price->id,
            quantity: 1
        );

        $bill = BillingService::createBill($customerId);

        return BillingService::finalizeAndGetPaymentIntent($bill);
    }
}
