<?php

   
/**
 * Description of Get Message List Extended Controller
 * 
 * @module Extended Get Message List
 * 
 * @class Cit_Get_message_list.php
 * 
 * @path application\webservice\friends\controllers\Cit_Get_message_list.php
 * 
 * @author CIT Dev Team
 * 
 * @date 18.06.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Get_message_list extends Get_message_list {
        public function __construct()
{
    parent::__construct();
}
public function format_images(&$input_params)
{
    if(!empty($input_params['get_send_image']))
    {
        foreach ($input_params['get_send_image'] as $key => $image)
        {
            if(!empty($image['u_image']))
            {
                $input_params['sender_image'] = $image['u_image'];
            }
        }
        
    }
    
    if(!empty($input_params['get_receiver_images']))
    {
        foreach ($input_params['get_receiver_images'] as $key => $image)
        {
            if(!empty($image['ui_image']))
            {
                $input_params['receiver_image'] = $image['ui_image'];
            }
        }
        
    }
 
}
  public function checkMessageExist($input_params=array()){
      $return_arr['message']='';
      $return_arr['status']='1';
         if(false == empty($input_params['message_id']))
         {
            $this->db->from("message");
            $this->db->select("iMessageId AS message_id");

            if(strpos($input_params['message_id'], ',') !== false){
                    $strWhere = "iMessageId IN ('" . str_replace(",", "','", $input_params['message_id']) . "')";            
            }else{
               $strWhere = "iMessageId ='" .$input_params['message_id']. "'";   
            }
            if (isset($strWhere) && $strWhere != "")
            {
               $this->db->where($strWhere);
            }
            $arrMessage=$this->db->get()->result_array();
            //print_r($arrMessage);exit;
          if(true == empty($arrMessage)){
             $return_arr['message']="No notification available";
             $return_arr['status'] = "0";
             return  $return_arr;
          }else{
            $return_arr['message_id']=array_column($arrMessage, 'message_id');
          }
      }
      return $return_arr;
    
  }
}
