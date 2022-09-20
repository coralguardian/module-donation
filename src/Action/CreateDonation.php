<?php

namespace D4rk0snet\Donation\Action;

use D4rk0snet\CoralCustomer\Enum\CoralCustomerActions;
use D4rk0snet\CoralCustomer\Enum\CoralCustomerFilters;
use D4rk0snet\Donation\Entity\DonationEntity;
use D4rk0snet\Donation\Entity\RecurringDonationEntity;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use Hyperion\Doctrine\Service\DoctrineService;

class CreateDonation
{
    public static function doAction(DonationModel $donationModel)
    {
        $em = DoctrineService::getEntityManager();

        // Au cas ou le customer n'existe pas on demande sa création
        do_action(CoralCustomerActions::NEW_CUSTOMER->value, $donationModel->getCustomerModel());

        // Récupération du customer
        $customerEntity = apply_filters(
            CoralCustomerFilters::GET_CUSTOMER->value,
            null,
            $donationModel->getCustomerModel()->getEmail(),
            $donationModel->getCustomerModel()->getCustomerType()
        );

        if($donationModel->getDonationRecurrency() === DonationRecurrencyEnum::ONESHOT) {
            $donationEntity = new DonationEntity(
                customer: $customerEntity,
                date: new \DateTime(),
                amount: $donationModel->getAmount(),
                lang: $donationModel->getLang(),
                isPaid: $donationModel->isPaid(),
                paymentMethod: $donationModel->getPaymentMethod()
            );
        } else {
            $donationEntity = new RecurringDonationEntity(
                customer: $customerEntity,
                date: new \DateTime(),
                amount: $donationModel->getAmount(),
                lang: $donationModel->getLang(),
                paymentMethod: $donationModel->getPaymentMethod(),
                isPaid: $donationModel->isPaid()
            );
        }

        $donationEntity->setStripePaymentIntentId($donationModel->getStripePaymentIntentId());

        $em->persist($donationEntity);
        $em->flush($donationEntity);

        do_action(CoralDonationActions::NEW_DONATION->value, $donationModel, $donationEntity);
    }
}