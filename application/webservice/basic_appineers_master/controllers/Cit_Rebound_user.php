<?php

   
/**
 * Description of User Sign Up Email Extended Controller
 * 
 * @module Extended User Sign Up Email
 * 
 * @class Cit_User_sign_up_email.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_User_sign_up_email.php
 * 
 * @author CIT Dev Team
 * 
 * @date 10.02.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Rebound_user extends Rebound_user {
        public function __construct()
{
    parent::__construct();
}
public function prepareDistanceQuery($input_params=array()){

    $this->db->select('dLatitude,dLongitude');
        $this->db->from('users');
        $this->db->where('iUserId', $input_params['user_id']);
        $user_data=$this->db->get()->row_array();

 
      $user_latitude    =   $user_data['dLatitude'];
      $user_longitude   =   $user_data['dLongitude'];
      if(!empty($user_longitude) && !empty($user_latitude))
      {

        $distance = "
            3959 * acos (
              cos ( radians($user_latitude) )
              * cos( radians( u.dLatitude ) )
              * cos( radians( u.dLongitude ) - radians($user_longitude))
              + sin ( radians($user_latitude) )
              * sin( radians( u.dLatitude ))
            )";
        
      }else{
           //distance filter
        $distance= 'IF(1=1,"","")'; 
      }
      
      $return_arr['distance']=$distance;

  
    
      return $return_arr;
}

}
