<?php

namespace D4rk0snet\Donation\Listener;

use D4rk0snet\CoralCustomer\Model\CustomerModel;
use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\CoralOrder\Enums\PaymentMethod;
use D4rk0snet\CoralOrder\Enums\Project;
use D4rk0snet\CoralOrder\Model\DonationOrderModel;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Models\DonationModel;
use JsonMapper;

/**
 * Cette classe écoute l'action NEW_MONTHLY_SUBSCRIPTION du module order
 */
class NewDonation
{
    public static function doAction(
        DonationOrderModel $donationOrderModel,
        CustomerModel $customerModel,
        string $setupIntentId
    ){
            $mapper = new JsonMapper();
            $mapper->bExceptionOnMissingData = true;
            $mapper->postMappingMethod = 'afterMapping';

            $donationModel = new DonationModel();
            $donationModel
                ->setDonationRecurrency($donationOrderModel->getDonationRecurrency())
                ->setAmount($donationOrderModel->getAmount())
                ->setStripePaymentIntentId($setupIntentId) // @todo: Changer en base ce n'est pas un paymentIntentId
                ->setIsPaid(true)
                ->setDate(new \DateTime())
                ->setPaymentMethod(PaymentMethod::CREDIT_CARD)
                ->setProject(Project::from($donationOrderModel->getProject()))
                ->setLang(Language::FR) // @todo: Est ce que la langue a réellement du sens dans un don mensuel?
                ->setCustomerModel($customerModel)
                ->setIsExtra($donationOrderModel->isExtra());

            do_action(CoralDonationActions::PENDING_DONATION->value, $donationModel); // @todo: Découpler plus tard, on ne devrait pas avoir d'évènements d'un autre module.
    }
}