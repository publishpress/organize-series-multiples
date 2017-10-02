<?php
/*
Plugin Name: Organize Series Addon: Multiples
Plugin URI: http://organizeseries.com
Version: 1.3
Description: This plugin is an addon for the Organize Series Plugin and requires it to work.  Organzie Series Multiples gives the ability for authors to add posts to more than one series. <strong>NOTE: Once activating this plugin IT IS NOT POSSIBLE to roll back to just using Organize Series Core without having to re-edit all your series as Organize Series Multiples changes the way the series parts are saved to allow for multiple series.  USE WITH CARE!!</strong>
Author: Darren Ethier
Author URI: http://www.unfoldingneurons.com
*/

$orgseries_mult_ver = '1.3';
require __DIR__ . '/vendor/autoload.php';

/**
 * @todo
 * - Class for registering the add-on with RegisteredExtensions, we might need a new service in OS core that's
 *   used for registering extensions (Extension Registry?) All-addons would use this.
 * - Register a global "HasHooks" route that's used to always register the add-on extension on every route.
 * - Add to OS core the automatic initialization of the EDD updater (registered to its own admin-only route?) that automatically
 *   instantiates an instance of the updater for each registered extension.
 */

/* LICENSE */
//"Organize Series Plugin" and all addons for it created by this author are copyright (c) 2007-2012 Darren Ethier. This program is free software; you can redistribute it and/or
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

//check for php version requirements.  Here we only load up organize series multiples if PHP version is greater than 5.6
//Organize Series Core will take care of any necessary notice on the PHP version.
if (version_compare(PHP_VERSION, '5.6') >= 0) {
    require $os_multi_plugin_dir . 'bootstrap.php';
}
