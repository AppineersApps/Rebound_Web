<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of States List Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module States List
 *
 * @class Influencer_type_list_model.php
 *
 * @path application\webservice\influencer\models\Influencer_type_list_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Influencer_type_list_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * get_states_list_v1 method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function influencer_list()
    {
       // print_r("Text");exit;
            try {
            $result_arr = array();
                                
            $this->db->from("influencer");
            $this->db->select('iInfluencerId AS influencer_id');
            $this->db->select('vInfluencerName AS influencer_name');
            $this->db->select('vInfluencerCode AS influencer_code');
            $this->db->select("vInfluencerImage AS influencer_image");
            $this->db->where("eStatus", 'Active');
            $this->db->order_by("iInfluencerId", "asc");

            
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        $this->db->_reset_all();
       // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


    public function set_influencer($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $insert_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            if (isset($params_arr["influencer_code"]))
            {

                if (isset($params_arr["influencer_code"]))
                {
                    $this->db->set("vInfluencerCode", $params_arr["influencer_code"]);
                }
                if (isset($params_arr["user_id"]))
                {
                    $this->db->where("iUserId =", $params_arr["user_id"]);
                }
                $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_upddat"], FALSE);

                $res = $this->db->update("users");
                $affected_rows = $this->db->affected_rows();
                if (!$res || $affected_rows == -1)
                {
                    throw new Exception("Failure in updation.");
                }
                $result_param = "affected_rows";
                $result_arr[0][$result_param] = $affected_rows;
                $success = 1;

            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        #echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     public function get_influencer_details($arrResult)
    {

        try
        {
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';            
            
            $this->db->from("influencer");
            $this->db->select('iInfluencerId AS influencer_id');
            $this->db->select('vInfluencerName AS influencer_name');
            $this->db->select('vInfluencerCode AS influencer_code');
            $this->db->select("vInfluencerImage AS influencer_image");

           
            if(false == empty($arrResult['influencer_type']))
            {
              $this->db->where(" eStatus = '".$arrResult['influencer_type']."' AND iInfluencerId ='".$arrResult['influencer_id']."'"); 
            }
            if(false == empty($arrResult['influencer_id']))
            {
              $this->db->where("iInfluencerId = '".$arrResult['influencer_id']."'"); 
            }
            if(false == empty($arrResult['influencer_code']))
            {
              $this->db->where("vInfluencerCode = '".$arrResult['influencer_code']."'"); 
            }

            $result_obj = $this->db->get();
            
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
