<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Messages Model
 *
 * @category webservice
 *
 * @package comments
 *
 * @subpackage models
 *
 * @module Messages
 *
 * @class Messages_model.php
 *
 * @path application\webservice\comments\models\Messages_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Messages_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * check_chat_intiated_or_not method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_chat_intiated_or_not($user_id = '', $receiver_id = '', $firebase_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("message AS m");

            $this->db->select("m.iMessageId AS m_message_id");
            $this->db->where("m.iMessageId =",$firebase_id);
            $this->db->or_where("(m.iSenderId IS NOT NULL AND m.iSenderId <> '')", FALSE, FALSE);
            $this->db->where("(m.iSenderId = ".$user_id." AND m.iReceiverId = ".$receiver_id.") OR (m.iSenderId = ".$receiver_id." AND m.iReceiverId = ".$user_id.")", FALSE, FALSE);

            $this->db->limit(1);
            $result_obj = $this->db->get();
    //echo $this->db->last_query(); exit;
           
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
        // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * update_message method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_message($params_arr = array(), $where_arr = array())
    {
       // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            if (isset($where_arr["m_message_id"]) && $where_arr["m_message_id"] != "")
            {
                $this->db->where("iMessageId =", $where_arr["m_message_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iSenderId", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"]))
            {
                $this->db->set("iReceiverId", $params_arr["receiver_id"]);
            }
            if (isset($params_arr["message"]))
            {
                $this->db->set("vMessage", $params_arr["message"]);
            }

             if (isset($params_arr["upload_doc"]) && !empty($params_arr["upload_doc"]))
            {
                $this->db->set("tMessageUpload", $params_arr["upload_doc"]);
            }
            
            $this->db->set($this->db->protect("dtModifiedDate"), $params_arr["_dtmodifieddate"], FALSE);
            $res = $this->db->update("message");
            $affected_rows = $this->db->affected_rows();
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * add_message method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function add_message($params_arr = array())
    {
       //print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["firebase_id"]) && $params_arr["firebase_id"] != "")
            {
                $this->db->set("iMessageId", $params_arr["firebase_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iSenderId", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"]))
            {
                $this->db->set("iReceiverId", $params_arr["receiver_id"]);
            }
               if (isset($params_arr["message"]))
            {
                $this->db->set("vMessage", $params_arr["message"]);
            }

             if (isset($params_arr["upload_doc"]) && !empty($params_arr["upload_doc"]))
            {
                $this->db->set("tMessageUpload", $params_arr["upload_doc"]);
            }

            
            $this->db->set($this->db->protect("dtAddedDate"), $params_arr["_dtaddeddate"], FALSE);
            $this->db->set($this->db->protect("dtModifiedDate"), $params_arr["_dtmodifieddate"], FALSE);
            
            $this->db->insert("message");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "m_message_id";
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
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $receiver_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("message AS m");
            $this->db->join("users AS s", "m.iSenderId = s.iUserId", "left");
            $this->db->join("users AS r", "m.iReceiverId = r.iUserId", "left");

            $this->db->select("s.iUserId AS s_users_id");
            $this->db->select("r.iUserId AS r_users_id");
            $this->db->select("r.vDeviceToken AS r_device_token");
            $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
           // $this->db->select("r.eNotificationType AS r_notification");
            $this->db->where("(m.iSenderId = ".$user_id." AND m.iReceiverId = ".$receiver_id.")", FALSE, FALSE);
            $this->db->where("r.eStatus","Active");

            $this->db->limit(1);

            $result_obj = $this->db->get();
            #echo $this->db->last_query();exit;
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
     * get_message method is used to execute database queries for Get Message List API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param string $where_clause where_clause is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_message($where_clause = '',$status='')
    {
        //print_r($user_id.'---'.$receiver_id);exit;
       
        try
        {
            $result_arr = array();

            $this->db->from("message AS m");
            $this->db->join("users AS u", "m.iSenderId = u.iUserId", "left");
            $this->db->join("users AS u1", "m.iReceiverId = u1.iUserId", "left");
          
            $this->db->select("m.iMessageId AS message_id"); 
             $this->db->select("m.dtAddedDate AS message_date"); 
            $this->db->select("m.iSenderId AS sender_id");
            $this->db->select("m.iReceiverId AS receiver_id");
            $this->db->select("m.vMessage AS message");
            $this->db->select("m.tMessageUpload AS message_upload");
            $this->db->select("concat(u.vFirstName,\" \",u.vLastName) AS sender_name");
            $this->db->select("concat(u1.vFirstName,\" \",u1.vLastName) AS receiver_name");
            $this->db->select("m.dtModifiedDate AS updated_at");
            $this->db->select("(".$this->db->escape("").") AS sender_image", FALSE);
            $this->db->select("(".$this->db->escape("").") AS receiver_image", FALSE);
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_logged_user", FALSE);
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_receiver_user", FALSE);
           
           $this->db->where("".$where_clause." ", FALSE, FALSE);

           if (isset($status) && $status != "")
            {
            $this->db->where("m.eStatus",$status);
            }
           $this->db->order_by("m.dtAddedDate", "desc");

           // if(false == empty($receiver_id)){
           //  $this->db->where("m.iSenderId", $user_id)->where("m.iReceiverId", $receiver_id)->or_where("m.iSenderId", $receiver_id)->where("m.iReceiverId", $user_id); 
           //   $this->db->order_by("m.dtModifiedDate", "desc");
           
           //  }else{
           //  /*$this->db->where("m.iReceiverId", $user_id);
           //  $this->db->order_by("m.dtAddedDate", "desc");*/
           //  $strWhere ="m.iMessageId in (select max(m.iMessageId ) as max_id
           //          from message m
           //           group by least(m.iSenderId , m.iReceiverId ), greatest(m.iSenderId , m.iReceiverId )
           //          ) 
           //           AND (m.iSenderId = '".$user_id."' OR m.iReceiverId = '".$user_id."')";
           //          $this->db->where($strWhere);
           //         // $this->db->where("m.iSenderId", $user_id)->or_where("m.iReceiverId", $user_id); 
           //  }
            $result_obj = $this->db->get();
            //echo $this->db->last_query();exit;
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
     * delete_message_thread_v1 method is used to execute database queries for Dislike User Profile API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 17.07.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $dislike_id dislike_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_message_thread_v1($user_id = '', $dislike_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->where("(dtAddedDate IS NOT NULL AND dtAddedDate <> '')", FALSE, FALSE);
            $this->db->where("((iSenderId =".$user_id." and iReceiverId=".$dislike_id.") OR (iSenderId =".$dislike_id." and iReceiverId=".$user_id."))", FALSE, FALSE);
            $res = $this->db->delete("message");
            if (!$res)
            {
                throw new Exception("Failure in deletion.");
            }
            $affected_rows = $this->db->affected_rows();
            $result_param = "affected_rows5";
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
            $res = $this->db->delete("message");
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
    public function delete_message_id($where_arr = array())
  {
    try
        {
            $result_arr = array();

            if(strpos($where_arr['message_id'], ',') !== false){
                    $strWhere = "iMessageId IN ('" . str_replace(",", "','", $where_arr['message_id']) . "')";            
            }else{
                 $strWhere = "iMessageId ='" .$where_arr['message_id']. "'";   
            }
            if (isset($strWhere) && $strWhere != "")
            {
                $this->db->where($strWhere);
            }
            $res = $this->db->delete("message");
           //echo $this->db->last_query();exit;
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success =1;
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
        //print_r($return_arr);exit;
        return $return_arr;
    }
}
