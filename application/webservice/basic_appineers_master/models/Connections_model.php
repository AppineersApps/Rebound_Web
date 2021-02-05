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
 * @class States_list_model.php
 *
 * @path application\webservice\basic_appineers_master\models\States_list_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Connections_model extends CI_Model
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
     public function connection_details($params_arr = array())
    {
        try {
            //print_r($params_arr['premium_status']);
             //exit;

            $page_no = 1;
            $start_offset = 0;
            $end_offset =  $this->config->item("PAGINATION_ROW_COUNT");

            if($params_arr["page_no"] != "" )
            {
                $page_no = isset($params_arr["page_no"]) ? $params_arr["page_no"] : 1;

                $start_offset = ($page_no * $end_offset) - $end_offset;
            }

            if($params_arr['connection_type']=="Match"){
        
          $result_arr = array();
                $this->db->from("users_connections AS us1");
            $this->db->join("users AS u", "us1.iConnectionUserId = u.iUserId", "left");
            $this->db->join("users_connections AS us2", "us1.iConnectionUserId = us2.iUserId", "left");

             $this->db->select("u.iUserId AS user_id");
             $this->db->select("u.vFirstName AS first_name");
              $this->db->select("u.vLastName AS last_name");
             $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
             $this->db->select("u.vProfileImage AS user_image"); 
            // $this->db->select("u.iAge AS age");
           
            if (isset($params_arr['user_id']) && $params_arr['user_id'] != "")
            {
                //$this->db->where("us1.iUserId =", $params_arr['user_id']);
            
             $strwher="(us1.eConnectionType = 'Like' OR us1.eConnectionType='Superlike' AND u.eStatus = 'Active') AND us1.iUserId = '".$params_arr['user_id']."'";
            $this->db->where( $strwher);
            }
            if (isset($params_arr['user_id']) && $params_arr['user_id'] != "")
            {
              
                $strwher="(us2.eConnectionType = 'Like' OR us2.eConnectionType='Superlike' AND u.eStatus = 'Active') AND us2.iConnectionUserId = '".$params_arr['user_id']."'";
                $this->db->where($strwher);
            }
           
              if ($params_arr['premium_status']==1)
              {
              $this->db->order_by('u.iUserId');
              }else
              {
                $this->db->order_by('u.iUserId');
              $this->db->limit(3);
              }

             $result_obj = $this->db->get();
               //echo $this->db->last_query();exit;
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
    
            }

            else if($params_arr['connection_type']=="Like"){
                $result_arr = array();
                $this->db->from("users_connections AS usc");            
                $this->db->join("users AS u", "u.iUserId = usc.iConnectionUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("u.vFirstName AS first_name");
              $this->db->select("u.vLastName AS last_name");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image"); 
               // $this->db->select("u.iAge AS age");
                $this->db->select("usc.eConnectionType AS connection_type");
                //$this->db->where("usc.eStatus", 'Active');

                $strwher="(usc.eConnectionType = '".$params_arr['connection_type']."' OR usc.eConnectionType='Superlike') AND usc.iUserId = '".$params_arr['user_id']."'";
                
                $this->db->where($strwher);
                $this->db->order_by("usc.dtAddedAt","DESC");

                 if($params_arr["page_no"] != "" )
                {
                   $this->db->limit($end_offset,$start_offset); 
                }
                $result_obj = $this->db->get();
               //echo $this->db->last_query();exit;
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
                
            }

            else if($params_arr['connection_type']=="Likeme"){
                $result_arr = array();
                 $this->db->from("users_connections AS usc");
                 $this->db->select("u.vFirstName AS first_name");
              $this->db->select("u.vLastName AS last_name");            
                $this->db->join("users AS u", "u.iUserId = usc.iUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image");
              //  $this->db->select("u.iAge AS age");
                $this->db->select("usc.eConnectionType AS connection_type");
                //$this->db->where("usc.eStatus", 'Active');

                $strwher="(usc.eConnectionType = 'Like') AND usc.iConnectionUserId = '".$params_arr['user_id']."'";
                
                $this->db->where($strwher);
                $this->db->order_by("usc.dtAddedAt","DESC");

                 if($params_arr["page_no"] != "" )
                {
                   $this->db->limit($end_offset,$start_offset); 
                }
                $result_obj = $this->db->get();
               //echo $this->db->last_query();exit;
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
                
            }
              else if($params_arr['connection_type']=="block"){
                $result_arr = array();
                $this->db->from("user_block AS usb");            
                $this->db->join("users AS u", "u.iUserId = usb.iBlockUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("u.vFirstName AS first_name");
              $this->db->select("u.vLastName AS last_name");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image");
                $this->db->where("usb.eStatus", 'Active');
                $this->db->where("usb.iUserId", $params_arr['user_id']);
                $this->db->order_by("usb.dtAddedAt","DESC");

                if($params_arr["page_no"] != "" )
                {
                   $this->db->limit($end_offset,$start_offset); 
                }
                $result_obj = $this->db->get();
               //echo $this->db->last_query();exit;
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
                
            }

            if(!is_array($result_arr) || count($result_arr) == 0){
                    throw new Exception('No records found.');
            }
            
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        //print_r($return_arr);
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        // print_r($return_arr);
        return $return_arr;
    }


     /**
     * get_states_list_v1 method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
     public function get_match_user($params_arr = array())
    {
        try {
            //print_r($params_arr['premium_status']);
             //exit;
        
        
          $result_arr = array();
                $this->db->from("users_connections AS us1");
            $this->db->join("users AS u", "us1.iConnectionUserId = u.iUserId", "left");
            $this->db->join("users_connections AS us2", "us1.iConnectionUserId = us2.iUserId", "left");

             $this->db->select("u.iUserId AS user_id_4");
             $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name_4", FALSE);
             $this->db->select("u.vProfileImage AS user_image_4"); 
             //$this->db->select("u.iAge AS age_4");
           
            if (isset($params_arr['user_id']) && $params_arr['user_id'] != "")
            {
                //$this->db->where("us1.iUserId =", $params_arr['user_id']);
            
             $strwher="(us1.eConnectionType = 'Like' OR us1.eConnectionType='Superlike' AND u.eStatus = 'Active') AND us1.iUserId = '".$params_arr['user_id']."'";
            $this->db->where( $strwher);
            }
            if (isset($params_arr['user_id']) && $params_arr['user_id'] != "")
            {
              
                $strwher="(us2.eConnectionType = 'Like' OR us2.eConnectionType='Superlike' AND u.eStatus = 'Active') AND us2.iConnectionUserId = '".$params_arr['user_id']."'";
                $this->db->where($strwher);
            }
           
              $this->db->order_by('u.iUserId');
              

             $result_obj = $this->db->get();
               //echo $this->db->last_query();exit;
              $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
    
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                    throw new Exception('No records found.');
            }
            
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        //print_r($return_arr);
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        // print_r($return_arr);
        return $return_arr;
    }


    
     public function delete_like_and_dislike($user_id = '', $connection_user_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iUserId =", $user_id);
            }
            if (isset($connection_user_id) && $connection_user_id != "")
            {
                $this->db->where("iConnectionUserId =", $connection_user_id);
            }
            $res = $this->db->delete("users_connections");
            if (!$res)
            {
                throw new Exception("Failure in deletion.");
            }
            $affected_rows = $this->db->affected_rows();
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }



    /**
     * get_liked_user_details method is used to execute database queries for Like User Profile API.
     * @created Devangi Nirmal | 20.06.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param string $liked_id liked_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_liked_user_details($liked_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("users AS u");
            
            $this->db->select("u.iUserId AS u_users_id");
            $this->db->select("u.eIsSubscribed AS u_is_subscribed_1");
            if(isset($liked_id) && $liked_id != ""){ 
                $this->db->where("u.iUserId =", $liked_id);
            }
            $this->db->where_in("u.eStatus", array('Active'));
            
            
            
            $this->db->limit(1);
            
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

      /**
     * check_eligibility_of_liking method is used to execute database queries for Like User Profile API.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 29.07.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_eligibility_of_liking($user_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("users AS u");
            
            $this->db->select("u.eIsSubscribed AS u_is_subscribed");
            $this->db->select("u.iUserId AS u_users_id_1");
            if(isset($user_id) && $user_id != ""){ 
                $this->db->where("u.iUserId =", $user_id);
            }
            
            
            
            $this->db->limit(1);
            
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     /**
     * delete_record method is used to execute database queries for Like User Profile API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 05.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $liked_id liked_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_record($user_id = '', $liked_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iUserId =", $user_id);
            }
            if (isset($liked_id) && $liked_id != "")
            {
                $this->db->where("iConnectionUserId =", $liked_id);
            }
            $res = $this->db->delete("users_connections");
            if (!$res)
            {
                throw new Exception("Failure in deletion.");
            }
            $affected_rows = $this->db->affected_rows();
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_user_device_token method is used to execute database queries for Like User Profile API.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $liked_id liked_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_device_token($liked_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("users AS u");
            
            $this->db->select("u.vDeviceToken AS u_device_token");
            if(isset($liked_id) && $liked_id != ""){ 
                $this->db->where("u.iUserId =", $liked_id);
            }
           
            
            
            $this->db->limit(1);
            
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

   /**
     * entry_for_match method is used to execute database queries for Like User Profile API.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function entry_for_match($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            $this->db->set($this->db->protect("vNotificationmessage"), $params_arr["_vmessage"], FALSE);
            if (isset($params_arr["liked_id"]))
            {
                $this->db->set("iReceiverId", $params_arr["liked_id"]);
            }
            $this->db->set("eNotificationType", $params_arr["_enotificationtype"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set("eNotificationStatus", $params_arr["_estatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iSenderId", $params_arr["user_id"]);
            }
            $this->db->insert("notification");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id2";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


    /**
     * notification_entry method is used to execute database queries for Like User Profile API.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 19.06.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function notification_entry($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            $this->db->set($this->db->protect("vNotificationmessage"), $params_arr["_vmessage"], FALSE);
            if (isset($params_arr["liked_id"]))
            {
                $this->db->set("iReceiverId", $params_arr["liked_id"]);
            }
            $this->db->set("eNotificationType", $params_arr["_enotificationtype"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set("eNotificationStatus", $params_arr["_estatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iSenderId", $params_arr["user_id"]);
            }
            $this->db->insert("notification");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id1";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    

     /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function add_connection_status($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
         
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["connection_user_id"]))
            {
                $this->db->set("iConnectionUserId", $params_arr["connection_user_id"]);
            }
            
            if (isset($params_arr["connection_type"]))
            {
                $this->db->set("eConnectionType", $params_arr["connection_type"]);
            }
        
            $this->db->insert("users_connections");
            $insert_id = $this->db->insert_id();

            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "connection_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }



     /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function add_blocked_status($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set("eStatus", $params_arr["_estatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["connection_user_id"]))
            {
                $this->db->set("iConnectionUserId", $params_arr["connection_user_id"]);
            }
            $this->db->insert("user_block");
            $insert_id = $this->db->insert_id();

            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "block_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }







 /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function update_exist_connection_status($params_arr = array())
    {

        try
        {

        //print_r($params_arr);
        //exit;
            $result_arr = array();
            
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
         
             if (isset($params_arr["user_id"]))
            {
                $this->db->where("iUserId=", $params_arr["user_id"]);
            }
             if (isset($params_arr["connection_user_id"]))
            {
                $this->db->where("iConnectionUserId =", $params_arr["connection_user_id"]);
            }

            if (isset($params_arr["connection_type"]))
            {
                $this->db->set("eConnectionType", $params_arr["connection_type"]);
            }
           
            
            
            $res = $this->db->update("users_connections");
            
            $affected_rows = $this->db->affected_rows();
             //echo $this->db->last_query();exit;
            
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }



 /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
   


     /**
     * delete review method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_block_connection($params_arr = array())
    {
        // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["block_id"]))
            {
                $this->db->where("iBlockId =", $params_arr["block_id"]);
            }
            $this->db->stop_cache();
           
            $res = $this->db->delete("user_block");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


     /**
     * delete review method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_connection($params_arr = array())
    {
        // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["connection_id"]))
            {
                $this->db->where("iConnectionId =", $params_arr["connection_id"]);
            }
            $this->db->stop_cache();
           
            $res = $this->db->delete("user_connections");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $receiver_id = '',$connection_type='')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_connections AS uc");
            // if(false ==empty($connection_type) && $connection_type == 'Like'){
            $this->db->join("users AS s", "uc.iUserId = s.iUserId", "left");
            $this->db->join("users AS r", "uc.iConnectionUserId  = r.iUserId", "left");
            // }
             // if(false ==empty($connection_type) && $connection_type == 'Match'){
             //     $this->db->join("users AS s", "uc.iConnectionUserId = s.iUserId", "left");
             //    $this->db->join("users AS r", "uc.iUserId  = r.iUserId", "left");
             // }

            $this->db->select("s.iUserId AS s_users_id");
            $this->db->select("r.iUserId AS r_users_id");
            $this->db->select("r.vDeviceToken AS r_device_token");
            $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
           // $this->db->select("r.eNotificationType AS r_notification");
            // $this->db->where("(uc.iUserId = ".$user_id." ) OR (uc.iConnectionUserId = ".$receiver_id.")", FALSE, FALSE);
            //if(false ==empty($connection_type) && $connection_type == 'Like'){
                $this->db->where("(uc.iUserId = ".$user_id." AND uc.iConnectionUserId = ".$receiver_id.")", FALSE, FALSE);

           /* }
             if(false ==empty($connection_type) && $connection_type == 'Match'){
                $this->db->where("(uc.iUserId = ".$receiver_id." AND uc.iConnectionUserId = ".$user_id.") OR (uc.iUserId = ".$user_id." AND uc.iConnectionUserId = ".$receiver_id.")", FALSE, FALSE);

            }*/
            

            $this->db->limit(1);

            $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
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
       // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
