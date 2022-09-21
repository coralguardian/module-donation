<?php
/**
 * Plugin Name: CG - Module donation
 * Plugin URI:
 * Description: Gestion des dons de la plateforme
 * Version: 0.1
 * Requires PHP: 8.1
 * Author: Benoit DELBOE & Grégory COLLIN
 * Author URI:
 * Licence: GPLv2
 */

use D4rk0snet\Donation\Action\CreateDonation;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Listener\NewSubscriptionStatusUpdated;
use Hyperion\Stripe\Enum\StripeEventEnum;

add_action('init', ['\D4rk0snet\Donation\Plugin','init']);
add_action(CoralDonationActions::PENDING_DONATION->value, [CreateDonation::class,'doAction'], 10, 2);
add_action(StripeEventEnum::SUBSCRIPTION_UPDATE->value, [NewSubscriptionStatusUpdated::class, 'doAction'], 10,1);
add_action(StripeEventEnum::PAYMENT_SUCCESS->value, [\D4rk0snet\Donation\Listener\NewPaymentDone::class, 'doAction'], 10,1);

add_filter(\Hyperion\Doctrine\Plugin::ADD_ENTITIES_FILTER, function (array $entitiesPath) {
    $entitiesPath[] = __DIR__."/src/Entity";

    return $entitiesPath;
});
