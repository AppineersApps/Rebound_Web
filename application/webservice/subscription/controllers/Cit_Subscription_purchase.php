<?php

   
/**
 * Description of Subscription Purchase Extended Controller
 * 
 * @module Extended Subscription Purchase
 * 
 * @class Cit_Subscription_purchase.php
 * 
 * @path application\webservice\master\controllers\Cit_Subscription_purchase.php
 * 
 * @author CIT Dev Team
 * 
 * @date 09.05.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Subscription_purchase extends Subscription_purchase {
        public function __construct()
{
    parent::__construct();
}
public function validateReceiptCheck($input_params=array()){



    //fetching the file contents
    $sample_json = file_get_contents($_FILES['receipt_data']['tmp_name']);

    //$applesharedsecret = '76238eb83ebb44bebfebe4c66999050a';
    //$appleurl ='https://sandbox.itunes.apple.com/verifyReceipt';
     $applesharedsecret     = $this->config->item("SUBSCRIPTION_PASSWORD");
    $appleurl              = $this->config->item("SUBSCRIPTION_ITUNES_URL");
    //https://buy.itunes.apple.com/verifyReceipt //for production
/*
    echo "applesharedsecret--".$applesharedsecret."---";
    echo "appleurl--".$appleurl."---";
     var_dump($sample_json);
    exit;*/

    $request = json_encode(array("receipt-data" => $sample_json,"password"=>$applesharedsecret));
    // setting up the curl

    $ch = curl_init($appleurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $jsonresult = curl_exec($ch);


/*    print_r($jsonresult);
    exit;

    */

    $err = curl_error($ch);
    curl_close($ch);
    if($err){
        $return_arr[0]['transaction_id'] = '';
        $return_arr[0]['expiry_date']    = '';
        $return_arr[0]['success']        = 0;
        $return_arr[0]['message']        = $err;
        $return_arr[0]['receipt_data_v1']   ="";
    }else{
        $decoded_json = json_decode($jsonresult);

        $return_arr =array();
        if(!empty($decoded_json->latest_receipt_info)) {
            
            $expires_date = "";
            $transaction_id = "";
            $product_id = "";

            foreach ($decoded_json->latest_receipt_info as $value) {
                 $gmt_date       = $value->expires_date;
                 $date1 = explode(' ',$gmt_date);
                 $expiry_date_curr = $date1[0]." ".$date1[1];
                 
                 if($expires_date == "")
                 {
                    $expires_date = $expiry_date_curr;
                    $transaction_id = $value->transaction_id;
                    $product_id = $value->product_id;
                 }
                 if(strtotime($expiry_date_curr) > strtotime($expires_date))
                 {
                    $expires_date = $expiry_date_curr;
                    $transaction_id = $value->transaction_id;
                    $product_id = $value->product_id;
                 }

                 
                 break;

            }


            if(strtotime($expires_date) < (date("Y-m-d H:i:s")))
            {

                
                $return_arr[0]['success']        = 0;
                $return_arr[0]['message']        = 'Your subscription is expired. Please subscribe again to use app features.';    
            }else
            {

                
                $return_arr[0]['success']        = 1;
                $return_arr[0]['message']        = 'Subscription purchased successfully.'; 
            }


            
            
            $return_arr[0]['transaction_id'] = $transaction_id;
            $return_arr[0]['product_id'] = $product_id;
            $return_arr[0]['expiry_date']    = $expires_date;
            $return_arr[0]['receipt_data_v1']   =$sample_json;
        }else{
          
            $return_arr[0]['transaction_id'] = $transaction_id;
            $return_arr[0]['expiry_date']    = $expiry_date;
            $return_arr[0]['success']        = 0;
            $return_arr[0]['message']        = 'Something went wrong, Please try again!';
             $return_arr[0]['receipt_data_v1']  ="";
        }
    }
    return $return_arr;
    
}
public function subscriptionDetails($input_params = array())
{
    
    $result_arr['success_v1'] = 0;
    $result_arr['expiry_date_v1'] = '';
  
    $user_id        = $input_params['user_id'];
    $packageName    = $this->config->item('PACKAGE_NAME');
    $subscriptionId = $input_params['subscription_id'];
    $purchase_token = $input_params['purchase_token'];
   
    // Including the third_party
    require_once APPPATH.'third_party/vendor/autoload.php';        
    putenv("GOOGLE_APPLICATION_CREDENTIALS=".FCPATH."tyst-private_google_api_key.json");
   
    /************************************************
      Make an API request authenticated with a service
      account.
     ************************************************/
     
    $client = new Google_Client();
   
   
    // set the location manually
    $client->setAuthConfig(getenv('GOOGLE_APPLICATION_CREDENTIALS'));
    
    $client->setApplicationName("Client_Library_Examples");
    
    $client->setScopes(['https://www.googleapis.com/auth/androidpublisher']);

    // Your redirect URI can be any registered URI, but in this example
    // we redirect back to this same page
   
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
   
    $client->setRedirectUri($redirect_uri);

    // returns a Guzzle HTTP Client
    $httpClient = $client->authorize();
   

    $AndroidPublisher = new Google_Service_AndroidPublisher($client);
   
   try
   {
    $getData = $AndroidPublisher->purchases_subscriptions->get($packageName, $subscriptionId, $purchase_token, $optParams = array());
   }catch(Exception $e)
   {
      $getData = array();
   }
   
   
    if(!empty($getData))
    {
        $seconds = $getData['expiryTimeMillis'] / 1000;
        $expiryTimeMillis = date("Y-m-d", $seconds);
        $result_arr['success_v1'] = "1";
        $result_arr['expiry_date_v1'] = $expiryTimeMillis;
    }
    
    return $result_arr;
}
}
