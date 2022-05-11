<?php

namespace D4rk0snet\Donation;

use D4rk0snet\Donation\API\RecurringDonateEndpoint;
use Hyperion\Stripe\Enum\StripeEventEnum;

class Plugin
{
    public static function init()
    {
        add_filter(\Hyperion\Doctrine\Plugin::ADD_ENTITIES_FILTER, function(array $entitiesPath)
        {
           $entitiesPath[] = __DIR__."/Entity";

           return $entitiesPath;
        });

        do_action(\Hyperion\RestAPI\Plugin::ADD_API_ENDPOINT_ACTION, new RecurringDonateEndpoint());
        add_action(StripeEventEnum::SETUPINTENT_SUCCESS, '\D4rk0snet\Donation\Action\SetupIntentSuccess::doAction');
    }

}