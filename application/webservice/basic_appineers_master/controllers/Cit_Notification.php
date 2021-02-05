<?php
            
/**
 * Description of Send Message Extended Controller
 * 
 * @module Extended Send Message
 * 
 * @class Cit_Send_message.php
 * 
 * @path application\webservice\friends\controllers\Cit_Send_message.php
 * 
 * @author CIT Dev Team
 * 
 * @date 30.05.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Notification extends Notification {

   public function __construct()
    {
        parent::__construct();
    }

  public function checkNotificationExist($input_params=array()){
      $return_arr['message']='';
      $return_arr['status']='1';
         if(false == empty($input_params['user_id']))
         {
            $this->db->from("notification");
            $this->db->select("iReceiverId AS receive_id");
             $this->db->where("iReceiverId", $input_params['user_id']);
            $arrNotification=$this->db->get()->result_array();
            //print_r($arrNotification);exit;
          if(true == empty($arrNotification)){
             $return_arr['message']="No notification available";
             $return_arr['status'] = "0";
             return  $return_arr;
          }else{
            $return_arr['receive_id']=$arrNotification['0']['receive_id'];
          }
      }
      return $return_arr;
    
  }
}
