<?php

namespace D4rk0snet\Donation\Service;

use D4rk0snet\Coralguardian\Entity\CompanyCustomerEntity;
use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Service\BillingService;
use Hyperion\Stripe\Service\CustomerService;
use Hyperion\Stripe\Service\StripeService;
use Stripe\PaymentIntent;

class DonationService
{
    public static function createDonation(DonationModel $donationModel) : DonationEntity
    {
        $customer = DoctrineService::getEntityManager()
            ->getRepository(CustomerEntity::class)
            ->find($donationModel->getCustomerUUID());

        if ($customer === null) {
            throw new \Exception("Customer not found");
        }

        // Sauvegarde en bdd
        $donation = new DonationEntity(
            customer: $customer,
            date: $donationModel->getDate() ?? new \DateTime(),
            amount: $donationModel->getAmount(),
            lang: $donationModel->getLang(),
            isPaid: false,
            paymentMethod: $donationModel->getPaymentMethod()
        );

        DoctrineService::getEntityManager()->persist($donation);
        DoctrineService::getEntityManager()->flush();

        return $donation;
    }

    public static function createRecurrentDonation(DonationModel $donationModel) : RecurringDonationEntity
    {
        $customer = DoctrineService::getEntityManager()
            ->getRepository(CustomerEntity::class)
            ->find($donationModel->getCustomerUUID());

        if ($customer === null) {
            throw new \Exception("Customer not found");
        }

        // Sauvegarde en bdd
        $donation = new RecurringDonationEntity(
            customer: $customer,
            date: new \DateTime(),
            amount: $donationModel->getAmount(),
            lang: $donationModel->getLang(),
            paymentMethod: $donationModel->getPaymentMethod(),
            isPaid: false
        );

        DoctrineService::getEntityManager()->persist($donation);
        DoctrineService::getEntityManager()->flush();

        return $donation;
    }

    public static function createInvoiceAndGetPaymentIntentForRecurringDonation(DonationModel $donationModel) : PaymentIntent
    {
        $customer = DoctrineService::getEntityManager()
            ->getRepository(CustomerEntity::class)
            ->find($donationModel->getCustomerUUID());

        if ($customer === null) {
            throw new \Exception("Customer not found");
        }

        $customerId = self::createStripeCustomer($customer);

        return StripeService::createPaymentIntent(
            amount: $donationModel->getAmount(),
            customerId: $customerId,
            metadata:
            ['type' => 'recurringSubscription'],
            isForFutureUsage: true
        );
    }

    public static function createInvoiceAndGetPaymentIntentForOneshotDonation(DonationModel $donationModel) : PaymentIntent
    {
        $customer = DoctrineService::getEntityManager()
            ->getRepository(CustomerEntity::class)
            ->find($donationModel->getCustomerUUID());

        if ($customer === null) {
            throw new \Exception("Customer not found");
        }

        $price  = BillingService::createCustomPrice(
            $donationModel->getAmount(),
            DonationRecurrencyEnum::ONESHOT->getStripeProductId()
        );

        $stripeCustomerId = self::createStripeCustomer($customer);

        BillingService::createLineItem(
            customerId: $stripeCustomerId,
            priceId: $price->id,
            quantity: 1
        );

        $bill = BillingService::createBill($stripeCustomerId);

        return BillingService::finalizeAndGetPaymentIntent($bill);
    }

    private static function createStripeCustomer(CustomerEntity $customer) : string
    {
        if ($customer instanceof CompanyCustomerEntity) {
            $customerId = CustomerService::getOrCreateCompanyCustomer(
                email: $customer->getEmail(),
                companyName: $customer->getCompanyName(),
                mainContactName: $customer->getMainContactName()
            )->id;
        } else {
            $customerId = CustomerService::getOrCreateIndividualCustomer(
                email: $customer->getEmail(),
                firstName: $customer->getFirstname(),
                lastName: $customer->getLastname(),
                metadata: ['type' => 'individual']
            )->id;
        }

        return $customerId;
    }
}
