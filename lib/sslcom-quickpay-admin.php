<?php 

namespace Sslcommerz\Quickpay\Admin;

class Quickpay_Admin_Settings
{
	public function __construct() {
        add_action( 'admin_menu', array( $this, 'sslcom_make_menu_pages' ) );
        add_action( 'admin_init', array( $this, 'sslcom_initialize_settings' ));
    }

	public function sslcom_make_menu_pages() {

		global $sslcom_quickpay_slug;

	    add_menu_page(
	        __('Quick Pay', $sslcom_quickpay_slug),
	        __('Quick Pay', $sslcom_quickpay_slug),
	        'administrator',
	        'quickpay-settings',
	        array( $this, 'sslcom_quickpay_settings')
	    );

	    add_submenu_page(
	        'quickpay-settings',
	        __('Payment History', $sslcom_quickpay_slug),
	        __('Payment History ', $sslcom_quickpay_slug),
	        'administrator',
	        'payment-history',
	        array( $this, 'sslcom_quickpay_payment')
	    );
	}

	# Payment History Page

	public function sslcom_quickpay_payment() {
	?>
	    <div class="wrap">
	        <h2>SSLCommerz Payment History</h2><hr>
	        <?php include_once( SSLCDPATH . 'lib/sslcom-quickpay-payment-history.php' ); ?>
	    </div>
	<?php
	}

	# Settings Page Content

	public function sslcom_quickpay_settings() {
	?>
	    <div class="wrap">
	        <h2>SSLCommerz Quick Pay Settings</h2>
	        <hr>
	        <h4 style='color:green;'>Register for sandbox merchant panel & store credentials <a href='https://developer.sslcommerz.com/registration/' target='blank'>Click Here</a></h4>

	        <hr>

	        <?php settings_errors(); ?>

	        <form method="post" action="options.php">
	            <?php settings_fields( 'sslcom_quickpay' ); ?>
	            <?php do_settings_sections( 'sslcom_quickpay' ); ?>
	            <?php submit_button(); ?>
	        </form>

	    </div>

	<?php
	}


