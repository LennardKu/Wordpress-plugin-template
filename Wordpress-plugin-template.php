<?php
/*
Plugin Name: Wordpress plugin template
Plugin URI: https://github.com/LennardKu/Wordpress-plugin-template
Description: This is a template for wordpress plugins
Version: 1.0
Author: Lennard Kuenen
Author URI: https://webwizdom.nl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Include the shortcode functionality
require_once plugin_dir_path(__FILE__) . 'includes/custom-greeting-shortcode.php';

// Include the GitHub Updater library
require_once plugin_dir_path(__FILE__) . 'lib/GitHubUpdater.php';

// Initialize GitHub Updater
if (is_admin()) {
   $updater = new Custom_Greeting_Updater(__FILE__);
    $updater->set_username('your-username');
    $updater->set_repository('custom-greeting-plugin');
    $updater->authorize('your-github-access-token');
    $updater->initialize();
}
