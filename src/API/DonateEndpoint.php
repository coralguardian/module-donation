<?php

namespace D4rk0snet\Donation\API;

use D4rk0snet\Donation\Models\DonationModel;
use D4rk0snet\Donation\Service\DonationService;
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
        $payload = json_decode($request->get_body());
        if ($payload === null) {
            return APIManagement::APIError("Invalid body content", 400);
        }

        try {
            $mapper = new JsonMapper();
            $mapper->bExceptionOnUndefinedProperty = true;
            $mapper->bExceptionOnMissingData = true;
            $donationModel = $mapper->map($payload, new DonationModel());
        } catch (\Exception $exception) {
            return APIManagement::APIError($exception->getMessage(), 400);
        }

        $donation = DonationService::createDonation($donationModel);
        $paymentIntent = DonationService::createInvoiceAndGetPaymentIntent($donationModel);

        // Add Donation id to paymentintent
        StripeService::addMetadataToPaymentIntent($paymentIntent, [
            'donation_uuid' => $donation->getUuid(),
            'type' => 'donation'
        ]);

        return APIManagement::APIOk([
            'secret' => $paymentIntent->client_secret
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
