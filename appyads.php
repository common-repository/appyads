<?php
/**
 * Plugin Name: AppyAds
 * Plugin URI: https://www.appyads.com
 * Description: An advertising alternative for publishers, media outlets, mobile application and website owners.
 * Author: AppyAds
 * Author URI: https://www.appyads.com/
 * Version: 1.0.3
 * Text Domain: appyads
 *
 * Copyright: (c) 2015-2019 AppyAds (support@appyads.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   AppyAds
 * @author    Jon DeWeese
 * @category  Admin
 * @copyright Copyright: (c) 2015-2019 AppyAds
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */
 
defined( 'ABSPATH' ) or exit;

require_once('lib/appyads_library.php');
require_once('classes/AppyAds_Ad.php');
// Administrator utilities
if (is_admin()) require_once('admin/appyads_admin.php');

?>