	# Initialize Settings
	public function sslcom_initialize_settings() {
		global $sslcom_quickpay_slug;

	    if( false == get_option( 'sslcom_quickpay' ) ) {
	        add_option( 'sslcom_quickpay' );
	    }

	    add_settings_section(
	        'gateway_settings_section',
	        __('Payment Gateway Configuration', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_quickpay_callback'),
	        'sslcom_quickpay'
	    );

	    add_settings_field(
	        'enable_quickpay',
	        __('Enable Quickpay', $sslcom_quickpay_slug),
	        array( $this, 'quickpay_enable_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'enable_sandbox',
	        __('Enable Sandbox', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_sandbox_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'storeid',
	        __('Sandbox/Live Store ID', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_storeid_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'storepassword',
	        __('Sandbox/Live Store Password', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_storepass_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'sslcom_title',
	        __('Gateway Title', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_title_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'sslcom_description',
	        __('Gateway Description', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_description_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );

	    add_settings_field(
	        'return_page',
	        __('Return Page', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_returnpage_callback'),
	        'sslcom_quickpay',
	        'gateway_settings_section'
	    );


	    # Checkout page settings

	    add_settings_section(
	        'checkout_settings_section', 
	        __('Checkout Page Configuration', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_checkout_callback'),
	        'sslcom_quickpay'
	    );

	    add_settings_field(
	        'package_name',
	        __('Packages or Products Name', $sslcom_quickpay_slug),
	        array( $this, 'sslcom_package_callback'),
	        'sslcom_quickpay',
	        'checkout_settings_section'
	    );

	    add_settings_field(
	        'enable_extra_f1',
	        __('Enable Extra Field One', $sslcom_quickpay_slug),
	        array( $this, 'extra_field1_chk_callback'),
	        'sslcom_quickpay',
	        'checkout_settings_section'
	    );

	    add_settings_field(
	        'extra_f1',
	        __('Extra Field One Label', $sslcom_quickpay_slug),
	        array( $this, 'extra_field1_callback'),
	        'sslcom_quickpay',
	        'checkout_settings_section'
	    );

	    add_settings_field(
	        'enable_extra_f2',
	        __('Enable Extra Field Two', $sslcom_quickpay_slug),
	        array( $this, 'extra_field2_chk_callback'),
	        'sslcom_quickpay',
	        'checkout_settings_section'
	    );

	    add_settings_field(
	        'extra_f2',
	        __('Extra Field Two Label', $sslcom_quickpay_slug),
	        array( $this, 'extra_field2_callback'),
	        'sslcom_quickpay',
	        'checkout_settings_section'
	    );
	    
	    register_setting(
	        'sslcom_quickpay',
	        'sslcom_quickpay',
	        array( $this, 'sslcom_sanitize_settings')
	    );
	}

	public function sslcom_quickpay_callback() {
	    echo "<hr>";
	    // echo "<pre>";
	    // print_r(get_option( 'sslcom_quickpay' ));
	}

	public function quickpay_enable_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $enable_quickpay = get_option('enable_quickpay');
	    if( isset( $options['enable_quickpay'] ) && $options['enable_quickpay'] != '' ) {
	        $enable_quickpay = $options['enable_quickpay'];
	    }

	    $html = '<input type="checkbox" id="enable_quickpay" name="sslcom_quickpay[enable_quickpay]" value="1"' . checked( 1, $enable_quickpay, false ) . '/>';
	    $html .= '<label for="checkbox_example">Check to enable the plugin.</label>';

	    echo $html;
	}

	public function sslcom_sandbox_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $enable_sandbox = get_option('enable_sandbox');
	    if( isset( $options['enable_sandbox'] ) && $options['enable_sandbox'] != '' ) {
	        $enable_sandbox = $options['enable_sandbox'];
	    }

	    $html = '<input type="checkbox" id="enable_sandbox" name="sslcom_quickpay[enable_sandbox]" value="1"' . checked( 1, $enable_sandbox, false ) . '/>';
	    $html .= '<label for="checkbox_example">Check to enable Sandbox/Test Mode.</label>';

	    echo $html;
	}

	public function sslcom_storeid_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $storeid = "";
	    if( isset( $options['storeid'] ) && $options['storeid'] != '' ) {
	        $storeid = $options['storeid'];
	    }

	    $html = '<input type="text" id="storeid" name="sslcom_quickpay[storeid]" size="40" value="' . $storeid . '" placeholder="Enter Store ID" />';
	    
	    echo $html;
	}

	public function sslcom_storepass_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $storepassword = "";
	    if( isset( $options['storepassword'] ) && $options['storepassword'] != '' ) {
	        $storepassword = $options['storepassword'];
	    }

	    $html = '<input type="text" id="storepassword" name="sslcom_quickpay[storepassword]" size="40" value="' . $storepassword . '" placeholder="Enter Store Password" />';

	    echo $html;
	}

	public function sslcom_returnpage_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    if( isset( $options['return_page'] ) && $options['return_page'] != '' ) {
	        $return_page = $options['return_page'];
	    }

	    $pages = get_pages();

	    $html = '<select name="sslcom_quickpay[return_page]"><option value="">';
	    $html .= esc_attr( __( 'Select page' ) ).'</option>';
	    $html .= '<option selected value='.$return_page.'>'.get_the_title( $return_page ).'</option>';
	    foreach ( $pages as $page ) {
		    $html .= '<option value="' . $page->ID . '">';
		    $html .= $page->post_title;
		    $html .= '</option>';
		}
		$html .= '</select> <label for="return_page" style="color:green;"><b>Select that page which you have already created for [Shortcode] setup.</b></label><br><br>';
	    
	    echo $html;
	}

	public function sslcom_title_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $sslcom_title = "Pay Online (Local or International Debit/Credit/VISA/Master/Amex Card, bKash, DBBL etc)";
	    if( isset( $options['sslcom_title'] ) && $options['sslcom_title'] != '' ) {
	        $sslcom_title = $options['sslcom_title'];
	    }

	    $html = '<input type="text" id="sslcom_title" name="sslcom_quickpay[sslcom_title]" size="90" value="' . $sslcom_title . '" placeholder="Enter Payment Title" />';
	    
	    echo $html;
	}

	public function sslcom_description_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $sslcom_description = "You are able to pay for your products using local credit/debit cards like VISA, MasterCard, AMEX, DBBL Nexus Card and Mobile Wallet or bank accounts right from your online store.";
	    if( isset( $options['sslcom_description'] ) && $options['sslcom_description'] != '' ) {
	        $sslcom_description = $options['sslcom_description'];
	    }

	    $html = '<textarea rows="4" cols="93" name="sslcom_quickpay[sslcom_description]" placeholder="Description will show in the checkout page.">' . $sslcom_description . '</textarea>';
	    
	    echo $html;
	}

	public function sslcom_checkout_callback() {
	    echo "<hr>";
	}

	public function sslcom_package_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $package_name = "Package 1, Package 2";
	    
	    if( isset( $options['package_name'] ) && $options['package_name'] != '' ) {
	        $package_name = $options['package_name'];
	    }

	    $html = '<textarea rows="4" cols="93" name="sslcom_quickpay[package_name]" placeholder="Enter your package or product name">' . $package_name . '</textarea>';
	    $html .= '<br><label for="package_name" style="color:green;"><b>Enter package name by comma separation.</b>';
	    
	    echo $html;
	}

	public function extra_field1_chk_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $enable_extra_f1 = get_option('enable_extra_f1');

	    if( isset( $options['enable_extra_f1'] ) && $options['enable_extra_f1'] != '' ) {
	        $enable_extra_f1 = $options['enable_extra_f1'];
	    }

	    $html = '<input type="checkbox" id="enable_extra_f1" name="sslcom_quickpay[enable_extra_f1]" value="1"' . checked( 1, $enable_extra_f1, false ) . '/>';
	    $html .= '<label for="checkbox_example">Enable to add Extra Field One</label>';

	    echo $html;
	}

