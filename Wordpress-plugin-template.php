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
    $config = array(
        'slug' => plugin_basename(__FILE__),
        'proper_folder_name' => 'Wordpress-plugin-template',
        'api_url' => 'https://api.github.com/repos/LennardKu/Wordpress-plugin-template',
        'raw_url' => 'https://raw.github.com/LennardKu/Wordpress-plugin-template/master',
        'github_url' => 'https://github.com/LennardKu/Wordpress-plugin-template',
        'zip_url' => 'https://github.com/LennardKu/Wordpress-plugin-template/archive/master.zip',
        'sslverify' => true,
        'requires' => '3.0',
        'tested' => '5.8',
        'readme' => 'README.md',
    );

    new GitHubUpdater($config);
}
