<?php

namespace D4rk0snet\Donation\API;

use D4rk0snet\Donation\Models\DonationModel;
use D4rk0snet\Donation\Service\DonationService;
use Hyperion\RestAPI\APIEnpointAbstract;
use Hyperion\RestAPI\APIManagement;
use JsonMapper;
use WP_REST_Request;
use WP_REST_Response;

class RecurringDonateEndpoint extends APIEnpointAbstract
{
    public static function callback(WP_REST_Request $request): WP_REST_Response
    {
        $payload = json_decode($request->get_body());
        if($payload === null) {
            return APIManagement::APIError("Invalid body content", 400);
        }

        $mapper = new JsonMapper();
        $donationModel = $mapper->map($payload, new DonationModel());

        $secret = DonationService::createSubscription($donationModel);

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