	public function extra_field1_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $extra_f1 = "User ID";
	    if( isset( $options['extra_f1'] ) && $options['extra_f1'] != '' ) {
	        $extra_f1 = $options['extra_f1'];
	    }

	    $html = '<input type="text" id="extra_f1" name="sslcom_quickpay[extra_f1]" size="40" value="' . $extra_f1 . '" placeholder="Enter Field One Label" />';

	    echo $html;
	}

	public function extra_field2_chk_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $enable_extra_f2 = get_option('enable_extra_f2');

	    if( isset( $options['enable_extra_f2'] ) && $options['enable_extra_f2'] != '' ) {
	        $enable_extra_f2 = $options['enable_extra_f2'];
	    }

	    $html = '<input type="checkbox" id="enable_extra_f2" name="sslcom_quickpay[enable_extra_f2]" value="1"' . checked( 1, $enable_extra_f2, false ) . '/>';
	    $html .= '<label for="checkbox_example">Enable to add Extra Field Two</label>';

	    echo $html;
	}

	public function extra_field2_callback() {
	    $options = get_option( 'sslcom_quickpay' );

	    $extra_f2 = "Tracking ID";
	    if( isset( $options['extra_f2'] ) && $options['extra_f2'] != '' ) {
	        $extra_f2 = $options['extra_f2'];
	    }

	    $html = '<input type="text" id="extra_f2" name="sslcom_quickpay[extra_f2]" size="40" value="' . $extra_f2 . '" placeholder="Enter Field Two Label" />';

	    echo $html;
	}


	# Sanitize & Validate data

	public function safeg_sanitize_otp_settings( $input ) {
	    
	    global $sslcom_quickpay_slug;
	    $output = array();

	    if ( isset( $input['enable_quickpay'] ) ) {
	        if (  $input['enable_quickpay']  ) {
	            $output['enable_quickpay'] =  $input['enable_quickpay'] ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enable plugin', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['enable_sandbox'] ) ) {
	        if (  $input['enable_sandbox']  ) {
	            $output['enable_sandbox'] =  $input['enable_sandbox'] ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enable sandbox', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['storeid'] ) ) {
	        if (  $input['storeid']  ) {
	            $output['storeid'] =  sanitize_text_field($input['storeid']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Store ID', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['storepassword'] ) ) {
	        if (  $input['storepassword']  ) {
	            $output['storepassword'] =  sanitize_text_field($input['storepassword']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Store Password', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['storepassword'] ) ) {
	        if (  $input['storepassword']  ) {
	            $output['storepassword'] =  sanitize_text_field($input['storepassword']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Store Password', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['return_page'] ) ) {
	        if (  $input['return_page']  ) {
	            $output['return_page'] =  $input['return_page'] ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Select Return Page', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['sslcom_title'] ) ) {
	        if (  $input['sslcom_title']  ) {
	            $output['sslcom_title'] =  sanitize_text_field($input['sslcom_title']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Gateway Title', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['sslcom_description'] ) ) {
	        if (  $input['sslcom_description']  ) {
	            $output['sslcom_description'] =  sanitize_textarea_field($input['sslcom_description']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Gateway Description', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['package_name'] ) ) {
	        if (  $input['package_name']  ) {
	            $output['package_name'] =  sanitize_textarea_field($input['package_name']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Package Name', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['enable_extra_f1'] ) ) {
	        if (  $input['enable_extra_f1']  ) {
	            $output['enable_extra_f1'] =  $input['enable_extra_f1'] ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enable Extra Field One', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['extra_f1'] ) ) {
	        if (  $input['extra_f1']  ) {
	            $output['extra_f1'] =  sanitize_text_field($input['extra_f1']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Extra Field One Label', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['enable_extra_f2'] ) ) {
	        if (  $input['enable_extra_f2']  ) {
	            $output['enable_extra_f2'] =  $input['enable_extra_f2'] ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enable Extra Field Two', $sslcom_quickpay_slug));
	        }
	    }

	    if ( isset( $input['extra_f2'] ) ) {
	        if (  $input['extra_f2']  ) {
	            $output['extra_f2'] =  sanitize_text_field($input['extra_f2']) ;
	        } else {
	            add_settings_error( 'sslcom_quickpay', 'plugin-error', esc_html__( 'Enter Extra Field Two', $sslcom_quickpay_slug));
	        }
	    }

	    return apply_filters( 'sslcom_quickpay', $output, $input );
	}
}