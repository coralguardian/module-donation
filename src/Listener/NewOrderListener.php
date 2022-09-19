<?php

namespace D4rk0snet\Donation\Listener;

use D4rk0snet\CoralOrder\Model\OrderModel;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Models\DonationModel;

/**
 * Cette classe écoute l'action NEW_ORDER du module order
 */
class NewOrderListener
{
    public static function doAction(OrderModel $model)
    {
        if(count($model->getDonationOrdered()) === 0) {
            return;
        }

        foreach($model->getDonationOrdered() as $donationOrderModel) {
            $donationModel = new DonationModel();
            $donationModel
                ->setAmount($donationOrderModel->getAmount())
                ->setCustomerModel($model->getCustomer())
                ->setLang($model->getLang())
                ->setPaymentMethod($model->getPaymentMethod())
                ->setDate(new \DateTime())
                ->setDonationRecurrency($donationOrderModel->getDonationRecurrency())
                ->setIsPaid(true); // @todo : ce ne sera pas le cas pour le virement bancaire !

            do_action(CoralDonationActions::PENDING_DONATION->value, $donationModel);
        }
    }
}