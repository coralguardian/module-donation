<?php

namespace D4rk0snet\Donation\API;

use D4rk0snet\Donation\Models\DonationModel;
use D4rk0snet\Donation\Service\DonationService;
use Hyperion\RestAPI\APIEnpointAbstract;
use Hyperion\RestAPI\APIManagement;
use Hyperion\Stripe\Service\CustomerService;
use Hyperion\Stripe\Service\StripeService;
use JsonMapper;
use WP_REST_Request;
use WP_REST_Response;

class RecurringDonateEndpoint extends APIEnpointAbstract
{
    public static function callback(WP_REST_Request $request): WP_REST_Response
    {
        $payload = json_decode($request->get_body(), false, 512, JSON_THROW_ON_ERROR);
        if ($payload === null) {
            return APIManagement::APIError("Invalid body content", 400);
        }

        try {
            $mapper = new JsonMapper();
            $mapper->bExceptionOnUndefinedProperty = true;
            $mapper->bExceptionOnMissingData = true;

            /** @var DonationModel $donationModel */
            $donationModel = $mapper->map($payload, new DonationModel());
        } catch (\Exception $exception) {
            return APIManagement::APIError($exception->getMessage(), 400);
        }

        $customer = CustomerService::getOrCreateIndividualCustomer(
            email: $donationModel->getEmail(),
            firstName: $donationModel->getFirstname(),
            lastName: $donationModel->getLastname(),
            metadata: ['type' => 'individual']
        );

        $secret = StripeService::createPaymentIntent(
            amount: $donationModel->getAmount(),
            customerId: $customer->id,
            metadata: array_merge(
                ['type' => 'recurringSubscription'],
                $donationModel->toArray()
            ),
            isForFutureUsage: true
        );

        return APIManagement::APIOk([
            'secret' => $secret
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
        return 'donate/recurring';
    }
}
