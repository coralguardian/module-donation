<?php

namespace D4rk0snet\Donation\Listener;

use D4rk0snet\CoralCustomer\Model\CustomerModel;
use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\CoralOrder\Enums\PaymentMethod;
use D4rk0snet\CoralOrder\Enums\Project;
use D4rk0snet\CoralOrder\Model\DonationOrderModel;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Models\DonationModel;
use JsonMapper;
use Stripe\Subscription;

/**
 * Cette classe écoute l'action NEW_ORDER du module order
 */
class NewSubscriptionStatusUpdated
{
    public static function doAction(Subscription $subscription)
    {
        // Si il s'agit d'un don mensuel qui vient d'être validé(payé et moyen de paiement valide)
        // Alors on peut l'enregistrer en base
        if( $subscription->status === 'active' ) {
            $mapper = new JsonMapper();
            $mapper->bExceptionOnMissingData = true;
            $mapper->postMappingMethod = 'afterMapping';
            $customerModel = $mapper->map(json_decode($subscription->metadata['customer'], false, 512, JSON_THROW_ON_ERROR), new CustomerModel());
            $donationOrderModel = $mapper->map(json_decode($subscription->metadata['donationOrderedModel'], false, 512, JSON_THROW_ON_ERROR), new DonationOrderModel());

            $donationModel = new DonationModel();
            $donationModel
                ->setDonationRecurrency(DonationRecurrencyEnum::MONTHLY)
                ->setAmount($subscription->plan->amount / 100)
                ->setStripePaymentIntentId($subscription->id) // @todo: Changer en base ce n'est pas un paymentIntentId
                ->setIsPaid(true)
                ->setDate(new \DateTime())
                ->setPaymentMethod(PaymentMethod::CREDIT_CARD)
                ->setProject(Project::from($donationOrderModel->getProject()))
                ->setLang(Language::FR) // @todo: Est ce que la langue a réellement du sens dans un don mensuel?
                ->setCustomerModel($customerModel);

            do_action(CoralDonationActions::PENDING_DONATION->value, $donationModel); // @todo: Découpler plus tard, on ne devrait pas avoir d'évènements d'un autre module.
        }
    }
}