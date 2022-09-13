<?php

namespace D4rk0snet\Donation\Service;

use D4rk0snet\Coralguardian\Entity\CompanyCustomerEntity;
use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\Stripe\Model\PriceSearchModel;
use Hyperion\Stripe\Model\ProductSearchModel;
use Hyperion\Stripe\Service\BillingService;
use Hyperion\Stripe\Service\CustomerService;
use Hyperion\Stripe\Service\SearchService;
use Hyperion\Stripe\Service\StripeService;
use Stripe\PaymentIntent;
use Stripe\Price;

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

        $searchProductModel = (new ProductSearchModel())->addMetadata(['key' => DonationRecurrencyEnum::ONESHOT->value]);
        $stripeProduct = SearchService::searchProduct($searchProductModel)->first();
        if($stripeProduct === null) {
            throw new \Exception("Impossible de trouver le produit de don avec la clef ".DonationRecurrencyEnum::ONESHOT->value);
        }

        // On cherche si ce prix a déjà été créé pour ce produit.
        $priceSearchModel = (new PriceSearchModel())->setProduct($stripeProduct->id);
        $stripeProductPrices = SearchService::searchPrice($priceSearchModel);
        $price = array_filter($stripeProductPrices->toArray(), static function(Price $stripeProductPrice) use ($donationModel) {
            return $stripeProductPrice->unit_amount === $donationModel->getAmount();
        });
        if(count($price)) {
            $productPrice = current($price);
        } else {
            $productPrice = BillingService::createCustomPrice(
                $donationModel->getAmount(),
                $stripeProduct->id
            );
        }

        $stripeCustomerId = self::createStripeCustomer($customer);

        BillingService::createLineItem(
            customerId: $stripeCustomerId,
            priceId: $productPrice->id,
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
