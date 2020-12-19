<?php


/**
 * Description of Users Management Extended Controller
 * 
 * @module Extended Users Management
 * 
 * @class Cit_Influencer_management.php
 * 
 * @path application\admin\basic_appineers_master\controllers\Cit_Influencer_management.php
 * 
 * @author CIT Dev Team
 * 
 * @date 01.10.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Influencer_management extends Influencer_management {
        public function __construct()
{
    parent::__construct();
      $this->load->model('cit_api_model');
}
public function checkUniqueInfluencer($value = ''){
    $return_arr='1';
    if(false == empty($value)){
      $this->db->select('iInfluencerId');
      $this->db->from('influencer');
      $this->db->where_in('vInfluencerCode', $value);
      $arrInterestData=$this->db->get()->result_array();
     // echo $this->db->last_query();

     if(true == empty($arrInterestData)){
         $return_arr = "1";
         return  $return_arr;
      } 
      else {
         $return_arr = "0";
         return  $return_arr;
      }     
    } 
   return  $return_arr; 
    
}
public function showStatusButton($id='',$arr=array())
{     
        $url = $this->general->getAdminEncodeURL('basic_appineers_master/influencer_management/add').'|mode|'.$this->general->getAdminEncodeURL('Update').'|id|'.$this->general->getAdminEncodeURL($arr);
       return '<button type="button" data-id='.$arr.' class="btn btn-success operBut" data-url='.$url.' >Edit</button>';
      
}
public function ActiveInfluencerInlineEdition($field_name = '', $value = '', $id = ''){

            if($value=='Active'){
                $data = array(
                        'eStatus' => 'Active',
                        'dtUpdatedAt' => date('Y-m-d H:i:s'),
                        'dtDeletedAt'=>''
                   );
                
                $this->db->where('iInfluencerId', $id);
                $this->db->update('influencer', $data);
                $ret_arr['success'] = true; 
                $ret_arr['value'] = $value;
            
            }else if($value=='Archived'){
                $data=array(
                        'eStatus' => 'Archived',
                        'dtDeletedAt' => date('Y-m-d H:i:s')
                   );
                $this->db->where('iInfluencerId', $id);
                $this->db->update('influencer', $data);
                $ret_arr['success'] = true; 
                $ret_arr['value'] = $value;
            
            }else{
            $ret_arr['success'] = true; 
            $ret_arr['value'] = $value;
            }
            
            return $ret_arr;
    
}

public function updateDeletedAt($mode = '', $id = '', $parID = ''){
    $data=$this->input->post();
      if($data['u_status']=='Archived'){
          $data=array(
                          'dtDeletedAt' => date('Y-m-d H:i:s')
                      );
          $this->db->where('iInfluencerId', $id);
          $this->db->update('influencer', $data);
          $ret_arr['success'] = true;
      }else{
          $data=array(
                          'dtDeletedAt' => ''
                      );
          $this->db->where('iInfluencerId', $id);
          $this->db->update('influencer', $data);
          $ret_arr['success'] = true;
      }
      return $ret_arr;
     
  }
}
