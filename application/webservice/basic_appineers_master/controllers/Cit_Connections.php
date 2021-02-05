<?php
/**
 * Description of Resend Otp Extended Controller
 * 
 * @module Extended Resend Otp
 * 
 * @class Cit_Resend_otp.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Resend_otp.php
 * 
 * @author CIT Dev Team
 * 
 * @date 18.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Connections extends Connections {
  public function __construct()
  {
      parent::__construct();
  }

  public function checkConnectionExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("users_connections AS usc");
          $this->db->select("usc.iConnectionId AS conn_id");
          $this->db->select("usc.eConnectionType AS conn_type");
          $this->db->where_in("iConnectionUserId", $input_params['connection_user_id']);
          $this->db->where_in("iUserId", $input_params['user_id']);
          
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkconnectionexist']['0']['message']="No connection available";
            $return_arr['checkconnectionexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['connection_id']=$review_data[0]['conn_id'];
            $return_arr['conn_type']=$review_data[0]['conn_type'];
          } 
      }
      return $return_arr;
    
  }

    public function checkOtherConnectionExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("user_connections AS usc");
          $this->db->select("usc.iConnectionId AS conn_id");
          $this->db->select("usc.eConnectionType AS conn_type");
          $this->db->select("usc.eConnectionResult AS conn_result");
          $this->db->where_in("iUserId", $input_params['connection_user_id']);
          $this->db->where_in("iConnectionUserId", $input_params['user_id']);
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkotherconnectionexist']['0']['message']="No connection available";
            $return_arr['checkotherconnectionexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['old_connection_type']=$review_data[0]['conn_type'];
            $return_arr['old_connection_result']=$review_data[0]['conn_result'];
            $return_arr['connection_id']=$review_data[0]['conn_id'];
          } 
      }
      return $return_arr;
  }

  public function checkBlockExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("user_block AS usb");
          $this->db->select("usb.iBlockId AS block_id");
          $this->db->where_in("iUserId", $input_params['user_id']);
          $this->db->where_in("iConnectionUserId", $input_params['connection_user_id']);
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkblockexist']['0']['message']="No connection available";
            $return_arr['checkblockexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['block_id']=$review_data[0]['block_id'];
          } 
      }
      return $return_arr;
  }

  public function PrepareHelperMessage($input_params=array()){
    // print_r($input_params);exit;
    $this->db->select('nt.tMessage');
    $this->db->from('mod_push_notify_template as nt');
    $strConnectionType = (isset($input_params['connection_result']) && $input_params['connection_result'] == 'Match') ?  'matched_user':'like_user' ;
    $this->db->where('nt.vTemplateCode',$strConnectionType);
    $notification_text=$this->db->get()->result_array();
    $notification_text=$notification_text[0]['tMessage'];

    $notification_text = str_replace("|sender_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['s_name']), $notification_text);
    $return_array['notification_message']=$notification_text;
   // print_r($return_array);exit;
    return $return_array;
    
}

public function returnConfigParams(&$input_params=array()){
     $return_arr['terms_conditions_updated']='';
     $return_arr['privacy_policy_updated']  ='';

     $return_arr['log_status_updated']  ='';
     $return_arr['premium_status']  ='';
    //check for login user 
    
    $auth_header = $this->input->get_request_header('AUTHTOKEN');

    if ($auth_header != "") {
       $req_token = $auth_header;
    } else {
         $req_token = $input_params['user_access_token'];
     }
    if($req_token)
     {
                
                $access = $req_token;
                $this->db->select('iUserId');
                $this->db->from('users');
                $this->db->where('vAccessToken',$access);
                $this->db->where('eStatus','Active');
                $result = $this->db->get()->result_array();
                $userid = $result[0]['iUserId']; 
                   
     }
    if(!empty($userid)){
        $return_arr['terms_conditions_updated']=1;
        $return_arr['privacy_policy_updated']  =1;
        $this->db->select('vTermsConditionsVersion,vPrivacyPolicyVersion,eLogStatus,eIsSubscribed');
        $this->db->from('users');
        $this->db->where('iUserId',$userid);
        $version_data=$this->db->get()->row_array();
        $terms_conditions_version=$version_data['vTermsConditionsVersion'];
        $privacy_policy_version  =$version_data['vPrivacyPolicyVersion'];
        $return_arr['log_status_updated']=$version_data['eLogStatus']; 
        $return_arr['premium_status']=$version_data['eIsSubscribed']; 
       
    }
   //terms and conditions
    $this->db->select('vVersion,vPageCode');
    $this->db->from('mod_page_settings');
    $this->db->where_in('vPageCode',termsconditions);
    $termsconditions_code_version=$this->db->get()->row_array();
    //privacy policy 
    $this->db->select('vVersion,vPageCode');
    $this->db->from('mod_page_settings');
    $this->db->where_in('vPageCode',privacypolicy);
    $privacypolicy_code_version=$this->db->get()->row_array();
    if($privacy_policy_version==$privacypolicy_code_version['vVersion']){
        $return_arr['privacy_policy_updated']=0;
    }
    if($terms_conditions_version==$termsconditions_code_version['vVersion']){
        $return_arr['terms_conditions_updated']=0;
    }
    
     //end 
    $message = $this->config->item('VERSION_CHECK_MESSAGE');
    $app_name=$this->config->item('COMPANY_NAME');
    if($this->config->item('VERSION_UPDATE_CHECK')=='Enabled'){
        $return_arr['version_update_check']=1;
    }else{
        $return_arr['version_update_check']=0;
    }
    if($this->config->item('VERSION_UPDATE_OPTIONAL')=='Enabled'){
        $return_arr['version_update_optional']=1;
    }else{
        $return_arr['version_update_optional']=0;
    }
    $return_arr['android_version_number'] =$this->config->item('ANDROID_VERSION_NUMBER');
    $return_arr['iphone_version_number']  =$this->config->item('IPHONE_VERSION_NUMBER');
    $return_arr['version_check_message']  = str_replace('|appname|',$app_name,$message);
    return $return_arr;
}

}
?>
