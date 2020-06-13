<?php 
    # Setup Database Table

namespace Sslcommerz\Quickpay\Init;

class Quickpay_Init
{
    public static function sslcom_quickpay_install() {

        global $wpdb;
        global $sslcom_quickpay_version;

        $table_name = $wpdb->prefix . "sslcom_quickpay_payment";

        $charset_collate = '';

        if ( ! empty( $wpdb->charset ) ) {
          $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if ( ! empty( $wpdb->collate ) ) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            `id` bigint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `trxid` varchar(50) NOT NULL,
            `tran_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `tran_status` varchar(50) NULL,
            `card_type` varchar(100) NULL,
            `card_no` varchar(50) NULL,
            `total_amount` float(10) NOT NULL,
            `product_name` varchar(300) NULL,
            `cus_name` varchar(50) NULL,
            `cus_email` varchar(150) NULL,
            `cus_phone` varchar(20) NULL,
            `nid_pass` varchar(40) NULL,
            `cus_address` varchar(1000) NULL,
            `cus_country` varchar(50) NULL,
            `cus_city` varchar(50) NULL,
            `cus_postcode` varchar(50) NULL,
            `notes` varchar(5000) NULL,
            `extra_field1` varchar(300) NULL,
            `extra_field2` varchar(300) NULL,
            `ipn_status` varchar(150) NULL
            ) $charset_collate;";
            dbDelta( $sql );
        }

        add_option( 'sslcom_quickpay_version', $sslcom_quickpay_version );

    }
}
