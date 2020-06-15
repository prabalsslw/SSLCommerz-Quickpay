<?php 

namespace Sslcommerz\Quickpay\API;

class Sslcommerz_Quickpay_Api 
{
	public $quickpay_options;
	public $api_url;
	public $validation_url;
	public $sandbox;

	public function __construct()
    {
		$this->quickpay_options 	= get_option( 'sslcom_quickpay' );

		if(isset($this->quickpay_options['enable_sandbox']) && $this->quickpay_options['enable_sandbox'] != ""){
			$this->api_url 			= "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
			$this->validation_url 	= "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";
			$this->sandbox 			= "yes";
		}
		else{
			$this->api_url 			= "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
			$this->validation_url 	= "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";
			$this->sandbox 			= "no";
		}
    }
	public function EasyHostedRequest($post_data)
	{
		$response = $this->CallToApi($post_data);
		return array('status' => $response['status'], 'GatewayPageURL' => $response['GatewayPageURL'], 'sessionkey' => $response['sessionkey']);
	}

	public function EasyPopupRequest()
	{
		
	}

	public function SslcomValidatePayment($val_id)
	{
		$validation_req_url = $this->validation_url."?val_id=" . $val_id . "&store_id=" . $this->quickpay_options['storeid'] . "&store_passwd=" . $this->quickpay_options['storepassword'] . "&v=1&format=json";
		$result = wp_remote_post(
			$validation_req_url,
			array(
				'method'      => 'GET',
				'timeout'     => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array(),
				'cookies'     => array(),
			)
		);

		if($result['response']['code'] == 200)
		{
            $result = json_decode($result['body']);
            return $result;
        }
        else
		{
			if ( is_wp_error( $response ) ) {
				echo $response->get_error_message();
			}
			return "FAILED TO CONNECT WITH VALIDATION API.";
			exit;
		}
	}

	public function CallToApi($post_data)
	{
		$response = wp_remote_post( $this->api_url, array(
		    'method'      => 'POST',
			'timeout'     => 30,
			'redirection' => 10,
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $post_data,
			'cookies'     => array(),
		    )
		);

		if(empty($this->quickpay_options['enable_popup']))
		{
			if($response['response']['code'] == 200)
			{
				$sslcz = json_decode($response['body'], true);
				if ($sslcz['status'] == 'FAILED') {
	                return "FAILED TO CONNECT WITH SSLCOMMERZ API. Failed Reason: " . $sslcz['failedreason'];
	                exit;
	            }
	            else
	            {
	            	return $sslcz;
	            	exit;
	            }
			}
			else
			{
				if ( is_wp_error( $response ) ) {
					echo $response->get_error_message();
				}
				return "FAILED TO CONNECT WITH SSLCOMMERZ API. Error Code: ".$response['response']['code'];
				exit;
			}
		}
		else if(isset($this->quickpay_options['enable_popup']) && $this->quickpay_options['enable_popup'] != '' )
		{
			if($response['response']['code'] == 200)
			{
				$sslcz = json_decode($response['body'], true);
				
				if ($sslcz['status'] == 'FAILED') {
		            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
		            echo "<br/>Failed Reason: " . $sslcz['failedreason'];
		            exit;
		        }
		        else
		        {
		        	if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") {
						if($this->sandbox == "no")
						{
							echo json_encode(['status' => 'SUCCESS', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
							exit;
						}
						else if($this->sandbox == "yes")
						{
							echo json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
							exit;
						}
					} 
					else {
					   	echo json_encode(['status' => 'FAILED', 'data' => null, 'message' => $sslcz['failedreason'] ]);
					}
		        }
			}
			else
			{
				if ( is_wp_error( $response ) ) {
					echo $response->get_error_message();
				}
				echo "Error Code: ".$response['response']['code'];
				echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
				exit;
			}                       
		}
	}

	public function sslcommerQuickpayTranidGen($length = 8) {
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return "EPAY".$randomString;
	}
}