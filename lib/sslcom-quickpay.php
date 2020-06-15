<?php 
	require_once( SSLCDPATH . 'lib/sslcom-quickpay-api.php' );

	use Sslcommerz\Quickpay\API\Sslcommerz_Quickpay_Api;
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'sslcom_quickpay_payment';
	$quickpay_options = get_option( 'sslcom_quickpay' );

	if(isset($quickpay_options['enable_sandbox']) && $quickpay_options['enable_sandbox'] != ""){
		$js_api_url 	= "https://sandbox.sslcommerz.com/embed.min.js";
		$sandbox 		= "yes";
	}
	else{
		$js_api_url 	= "https://seamless-epay.sslcommerz.com/embed.min.js";
		$sandbox 		= "no";
	}

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
	    <p>
	    	<div id="errorMsg"></div>
	    </p>
	    
	    <?php 
	    	if(isset($quickpay_options['enable_popup']) && $quickpay_options['enable_popup'] != "")
	    	{
	    		$Sslcommerz_Quickpay_Api = new Sslcommerz_Quickpay_Api;
				$tran_id = $Sslcommerz_Quickpay_Api->sslcommerQuickpayTranidGen();
	    ?>
	    		<button class="your-button-class" id="sslczPayBtn"
					token="<?php echo $tran_id; ?>"
					postdata=""
					order="<?php echo $tran_id; ?>"
					endpoint="<?php echo get_site_url(); ?>/sslcommerzQuickpay.php?sslcomcheckout"> Proceed to Pay
				</button>
	   	<?php
	    	}else{
	    ?>
	    <p><input type="submit"></p>
	    <?php
	    	}
	    ?>
  	</form>
</div>
<?php } 
?>

<script type="text/javascript">
	(function (window, document) {
		var loader = function () {
		    var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
		    script.src = "<?php echo $js_api_url; ?>?" + Math.random().toString(36).substring(7);
		    tag.parentNode.insertBefore(script, tag);
		};

		window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
	})(window, document);
	
	function changeObj() {
        var obj = {};

        var sslcom_fullname = document.getElementById("sslcom_fullname").value;
        var sslcom_email = document.getElementById("sslcom_email").value;
        var sslcom_phone = document.getElementById("sslcom_phone").value;
        var sslcom_address = document.getElementById("sslcom_address").value;

        if(typeof(document.getElementById("extra_f1")) != 'undefined' && document.getElementById("extra_f1") != null){
        	var extra_f1 = document.getElementById("extra_f1").value;
    	} else{
    		extra_f1 = '';
    	}
    	if(typeof(document.getElementById("extra_f2")) != 'undefined' && document.getElementById("extra_f2") != null){
        	var extra_f2 = document.getElementById("extra_f2").value;
    	} else{
    		extra_f2 = '';
    	}
        
        var sslcom_service = document.getElementById("sslcom_service").value;
        var sslcom_amount = document.getElementById("sslcom_amount").value;
        var sslcom_currency = document.getElementById("sslcom_currency").value;
        var sslcom_note = document.getElementById("sslcom_note").value;

        if(sslcom_fullname !='' && sslcom_email !='' && sslcom_phone !='' && sslcom_address !='' && sslcom_service !='' && sslcom_amount !='' && sslcom_currency !='') 
        {
            var obj = {  "sslcom_fullname": sslcom_fullname, "sslcom_email": sslcom_email, "sslcom_phone": sslcom_phone, "sslcom_address": sslcom_address, "extra_f1": extra_f1, "extra_f2": extra_f2, "sslcom_service": sslcom_service, "sslcom_amount": sslcom_amount, "sslcom_currency": sslcom_currency, "sslcom_note": sslcom_note };
            document.getElementById("errorMsg").innerHTML = "Ok Now";
        }
        else
        {
        	if(sslcom_fullname == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "<span>Please Enter Full Name!</span>";
        	}
        	else if(sslcom_email == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Email Address!";
        	}
        	else if(sslcom_phone == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Phone Number!";
        	}
        	else if(sslcom_address == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Address!";
        	}
        	else if(typeof(document.getElementById("extra_f1")) != 'undefined' && document.getElementById("extra_f1") != null && document.getElementById("extra_f1").value == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Extra Field 1!";
        	}
        	else if(typeof(document.getElementById("extra_f2")) != 'undefined' && document.getElementById("extra_f2") != null && document.getElementById("extra_f2").value == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Extra Field 2!";
        	}
        	else if(sslcom_service == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Select One Package/Service/Product!";
        	}
        	else if(sslcom_amount == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Enter Amount More Than 10 TK!";
        	}
        	else if(sslcom_currency == '')
        	{
        		document.getElementById("errorMsg").innerHTML = "Please Select Currency!";
        	}
        }

        var x = document.getElementById("sslczPayBtn").getAttribute("postdata").value = obj;
        console.log(x);
    }
    changeObj();
	document.getElementById("sslcom_fullname").onchange = function() {changeObj()};
	document.getElementById("sslcom_email").onchange = function() {changeObj()};
	document.getElementById("sslcom_phone").onchange = function() {changeObj()};
	document.getElementById("sslcom_address").onchange = function() {changeObj()};
	if(typeof(document.getElementById("extra_f1")) != 'undefined' && document.getElementById("extra_f1") != null){
    	document.getElementById("extra_f1").onchange = function() {changeObj()};
	}
	if(typeof(document.getElementById("extra_f2")) != 'undefined' && document.getElementById("extra_f2") != null){
    	document.getElementById("extra_f2").onchange = function() {changeObj()};
	}
	document.getElementById("sslcom_service").onchange = function() {changeObj()};
	document.getElementById("sslcom_amount").onchange = function() {changeObj()};
    document.getElementById("sslcom_currency").onchange = function() {changeObj()};
	document.getElementById("sslcom_note").onchange = function() {changeObj()};
    
</script>