<?php

namespace D4rk0snet\Donation\API;

use D4rk0snet\Coralguardian\Event\BankTransferPayment;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Enums\PaymentMethod;
use D4rk0snet\Donation\Models\DonationModel;
use D4rk0snet\Donation\Service\DonationService;
use Hyperion\Doctrine\Service\DoctrineService;
use Hyperion\RestAPI\APIEnpointAbstract;
use Hyperion\RestAPI\APIManagement;
use Hyperion\Stripe\Service\StripeService;
use JsonMapper;
use WP_REST_Request;
use WP_REST_Response;

class DonateEndpoint extends APIEnpointAbstract
{
    public static function callback(WP_REST_Request $request): WP_REST_Response
    {
        $payload = json_decode($request->get_body(), false, 512, JSON_THROW_ON_ERROR);
        if ($payload === null) {
            return APIManagement::APIError("Invalid body content", 400);
        }

        try {
            $mapper = new JsonMapper();
            $mapper->bExceptionOnMissingData = true;
            /** @var DonationModel $donationModel */
            $donationModel = $mapper->map($payload, new DonationModel());
        } catch (\Exception $exception) {
            return APIManagement::APIError($exception->getMessage(), 400);
        }

        if ($donationModel->getDonationRecurrency() === DonationRecurrencyEnum::ONESHOT) {
            $donation = DonationService::createDonation($donationModel);

            if ($donation->getPaymentMethod() === PaymentMethod::BANK_TRANSFER) {
                BankTransferPayment::sendEvent($donation);
                DoctrineService::getEntityManager()->flush();

                return APIManagement::APIOk(["uuid" => $donation->getUuid()]);
            }

            $paymentIntent = DonationService::createInvoiceAndGetPaymentIntentForOneshotDonation($donationModel);

            StripeService::addMetadataToPaymentIntent($paymentIntent, array_merge(
                [
                    'donation_uuid' => $donation->getUuid(),
                    'type' => 'donation'
                ],
                $donationModel->toArray()
            ));
        } else {
            $recurringDonation = DonationService::createRecurrentDonation($donationModel);
            $paymentIntent = DonationService::createInvoiceAndGetPaymentIntentForRecurringDonation($donationModel);

            StripeService::addMetadataToPaymentIntent($paymentIntent, array_merge(
                [
                    'donation_uuid' => $recurringDonation->getUuid(),
                    'type' => 'recurring_donation'
                ],
                $donationModel->toArray()
            ));
        }

        return APIManagement::APIOk([
            'clientSecret' => $paymentIntent->client_secret
        ]);
    }

    public static function getMethods(): array
    {
        return ['POST'];
    }

    public static function getPermissions(): string
    {
        return "__return_true";
    }

    public static function getEndpoint(): string
    {
        return 'donate';
    }
}
