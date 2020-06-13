<?php 
	$quickpay_options = get_option( 'sslcom_quickpay' );
	// print_r($quickpay_options);
?>
<div class="sslcommerz-quickpay">
  	<form action="<?php the_permalink(); ?>" method="post">
  		<h3>General Information</h3><hr>
	    <p>
	    	<label for="name">Full Name: <span class="sslcom-required">*</span> </label><br>
	    	<input type="text" class="sslcom-text-field" name="sslcom_fullname" placeholder="Type Your Full Name" required >
	    </p>
	    <p>
	    	<label for="name">Email: <span class="sslcom-required">*</span> </label><br>
	    	<input type="email" class="sslcom-text-field" name="sslcom_email" placeholder="Type Your Email Address" required >
	    </p>
	    <p>
	    	<label for="name">Phone Number: <span class="sslcom-required">*</span> </label><br>
	    	<input type="text" class="sslcom-text-field" name="sslcom_phone" placeholder="Type Your Phone Number" required >
	    </p>
	    <p>
	    	<label for="name">Address: <span class="sslcom-required">*</span> </label><br>
	    	<textarea name="sslcom_address" rows="2" class="sslcom-text-area" placeholder="Type Your Full Address" required ></textarea>
	    </p>
	    <h3>Payment Information</h3><hr>
	    <p>
	    	<label for="name">Package/Service/Product <span class="sslcom-required">*</span> </label><br>
	    	
	    	<select name="sslcom_service" class="sslcom-text-field">
	    		<option value="">Select Package/Service/Product</option>
	    		<?php
	    			$sslcom_package = explode(",", $quickpay_options['package_name']);
	    			foreach ($sslcom_package as $packagename) {
	    				echo "<option value='".trim($packagename)."'>".trim($packagename)."</option>";
	    			}
	    		?>
	    	</select>
	    </p>
	    <p><input type="submit"></p>
  	</form>
</div>