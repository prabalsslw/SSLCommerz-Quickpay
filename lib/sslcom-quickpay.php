<?php 
	require_once( SSLCDPATH . 'lib/sslcom-quickpay-api.php' );

	use Sslcommerz\Quickpay\API\Sslcommerz_Quickpay_Api;
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'sslcom_quickpay_payment';
	$quickpay_options = get_option( 'sslcom_quickpay' );

	if(isset($_POST['status']) && $_POST['status'] == "VALID")
	{
		if((isset($_POST['tran_id']) && $_POST['tran_id'] != "") && (isset($_POST['val_id']) && $_POST['val_id'] != "") && (isset($_POST['amount']) && $_POST['amount'] != ""))
		{
			$Sslcommerz_Quickpay_Api = new Sslcommerz_Quickpay_Api;

			$val_id 		= sanitize_text_field($_POST['val_id']);
			$tran_id 		= sanitize_text_field($_POST['tran_id']);

			$validate_data 	= $Sslcommerz_Quickpay_Api->SslcomValidatePayment($val_id);

            $results 		= $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$validate_data->tran_id' ", ARRAY_A);

            $customer_name 	= sanitize_text_field($_POST['value_c']);
			$service_name	= sanitize_text_field($_POST['value_d']);
			$tran_amount	= $validate_data->currency_amount;
			$tran_currency	= $validate_data->currency;

            if($results[0]['tran_status'] == 'Pending' && ($results[0]['total_amount'] == $validate_data->currency_amount))
            {
            	if ($validate_data->status == 'VALID' || $validate_data->status == 'VALIDATED') 
	    		{
	    			if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, card_type = %s, card_no = %s WHERE trxid = %s",'Processing', $validate_data->card_type."(".$validate_data->currency_type.")", $validate_data->card_no, $tran_id)))
	    			{
	    				$html = "<div class='sslcommerz-quickpay'>";
	    				$html .= "<h4>We have received your payment successfully.</h4><hr>";
	    				$html .= "<p><table class='table table-striped table-hover'>";
	    				$html .= "<tr><td class='text-right'><b>Transaction ID:</b> </td><td><b>$tran_id</b></td></tr>";
	    				$html .= "<tr><td class='text-right'><b>Name:</b> </td><td>$customer_name</td></tr>";
	    				$html .= "<tr><td class='text-right'><b>Service/Package/Product Name:</b> </td><td>$service_name</td></tr>";
	    				$html .= "<tr><td class='text-right'><b>Transaction Amount:</b> </td><td>$tran_amount</td></tr>";
	    				$html .= "<tr><td class='text-right'><b>Currency:</b> </td><td>$tran_currency</td></tr>";
	    				$html .= "<tr><td class='text-right'><b>Transaction Status:</b> </td><td>Processing</td></tr>";
	    				$html .= "</table></p>";
	    				$html .= "<div>";
	    				echo $html;
	    			}
	    			
	    		}
            }
            else
            {
            	$html = "<div class='sslcommerz-quickpay'>";
				$html .= "<h4>Somthing Wrong!</h4><hr>";
				$html .= "<div>";
				echo $html;
            }
		}
	}
	else if(isset($_POST['status']) && $_POST['status'] == "FAILED")
	{
		$tran_id 			= sanitize_text_field($_POST['tran_id']);
		$currency_amount 	= sanitize_text_field($_POST['currency_amount']);

        $results 			= $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$tran_id' ", ARRAY_A);

        $customer_name 	= sanitize_text_field($_POST['value_c']);
		$service_name	= sanitize_text_field($_POST['value_d']);
		$tran_amount	= sanitize_text_field($_POST['currency_amount']);
		$tran_currency	= sanitize_text_field($_POST['currency']);

        if($results[0]['tran_status'] == 'Pending' && ($results[0]['total_amount'] == $currency_amount))
        {
			if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s WHERE trxid = %s", 'Failed', $tran_id)))
			{
				$html = "<div class='sslcommerz-quickpay'>";
				$html .= "<h4>Sadly, your payment failed!</h4><hr>";
				$html .= "<p><table class='table table-striped table-hover'>";
				$html .= "<tr><td class='text-right'><b>Transaction ID:</b> </td><td><b>$tran_id</b></td></tr>";
				$html .= "<tr><td class='text-right'><b>Name:</b> </td><td>$customer_name</td></tr>";
				$html .= "<tr><td class='text-right'><b>Service/Package/Product Name:</b> </td><td>$service_name</td></tr>";
				$html .= "<tr><td class='text-right'><b>Transaction Amount:</b> </td><td>$tran_amount</td></tr>";
				$html .= "<tr><td class='text-right'><b>Currency:</b> </td><td>$tran_currency</td></tr>";
				$html .= "<tr><td class='text-right'><b>Transaction Status:</b> </td><td style='color:red;'>Failed</td></tr>";
				$html .= "</table></p>";
				$html .= "<div>";
				echo $html;
			}
        }
        else
        {
        	$html = "<div class='sslcommerz-quickpay'>";
			$html .= "<h4>Somthing Wrong!</h4><hr>";
			$html .= "<div>";
			echo $html;
        }
	}
	else if(isset($_POST['status']) && $_POST['status'] == "CANCELLED")
	{
		$tran_id = sanitize_text_field($_POST['tran_id']);

        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$tran_id' ", ARRAY_A);

        if($results[0]['tran_status'] == 'Pending')
        {
			if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s WHERE trxid = %s", 'Cancelled', $tran_id)))
			{
				$html = "<div class='sslcommerz-quickpay'>";
				$html .= "<h4>Your payment has been canceled!</h4><hr>";
				$html .= "<p><table class='table table-striped table-hover'>";
				$html .= "<tr><td class='text-right'><b>Transaction ID:</b> </td><td><b>$tran_id</b></td></tr>";
				$html .= "<tr><td class='text-right'><b>Transaction Status:</b> </td><td>Canceled</td></tr>";
				$html .= "</table></p>";
				$html .= "<div>";
				echo $html;
			}
        }
        else
        {
        	$html = "<div class='sslcommerz-quickpay'>";
			$html .= "<h4>Somthing Wrong!</h4><hr>";
			$html .= "<div>";
			echo $html;
        }
	}
	else if(isset($_POST['token']) && $_POST['token'] == "Initiate")
	{
		if(isset($_POST['sslcom_fullname']) && isset($_POST['sslcom_email']) && isset($_POST['sslcom_phone']) && isset($_POST['sslcom_address']) && isset($_POST['sslcom_service']) && isset($_POST['sslcom_amount']) && isset($_POST['sslcom_currency']) )
		{
			$Sslcommerz_Quickpay_Api = new Sslcommerz_Quickpay_Api;

			$tran_id 			= $Sslcommerz_Quickpay_Api->sslcommerQuickpayTranidGen();
			$customer_name 		= sanitize_text_field($_POST['sslcom_fullname']);
			$customer_email 	= sanitize_text_field($_POST['sslcom_email']);
			$customer_phone 	= sanitize_text_field($_POST['sslcom_phone']);
			$customer_address 	= sanitize_textarea_field($_POST['sslcom_address']);
			$service_name 		= sanitize_text_field($_POST['sslcom_service']);
			$total_amount 		= sanitize_text_field($_POST['sslcom_amount']);
			$currency 			= sanitize_text_field($_POST['sslcom_currency']);
			$sslcom_note 		= isset($_POST['sslcom_note']) ? sanitize_textarea_field($_POST['sslcom_note']) : '';
			$extra_f1 			= isset($_POST['extra_f1']) ? sanitize_text_field($_POST['extra_f1']) : '';
			$extra_f2 			= isset($_POST['extra_f2']) ? sanitize_text_field($_POST['extra_f2']) : '';

			$post_data = array();
			$post_data['store_id'] = $quickpay_options['storeid'];
			$post_data['store_passwd'] = $quickpay_options['storepassword'];
			$post_data['currency'] = $currency;
			$post_data['total_amount'] = str_replace(',', '', $total_amount);
			$post_data['tran_id'] = $tran_id;

			$post_data['cus_name'] = $customer_name;
			$post_data['cus_email'] = $customer_email;
			$post_data['cus_phone'] = $customer_phone;
			$post_data['cus_add1'] = $customer_address;
			$post_data['cus_country'] = "BD";
			$post_data['cus_city'] = "Dhaka";
			$post_data['cus_postcode'] = "1000";
			$post_data['product_category'] = $service_name;
			$post_data['product_name'] = $service_name;
			$post_data['product_profile'] = 'general';
			$post_data['shipping_method'] = 'No';
			$post_data['num_of_item'] = '1';

			if($extra_f1 !="")
			{
				$post_data['value_a'] = $extra_f1;
			}
			if($extra_f2 !="")
			{
				$post_data['value_b'] = $extra_f2;
			}
			
			$post_data['value_c'] = $customer_name;
			$post_data['value_d'] = $service_name;

			$post_data['success_url'] = get_permalink($quickpay_options['return_page']);
			$post_data['fail_url'] = get_permalink($quickpay_options['return_page']);
			$post_data['cancel_url'] = get_permalink($quickpay_options['return_page']);

			$apiResponse = $Sslcommerz_Quickpay_Api->EasyHostedRequest($post_data);

			if($apiResponse['status'] == "SUCCESS" && $apiResponse['GatewayPageURL'] != "" && $apiResponse['sessionkey'] != "")
			{
				if (isset($tran_id)) 
				{
					global $wpdb;
					$table_name = $wpdb->prefix . 'sslcom_quickpay_payment';
					$field_Data = array(
						'trxid' => $tran_id,
						'tran_status' => 'Pending',
						'total_amount' => $total_amount,
						'product_name' => $service_name,
						'cus_name' => $customer_name,
						'cus_email' => $customer_email,
						'cus_phone' => $customer_phone,
						'cus_address' => $customer_address,
						'cus_country' => 'ANY',
						'notes' => $sslcom_note,
						'cus_city' => 'ANY',
						'cus_postcode' => 'ANY',
						'extra_field1' => $extra_f1,
						'extra_field2' => $extra_f2,
					);
					$wpdb->insert($table_name, $field_Data);
					echo '<meta http-equiv="refresh" content="0; url=' . $apiResponse['GatewayPageURL'] . '" />';
					exit;
				}
			}
		}
		
	}
	else{
?>
<div class="sslcommerz-quickpay">
  	<form action="<?php the_permalink(); ?>" method="post">
  		<h3>General Information</h3><hr>
	    <p>
	    	<label for="name">Full Name: <span class="sslcom-required">*</span> </label><br>
	    	<input type="text" id="sslcom_fullname" class="sslcom-text-field" name="sslcom_fullname" placeholder="Type Your Full Name" required autofocus>
	    	<input type="hidden" name="token" value="Initiate">
	    </p>
	    <div class="sslc-inline">
		    <p class="half-email">
		    	<label for="name">Email: <span class="sslcom-required">*</span> </label><br>
		    	<input type="email" id="sslcom_email" class="sslcom-text-field" name="sslcom_email" placeholder="Type Your Email Address" required >
		    </p>
		    <p class="half-phone">
		    	<label for="name">Phone Number: <span class="sslcom-required">*</span> </label><br>
		    	<input type="text" id="sslcom_phone" class="sslcom-text-field" name="sslcom_phone" placeholder="Type Your Phone Number" required >
		    </p>
		</div>
	    <p>
	    	<label for="name">Address: <span class="sslcom-required">*</span> </label><br>
	    	<textarea name="sslcom_address" id="sslcom_address" rows="2" class="sslcom-text-area" placeholder="Type Your Full Address" required ></textarea>
	    </p>
	    <?php
	    	if(isset($quickpay_options['enable_extra_f1']) && $quickpay_options['enable_extra_f1'] == 1)
	    	{ ?>
	    		<p>
			    	<label for="name"><?php if($quickpay_options['enable_extra_f1'] == 1) {echo $quickpay_options['extra_f1']; } ?>: <span class="sslcom-required">*</span> </label><br>
			    	<input type="text" id="extra_f1" class="sslcom-text-field" name="extra_f1" placeholder="Type Your <?php if($quickpay_options['enable_extra_f1'] == 1) {echo $quickpay_options['extra_f1']; } ?>" required >
			    </p>
	    <?php 
	    	}
	    ?>
	    <?php
	    	if(isset($quickpay_options['enable_extra_f2']) && $quickpay_options['enable_extra_f2'] == 1)
	    	{ ?>
	    		<p>
			    	<label for="name"><?php if($quickpay_options['enable_extra_f2'] == 1) {echo $quickpay_options['extra_f2']; } ?>: <span class="sslcom-required">*</span> </label><br>
			    	<input type="text" id="extra_f2" class="sslcom-text-field" name="extra_f2" placeholder="Type Your <?php if($quickpay_options['enable_extra_f2'] == 1) {echo $quickpay_options['extra_f2']; } ?>" required >
			    </p>
	    <?php 
	    	}
	    ?>
	    <h3>Payment Information</h3><hr>
	    <p>
	    	<label for="name">Package/Service/Product <span class="sslcom-required">*</span> </label><br>
	    	<select name="sslcom_service" id="sslcom_service" class="sslcom-text-field" required>
	    		<option value="">Select Package/Service/Product</option>
	    		<?php
	    			$sslcom_package = explode(",", $quickpay_options['package_name']);
	    			foreach ($sslcom_package as $packagename) {
	    				echo "<option value='".trim($packagename)."'>".trim($packagename)."</option>";
	    			}
	    		?>
	    	</select>
	    </p>
	    <div class="sslc-inline">
		    <p class="half-email">
		    	<label for="name">Amount <span class="sslcom-required">*</span> </label><br>
		    	<input type="number" id="sslcom_amount" class="sslcom-text-field" name="sslcom_amount" placeholder=" Type Amount" min="10" required >
		    </p>
		    <p class="half-phone">
		    	<label for="name">Currency <span class="sslcom-required">*</span> </label><br>
		    	<select name="sslcom_currency" id="sslcom_currency" class="sslcom-text-field" required>
		    		<option value="BDT" selected>BDT</option>
					<option value="EUR">EUR</option>
					<option value="GBP">GBP</option>
					<option value="AUD">AUD</option>
					<option value="USD">USD</option>
					<option value="CAD">CAD</option>
		    	</select>
		    </p>
		</div>
	    <p>
	    	<label for="name">Note: </label><br>
	    	<textarea name="sslcom_note" id="sslcom_note" rows="2" class="sslcom-text-area" placeholder="Type Your Note, If Have Any" ></textarea>
	    </p>
	    <h3>Payment Method</h3><hr>
	    <p>
			<div class="paymentclass">
				<label>
					<img class="imgclass" src="<?php echo SSLCDURL.'/images/SSLCommerz.png'; ?>">
					<h5 class="pyment-radio"><input type="radio" checked="checked" value="sslcommerz" name="payment-mode">
						<?php echo '&nbsp;' . $quickpay_options['sslcom_title']; ?>
					</h5><hr>
					<p><?php echo $quickpay_options['sslcom_description']; ?></p>
				</label>
			</div>
	    </p>
	    <p>
	    	<label for="checkbox">
	    		<input type="checkbox" id="terms_cond" name="terms" required > By clicking Proceed, you agreed to our <a href="#" target="new">Terms &amp; Condition</a>, <a href="#" target="new">Privacy Policy</a> and <a href="#" target="new">Return Policy</a>
	    	</label>
	    </p>
	    <p>
	    	<input class="paybtn" type="submit" value="Proceed To Pay">
	    </p>

  	</form>
</div>
<?php } 
?>

