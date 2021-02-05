<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Block User Model
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage models
 *
 * @module Block User
 *
 * @class Block_user_model.php
 *
 * @path application\webservice\user\models\Block_user_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 21.06.2019
 */

class Block_user_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get_liked_user_details_v1_v1 method is used to execute database queries for Block User API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 20.06.2019
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_block_user_details_v1_v1($block_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("users AS u");
            
            $this->db->select("u.iUserId AS u_users_id");
            if(isset($block_id) && $block_id != ""){ 
                $this->db->where("u.iUserId =", $block_id);
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
     * check_is_blocked method is used to execute database queries for Block User API.
     * @created Devangi Nirmal | 21.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param string $block_id block_id is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_is_blocked($block_id = '', $user_id = '')
    {
        try
        {
        	
            $result_arr = array();

            $this->db->from("user_block AS bu");

            $this->db->select("bu.iBlockUserId AS bu_blocked_user_id");
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("bu.iBlockUserId =", $block_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iUserId =", $user_id);
            }

            $this->db->limit(1);

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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * block_user method is used to execute database queries for Block User API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function block_user($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["block_id"]))
            {
                $this->db->set("iBlockUserId", $params_arr["block_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
                 $this->db->set("eStatus", 'active');
            }

            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_daddeddate"], FALSE);
            $this->db->insert("user_block");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
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
     * delete_like_and_dislike method is used to execute database queries for Block User API.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_like_and_dislike($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iUserId =", $user_id);
            }
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("iConnectionUserId =", $block_id);
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
     * delete_like_and_dislike method is used to execute database queries for Block User API.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_like_and_dislike_block_user($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iUserId =", $block_id );
            }
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("iConnectionUserId =", $user_id);
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






       public function delete_notification($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iSenderId =", $user_id);
            }
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("iReceiverId =", $block_id);
            }
            $res = $this->db->delete("notification");
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



    
       public function delete_notification_block_user($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iSenderId =", $block_id);
            }
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("iReceiverId =", $user_id);
            }
            $res = $this->db->delete("notification");
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
     * delete_message_thread method is used to execute database queries for Block User API.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_message_thread($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->where("(dtAddedDate IS NOT NULL AND dtAddedDate <> '')", FALSE, FALSE);
            $this->db->where("((iSenderId =".$user_id." and iReceiverId=".$block_id.") OR (iSenderId =".$block_id." and iReceiverId=".$user_id."))", FALSE, FALSE);
             //$this->db->set("eStatus",'unactive');
            $res = $this->db->delete("message");
           // $res = $this->db->update("message");
            if (!$res)
            {
                throw new Exception("Failure in deletion.");
            }
            $affected_rows = $this->db->affected_rows();
            $result_param = "affected_rows1";
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
     * delete_like_and_dislike method is used to execute database queries for Block User API.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function unblock_user($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iUserId =", $user_id);
            }
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("iBlockUserId =", $block_id);
            }
            $res = $this->db->delete("user_block");
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
     * delete_message_thread method is used to execute database queries for Block User API.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $block_id block_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function update_message_thread($user_id = '', $block_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->where("(dtAddedDate IS NOT NULL AND dtAddedDate <> '')", FALSE, FALSE);
            $this->db->where("((iSenderId =".$user_id." and iReceiverId=".$block_id.") OR (iSenderId =".$block_id." and iReceiverId=".$user_id."))", FALSE, FALSE);
             $this->db->set("eStatus",'active');
            //$res = $this->db->delete("message");
            $res = $this->db->update("message");
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
     * get_blocked_users_list method is used to execute database queries for User Sign Up Email API.
     * @created priyanka chillakuru | 12.09.2019
     * @modified priyanka chillakuru | 01.10.2019
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_blocked_users_list($insert_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.iUserId AS user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS email_user_name", FALSE);
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->join("user_block as ub","u.iUserId = ub.iBlockUserId","right");

            if (isset($insert_id) && $insert_id != "")
            {
                $this->db->where("ub.iUserId =", $insert_id);
            }

            $result_obj = $this->db->get();

             //echo $this->db->last_query();

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
