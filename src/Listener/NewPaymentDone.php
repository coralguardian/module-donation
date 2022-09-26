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
use Hyperion\Stripe\Service\StripeService;
use JsonMapper;
use Stripe\PaymentIntent;

/**
 * Cette classe écoute PAYMENT_SUCCEEDED de stripe
 */
class NewPaymentDone
{
    public static function doAction(PaymentIntent $stripePaymentIntent)
    {
        $mapper = new JsonMapper();
        $mapper->bExceptionOnMissingData = true;
        $mapper->postMappingMethod = 'afterMapping';

        // Si c'est un don unique, nous aurons le donationOrder dans les metas de l'invoice
        $invoice = StripeService::getStripeClient()->invoices->retrieve($stripePaymentIntent->invoice);
        if($invoice->metadata['oneshotDonation'] === null) {
            return;
        }

        $customerModel = $mapper->map(json_decode($invoice->metadata['customer'], false, 512, JSON_THROW_ON_ERROR), new CustomerModel());
        /** @var DonationOrderModel $donationOrderModel */
        $donationOrderModel = $mapper->map(json_decode($invoice->metadata['oneshotDonation'], false, 512, JSON_THROW_ON_ERROR), new DonationOrderModel());

        $donationModel = new DonationModel();
        $donationModel
            ->setDonationRecurrency(DonationRecurrencyEnum::ONESHOT)
            ->setAmount($donationOrderModel->getAmount())
            ->setStripePaymentIntentId($stripePaymentIntent->id)
            ->setIsPaid(true)
            ->setDate(new \DateTime())
            ->setPaymentMethod(PaymentMethod::CREDIT_CARD)
            ->setProject(Project::from($donationOrderModel->getProject()))
            ->setLang(Language::from($invoice->metadata['language'])) // @todo: Est ce que la langue a réellement du sens dans un don ?
            ->setCustomerModel($customerModel);

        do_action(CoralDonationActions::PENDING_DONATION->value, $donationModel);
    }
}