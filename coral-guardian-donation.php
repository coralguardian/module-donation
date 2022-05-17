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

add_action('init', ['\D4rk0snet\Donation\Plugin','init']);
add_filter(\Hyperion\Doctrine\Plugin::ADD_ENTITIES_FILTER, function (array $entitiesPath) {
    $entitiesPath[] = __DIR__."/src/Entity";

    return $entitiesPath;
});
//add_action(StripeEventEnum::SETUPINTENT_SUCCESS, ['\D4rk0snet\Donation\Action\SetupIntentSuccess','doAction']);
