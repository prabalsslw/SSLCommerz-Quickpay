<?php 

/**
* Plugin Name: SSLCommerz Quick Pay - Donation/Registration/Membership/Payment
* Plugin URI: https://www.sslcommerz.com/
* Description: This is the custome payment plugin for SSLCommerz. This plugin may used to Collect Donation, Registration Fees, Membership Fees or any other Custome payment.
* Version: 1.0.0
* Stable tag: 1.0.0
* Author: Prabal Mallick
* Author URI: https://prabalsslw.wixsite.com/prabal
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* License: GPL2
**/

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SSLCommerz_Woocommerce
 * @author     Prabal Mallick <prabalsslw@gmail.com>
 */

	defined( 'ABSPATH' ) or die(); // Protect from alien invasion

	define( 'SSLCDPATH', plugin_dir_path( __FILE__ ) );
	define( 'SSLCDURL', plugin_dir_url( __FILE__ ) );

	global $sslcom_quickpay_slug;

	define( 'SSLCOMMERZ_QUICKPAY_VERSION', '1.0.0' );

	$sslcom_quickpay_slug = 'sslcom_quickpay';
	$quickpay_options = get_option( 'sslcom_quickpay' );

	require_once( SSLCDPATH . 'lib/sslcom-quickpay-admin.php' );
	require_once( SSLCDPATH . 'lib/sslcom-quickpay-init.php' );
	require_once( SSLCDPATH . 'lib/sslcom-quickpay-rewrite.php' );

	use Sslcommerz\Quickpay\Admin\Quickpay_Admin_Settings;
	use Sslcommerz\Quickpay\Init\Quickpay_Init;

	new Quickpay_Admin_Settings;

	
	/**
	* Hook plugin activation
	*/
	register_activation_hook( __FILE__, 'sslcom_quickpay_active' );
	function sslcom_quickpay_active() {
		Quickpay_Init::sslcom_quickpay_install();

		$installed_version = get_option( "sslcommerz_quickpay_version" );
		if ( $installed_version == SSLCOMMERZ_QUICKPAY_VERSION ) {
			return true;
		}
		update_option( 'sslcommerz_quickpay_version', SSLCOMMERZ_QUICKPAY_VERSION );
	}

	/**
	* Hook plugin deactivation
	*/
	register_deactivation_hook( __FILE__, 'sslcom_quickpay_deactivator' );
	function sslcom_quickpay_deactivator() { 
		flush_rewrite_rules();
	}


	if(isset($quickpay_options['enable_quickpay']) && !empty($quickpay_options['enable_quickpay']))
	{
		add_action('plugins_loaded', array(Sslcom_Ipn_Url::get_instance(), 'setup'));
		add_shortcode('SSLCOMMERZ_QUICKPAY', 'make_sslcommerz_quickpay_shortcode');
	}

	# Create SHortcode
	function make_sslcommerz_quickpay_shortcode() {
    	require_once( SSLCDPATH . 'lib/sslcom-quickpay.php' );
	}
	
	function sslcom_quickpay_settings_link($links)
	{
	    $pluginLinks = array(
            'settings' => '<a href="'. esc_url(admin_url( 'admin.php?page=quickpay-settings')) .'">Settings</a>',
            'docs'     => '<a href="https://developer.sslcommerz.com/doc/v4/" target="blank">Docs</a>',
            'sandbox'     => '<a href="https://developer.sslcommerz.com/registration/" target="blank">Create Sandbox</a>',
            'support'  => '<a href="mailto:integration@sslcommerz.com">Support</a>'
        );

	    $links = array_merge($links, $pluginLinks);

	    return $links;
	}

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sslcom_quickpay_settings_link');



	# Load Plugin CSS
	function sslcommerz_quickpay_load_custom_style() {
        wp_register_style( 'sslcommerz_quickpay', SSLCDURL . 'include/css/style.css', false, SSLCOMMERZ_QUICKPAY_VERSION );
        wp_enqueue_style( 'sslcommerz_quickpay' );
	}

	add_action( 'wp_enqueue_scripts', 'sslcommerz_quickpay_load_custom_style' );