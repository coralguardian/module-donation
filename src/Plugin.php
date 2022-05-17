<?php

namespace D4rk0snet\Donation;

use D4rk0snet\Donation\API\DonateEndpoint;
use D4rk0snet\Donation\API\RecurringDonateEndpoint;

class Plugin
{
    public static function init()
    {
        do_action(\Hyperion\RestAPI\Plugin::ADD_API_ENDPOINT_ACTION, new RecurringDonateEndpoint());
        do_action(\Hyperion\RestAPI\Plugin::ADD_API_ENDPOINT_ACTION, new DonateEndpoint());
    }
}
