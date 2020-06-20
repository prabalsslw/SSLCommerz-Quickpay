<?php 
/*
 * Removes options from database when plugin is deleted.
 *  
 *
 */

# if uninstall not called from WordPress exit

	if (!defined('WP_UNINSTALL_PLUGIN' ))
	    exit();

	global $wpdb, $wp_version;

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sslcom_quickpay_payment" );

	delete_option("sslcommerz_quickpay_version");
	delete_option('sslcom_quickpay');

	wp_cache_flush();

?>