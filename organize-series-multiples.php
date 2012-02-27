<?php
/*
Plugin Name: Organize Series Addon: Multiples
Plugin URI: http://organizeseries.com
Version: 0.5
Description: This plugin is an addon for the Organize Series Plugin and requires it to work.  Organzie Series Multiples gives the ability for authors to add posts to more than one series. <strong>NOTE: Once activating this plugin IT IS NOT POSSIBLE to roll back to just using Organize Series Core without having to re-edit all your series as Organize Series Multiples changes the way the series parts are saved to allow for multiple series.  USE WITH CARE!!</strong>
Author: Darren Ethier
Author URI: http://www.unfoldingneurons.com
*/

$orgseries_mult_ver = '0.5';

/* LICENSE */
//"Organize Series Plugin" and all addons for it created by this author are copyright (c) 2007-2011 Darren Ethier. This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
//It goes without saying that this is a plugin for WordPress and I have no interest in developing it for other platforms so please, don't ask ;).

$os_multi_plugin_dir = WP_PLUGIN_DIR.'/organize-series-multiples/';
$os_multi_plugin_url = WP_PLUGIN_URL.'/organize-series-multiples/';

//let's set some constants
define('OS_MULTI_PATH', $os_multi_plugin_dir);
define('OS_MULTI_URL', $os_multi_plugin_url);
define('OS_MULTI_VER', $orgseries_mult_ver); //make sure the version number is available everywhere.

//let's include required files
require_once(OS_MULTI_PATH.'os-multi-setup.php');

//Automatic Upgrades stuff
if ( file_exists(WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php') ) {
	//let's get the client api key for updates
	$series_settings = get_option('org_series_options');
	$api_key = $series_settings['orgseries_api'];
	$host_server_url = 'http://organizeseries.com';
	$plugin_slug = 'organize-series-multiples';
	$options = array(
		'apikey' => $api_key,
		'lang_domain' => 'organize-series'
	);
	
	require( WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php' );
	$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options);
}

//let's remove orgSeries core hooks/filter we're replacing
add_action('init', 'os_multiples_remove_actions');

function os_multiples_remove_actions() {
	remove_action('quick_edit_custom_box', 'inline_edit_series', 9, 2);
	remove_action('manage_posts_custom_column', 'orgSeries_custom_column_action', 12, 2);
	remove_action('admin_print_scripts-edit.php', 'inline_edit_series_js');
	remove_action('wp_ajax_add-series', 'admin_ajax_series');
	remove_action('admin_print_scripts-post.php', 'orgSeries_post_script');
	remove_action('admin_print_scripts-post-new.php', 'orgSeries_post_script');
	remove_action('delete_series', 'wp_delete_series', 10, 2);
}
//let's initialize the plugin
$osMulti = new osMulti();
?>