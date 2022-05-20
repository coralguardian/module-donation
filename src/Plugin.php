<?php

namespace D4rk0snet\Donation;

use D4rk0snet\Donation\API\DonateEndpoint;

class Plugin
{
    public static function init()
    {
        do_action(\Hyperion\RestAPI\Plugin::ADD_API_ENDPOINT_ACTION, new DonateEndpoint());
    }
}
