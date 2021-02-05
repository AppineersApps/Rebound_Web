<?php
  

/**
 * Description of check_subscription_status_v1 Extended Controller
 * 
 * @module Extended check_subscription_status_v1
 * 
 * @class Cit_Check_subscription_status_v1.php
 * 
 * @path application
otification\master\controllers\Cit_Check_subscription_status_v1.php
 * 
 * @author CIT Dev Team
 * 
 * @date 27.04.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Check_subscription_status_v1 extends Check_subscription_status_v1 {
        public function __construct()
{
    parent::__construct();
}
public function checkSubscription($input_params = array()){
    $return_arr =array();   
    $return_arr['success'] = '0';



  
    if(!empty($input_params['fetch_the_subscribed_users'])) {
        foreach($input_params['fetch_the_subscribed_users'] as $data) {
              if($data['u_receipt_type']=='ios'){
                    $upload_url = $this->config->item('upload_url'); // upload url
                    $expiry_date  = $data['u_expiry_date'];
                    $subscriptionId  = trim($data['u_subscription_id']);
                    // fetch the current timezone
                    $current_timezone = date_default_timezone_get();
                    // convert the current timezone to UTC
                    date_default_timezone_set('UTC');
                    $current_date = date("Y-m-d H:i:s");
                    // Again coverting into local timezone
                    date_default_timezone_set($current_timezone);

                    if($subscriptionId=='com.app.Rebound.7days'){

	                    if(strtotime($current_date) > strtotime($expiry_date)) {
	                    	    $is_subscribed = '0';
                                $array = array('eIsSubscribed'=>$is_subscribed);
                                $this->db->where('iUserId',$data['u_user_id'] );
                                $this->db->update('users',$array);
                                $return_arr['success'] = '1';

	                    }
                    
                    }
                    else if(strtotime($current_date) > strtotime($expiry_date)) {
                        $sample_json           = $data['u_receipt_data'];
                        $applesharedsecret     = $this->config->item("SUBSCRIPTION_PASSWORD");
                        $appleurl              = $this->config->item("SUBSCRIPTION_ITUNES_URL");
                        //https://buy.itunes.apple.com/verifyReceipt //for production
                        $request = json_encode(array("receipt-data" => $sample_json,"password"=>$applesharedsecret));
                        // setting up the curl
                        $ch = curl_init($appleurl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                        $jsonresult = curl_exec($ch);
                        curl_close($ch);
                        $decoded_json = json_decode($jsonresult);
                       
                        if(!empty($decoded_json->latest_receipt_info)) {
                            $expires_date = "";
                            $transaction_id = "";
                            $new_receipt_data=$decoded_json->latest_receipt;
                            
                            foreach ($decoded_json->latest_receipt_info as $value) {
                                 $gmt_date       = $value->expires_date;
                               
                                 $date1 = explode(' ',$gmt_date);
                                 $expiry_date_curr = $date1[0]." ".$date1[1];  
                                 
                                 if(true==empty($expires_date))
                                 {
                                    $expires_date = $expiry_date_curr;
                                    $transaction_id = $value->transaction_id;
                                 }
                                 if(strtotime($expiry_date_curr) > strtotime($expires_date))
                                 {
                                    $expires_date = $expiry_date_curr;
                                    $transaction_id = $value->transaction_id;
                                 }

                             
                                 break;
                            }
                            $return_arr['success'] = '0';


                            if(strtotime($expiry_date) >= strtotime($expires_date)) {

                                $is_subscribed = '0';
                                $array = array('eIsSubscribed'=>$is_subscribed);
                                $this->db->where('iUserId',$data['u_user_id'] );
                                $this->db->update('users',$array);
                                $return_arr['success'] = '1';
                            } else {

                               if((!empty($expires_date)) && (!empty($new_receipt_data))){
                                $is_subscribed = '1';
                                $array = array('eIsSubscribed'=>$is_subscribed,'dtExpiryDate'=>$expires_date,'iTransactionId'=>$transaction_id,'tReceiptData'=>$new_receipt_data);
                                $this->db->where('iUserId',$data['u_user_id'] );
                                $this->db->update('users',$array);
                                $return_arr['success'] = '1';
                                }

                            }

                           
                        }//end decode jeson

                        
             }//end else if
           
         }



             else if($data['u_receipt_type']=='android'){
                    $user_id        = $data['u_user_id'];
                    $expiry_date    = strtotime($data['u_expiry_date']);
                    $current_date   = strtotime('now');
                    $is_subscribed  = $data['u_e_one_time_transaction'];
                    $packageName    = $this->config->item('PACKAGE_NAME');
                    $subscriptionId = $data['u_subscription_id'];
                    $purchase_token = $data['u_purchase_token'];
                    
                    if($purchase_token != '' && $purchase_token != null)
                    {
                        
                      
                        if($current_date > $expiry_date)
                        {
                            
                            // Including the third_party
                           require_once APPPATH.'third_party/vendor/autoload.php';        
                           putenv("GOOGLE_APPLICATION_CREDENTIALS=".FCPATH."tyst-private_google_api_key.json");
                
                            // echo pageHeader("Service Account Access");
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
                           
                            $getData = $AndroidPublisher->purchases_subscriptions->get($packageName, $subscriptionId, $purchase_token, $optParams = array());
                         
                            if(!empty($getData['paymentState']))
                            {
                                
                                if($getData['paymentState'] != "0")
                                {
                                    $seconds = $getData['expiryTimeMillis'] / 1000;
                                    $expiryTimeMillis = date("Y-m-d", $seconds);
                                    $data = array( 
                                        'users.eOneTimeTransaction' => "Yes", 
                                        'users.dtExpiryDate'   => $expiryTimeMillis, 
                                    );
                
                                    $this->db->where('iUserId',$user_id);
                                    $this->db->update('users',$data);
                                    $affected_rows = $this->db->affected_rows();
                                    if($affected_rows > 0)
                                    {
                                        $return_arr['success'] = '1';
                                    }
                                    else
                                    {
                                        $return_arr['success'] = '0';
                                    }
                                }
                            }
                            else
                            {
                                $seconds = $getData['expiryTimeMillis'] / 1000;
                                $expiryTimeMillis = date("Y-m-d", $seconds);
                
                                $data = array( 
                                    'users.eOneTimeTransaction' => "No", 
                                    'users.dtExpiryDate'   => $expiryTimeMillis, 
                                );
                
                                $this->db->where('iUserId',$user_id);
                                $this->db->update('users',$data);
                                $affected_rows = $this->db->affected_rows();
                
                                if($affected_rows > 0)
                                {
                                    $return_arr['success'] = '1';
                                }
                                else
                                {
                                    $return_arr['success'] = '0';
                                }
                            }
                        }   
                    }
                    else
                    {
                        if($current_date > $expiry_date)
                        {
                            $data = array( 
                                    'users.eOneTimeTransaction' => "No", 
                            );
            
                            $this->db->where('iUserId',$user_id);
                            $this->db->update('users',$data);
                            $affected_rows = $this->db->affected_rows();
                            if($affected_rows > 0)
                            {
                                $return_arr['success'] = '1';
                            }
                            else
                            {
                                $return_arr['success'] = '0';  
                            }
                        }
                    }
                 
             }
             
        }
    }
    return $return_arr;
}
}
