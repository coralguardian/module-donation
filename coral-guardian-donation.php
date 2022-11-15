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

use D4rk0snet\CoralOrder\Enums\CoralOrderEvents;
use D4rk0snet\Donation\Action\CreateDonation;
use D4rk0snet\Donation\Enums\CoralDonationActions;
use D4rk0snet\Donation\Listener\NewDonation;

add_action('init', ['\D4rk0snet\Donation\Plugin','init']);
add_action(CoralDonationActions::PENDING_DONATION->value, [CreateDonation::class,'doAction'], 10, 2);
add_action(CoralOrderEvents::NEW_DONATION->value, [NewDonation::class, 'doAction'], 10,3);

add_filter(\Hyperion\Doctrine\Plugin::ADD_ENTITIES_FILTER, function (array $entitiesPath) {
    $entitiesPath[] = __DIR__."/src/Entity";

    return $entitiesPath;
});
