<?php
/**
 * Plugin Name:       BenCreatesBooks Author Toolkit
 * Plugin URI:        https://store.bencreatesbooks.com/b/authorpress-wordpress-theme
 * Description:       Content toolkit for the AuthorPress theme. Adds the Books and Events post types, the Genre taxonomy, book and event detail fields, and the guided setup wizard with demo content import.
 * Version:           1.1.0
 * Author:            BenCreatesBooks
 * Author URI:        https://bencreatesbooks.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bencreatesbooks-author-toolkit
 *
 * @package BenCreatesBooksAuthorToolkit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'BCBAT_VERSION', '1.1.0' );

require_once plugin_dir_path( __FILE__ ) . 'inc/content.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/wizard.php';

register_activation_hook( __FILE__, 'bcbat_activation_flag' );
