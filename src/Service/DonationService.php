<?php

namespace D4rk0snet\Donation\Service;

use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Coralguardian\Entity\IndividualCustomerEntity;
use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Service\BillingService;
use Hyperion\Stripe\Service\CustomerService;
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
            date: new \DateTime(),
            amount: $donationModel->getAmount(),
            lang: $donationModel->getLang()
        );

        DoctrineService::getEntityManager()->persist($donation);
        DoctrineService::getEntityManager()->flush();

        return $donation;
    }

    public static function createRecurrentDonation(DonationModel $donationModel) : DonationEntity
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
            lang: $donationModel->getLang()
        );

        DoctrineService::getEntityManager()->persist($donation);
        DoctrineService::getEntityManager()->flush();

        return $donation;
    }

    public static function createInvoiceAndGetPaymentIntent(DonationModel $donationModel) : PaymentIntent
    {
        $customer = DoctrineService::getEntityManager()
            ->getRepository(CustomerEntity::class)
            ->find($donationModel->getCustomerUUID());

        if ($customer === null) {
            throw new \Exception("Customer not found");
        }

        if ($customer instanceof IndividualCustomerEntity) {
            $customerId = CustomerService::getOrCreateIndividualCustomer(
                email: $customer->getEmail(),
                firstName: $customer->getFirstname(),
                lastName: $customer->getLastname(),
                metadata: ['type' => 'individual']
            )->id;
        } else {
            $customerId = CustomerService::getOrCreateCompanyCustomer(
                email: $customer->getEmail(),
                companyName: $customer->getCompanyName(),
                mainContactName: $customer->getMainContactName()
            )->id;
        }

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
