<?php
    require_once( SSLCDPATH . 'lib/sslcom-quickpay-api.php' );

    use Sslcommerz\Quickpay\API\Sslcommerz_Quickpay_Api;

    global $wpdb;
    $table_name = $wpdb->prefix . 'sslcom_quickpay_payment';
    $quickpay_options = get_option( 'sslcom_quickpay' );

    $store_id = $quickpay_options['storeid'];
    $store_passwd = $quickpay_options['storepassword'];
    $Sslcommerz_Quickpay_Api = new Sslcommerz_Quickpay_Api;

    if($quickpay_options['enable_sandbox'] == 1)
    {
        $valid_url = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php';
    }
    else
    {
        $valid_url = 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';
    }

    if(isset($_POST['status']) && $_POST['status'] != "")
    {
        if($_POST['status'] == 'VALIDATED' || $_POST['status'] == 'VALID')
        {
            if(isset($_POST['val_id']) || isset($_POST['tran_id']))
            {
                $val_id = $_POST['val_id'];
                $tran_id = $_POST['tran_id'];
                $gw_data  = $Sslcommerz_Quickpay_Api->SslcomValidatePayment($val_id);

                $card_type = $gw_data->card_type;
                $currency_amount = $gw_data->currency_amount;
                $amount = $gw_data->amount;
                $currency_type = $gw_data->currency_type;

                $results = $wpdb->get_results("SELECT * FROM $table_name WHERE trxid = '$tran_id' ", ARRAY_A);

                if($results[0]['total_amount'] == trim($currency_amount))
                { 
                    if($gw_data->status =='VALIDATED' || $gw_data->status =='VALID') 
                    { 
                        if($results[0]['tran_status'] == 'Pending')
                        {
                            if($_POST['card_type'] != "")
                            {           
                                $ipn = 'IPN Triggered: Success';           
                                $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s,card_type = %s, ipn_status = %s WHERE trxid = %s",'Processing', $card_type."(".$currency_type.")", $ipn, $_POST['tran_id']));

                                $msg =  "Hash Validation Success.";
                            }
                            else
                            {
                                $msg =  "Card Type Empty or Mismatched";
                            }
                        }
                        else
                        {
                            $msg = "Payment already Processing.";
                        }
                    }
                    else
                    {
                        $msg=  "Status not ".$gw_data->status;
                    }
                }
                else
                {
                    $msg =  "Your Paid Amount is Mismatched.";
                }
            }
        }
        elseif($_POST['status'] == 'FAILED')
        {
            $ipn = 'IPN Triggered: Failed';           
            $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, ipn_status = %s WHERE trxid = %s",'Failed', $ipn, $_POST['tran_id']));
        }
        elseif($_POST['status'] == 'CANCELLED')
        {
            $ipn = 'IPN Triggered: CANCELLED';           
            $wpdb->query( $wpdb->prepare("UPDATE $table_name SET tran_status = %s, ipn_status = %s WHERE trxid = %s",'Cancelled', $ipn, $_POST['tran_id']));
        }
    }
    else
    {
        $msg =  "No IPN Request Received.";
    }

    echo $msg;
?>