<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Blocked User Model
 *
 * @category webservice
 *
 * @package friends
 *
 * @subpackage models
 *
 * @module Blocked User
 *
 * @class Blocked_user_model.php
 *
 * @path application\webservice\friends\models\Blocked_user_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 01.08.2019
 */

class Blocked_user_model extends CI_Model
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
     * if_blocked method is used to execute database queries for Send Message API.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 04.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function if_blocked($user_id = '', $receiver_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_connections AS uc");

            $this->db->select("uc.iUserId AS bu_blocked_to");
            $this->db->select("uc.iConnectionUserId AS bu_blocked_by");
            $this->db->where("(uc.dtAddedAt IS NOT NULL AND uc.dtAddedAt <> '')", FALSE, FALSE);
            $this->db->where("(uc.iUserId = ".$user_id." AND uc.iConnectionUserId = ".$receiver_id.") OR (uc.iUserId = ".$receiver_id." AND uc.iConnectionUserId = ".$user_id.")", FALSE, FALSE);

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
     * blocked_data_v1 method is used to execute database queries for Get Liked Users API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 31.07.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function blocked_data_v1($user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            $this->db->select("bu.iBlockedTo AS bu_blocked_to");
            $this->db->select("bu.iBlockedBy AS bu_blocked_by");
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedTo =", $user_id);
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * delete_block method is used to execute database queries for Like User Profile API.
     * @created Devangi Nirmal | 28.05.2019
     * @modified Devangi Nirmal | 28.05.2019
     * @param string $liked_id liked_id is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_block($liked_id = '', $user_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($liked_id) && $liked_id != "")
            {
                $this->db->where("iBlockedTo =", $liked_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iBlockedBy =", $user_id);
            }
            $res = $this->db->delete("blocked_user");
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
     * delete_from_block method is used to execute database queries for Dislike User Profile API.
     * @created Devangi Nirmal | 28.05.2019
     * @modified Devangi Nirmal | 28.05.2019
     * @param string $dislike_id dislike_id is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_from_block($dislike_id = '', $user_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($dislike_id) && $dislike_id != "")
            {
                $this->db->where("iBlockedTo =", $dislike_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("iBlockedBy =", $user_id);
            }
            $res = $this->db->delete("blocked_user");
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
     * blocked_data_v1_v1 method is used to execute database queries for Get Disliked Users API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 31.07.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function blocked_data_v1_v1($user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            $this->db->select("bu.iBlockedTo AS bu_blocked_to");
            $this->db->select("bu.iBlockedBy AS bu_blocked_by");
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedTo =", $user_id);
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
                $this->db->set("iBlockedTo", $params_arr["block_id"]);
            }
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iBlockedBy", $params_arr["user_id"]);
            }
            $this->db->set($this->db->protect("dAddedDate"), $params_arr["_daddeddate"], FALSE);
            $this->db->insert("blocked_user");
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

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            if (isset($block_id) && $block_id != "")
            {
                $this->db->where("bu.iBlockedTo =", $block_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedBy =", $user_id);
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
     * get_blocked method is used to execute database queries for Unblock User API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param string $unblock_id unblock_id is used to process query block.
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_blocked($unblock_id = '', $user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            if (isset($unblock_id) && $unblock_id != "")
            {
                $this->db->where("bu.iBlockedTo =", $unblock_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedBy =", $user_id);
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
     * delete_user_record method is used to execute database queries for Unblock User API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param string $bu_blocked_user_id bu_blocked_user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function delete_user_record($bu_blocked_user_id = '')
    {
        try
        {
            $result_arr = array();
            if (isset($bu_blocked_user_id) && $bu_blocked_user_id != "")
            {
                $this->db->where("iBlockedUserId =", $bu_blocked_user_id);
            }
            $res = $this->db->delete("blocked_user");
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
     * get_blocked_users method is used to execute database queries for Get Blocked Users API.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 10.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param array $settings_params settings_params are used for paging parameters.
     * @return array $return_arr returns response of query block.
     */
    public function get_blocked_users($user_id = '', $page_index = 1, &$settings_params = array())
    {
        try
        {
            $result_arr = array();

            $this->db->start_cache();
            $this->db->from("blocked_user AS bu");
            $this->db->join("users AS u", "bu.iBlockedTo = u.iUsersId", "left");

            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.iUsersId AS u_users_id");
            $this->db->select("(".$this->db->escape("").") AS user_images", FALSE);
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("CONVERT(DATEDIFF(NOW(),u.dob) / 365.25,SIGNED) AS u_dob");
            $this->db->select("(select vAnswer from user_answers where iUsersId = u.iUsersId and iQuestionId = 2) AS intent", FALSE);
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("bu.iBlockedBy =", $user_id);
            }

            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();

            $settings_params['count'] = $total_records;

            $record_limit = 20;
            $current_page = intval($page_index) > 0 ? intval($page_index) : 1;
            $total_pages = getTotalPages($total_records, $record_limit);
            $start_index = getStartIndex($total_records, $current_page, $record_limit);
            $settings_params['per_page'] = $record_limit;
            $settings_params['curr_page'] = $current_page;
            $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
            $settings_params['next_page'] = ($current_page+1 > $total_pages) ? 0 : 1;

            $this->db->order_by("bu.dAddedDate", "desc");
            $this->db->limit($record_limit, $start_index);
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $this->db->flush_cache();
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
     * blocked_data method is used to execute database queries for User Listing API.
     * @created Chetan Dvs | 27.05.2019
     * @modified Devangi Nirmal | 31.07.2019
     * @param string $user_id user_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function blocked_data($user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("blocked_user AS bu");

            $this->db->select("bu.iBlockedUserId AS bu_blocked_user_id");
            $this->db->select("bu.iBlockedTo AS bu_blocked_to");
            $this->db->select("bu.iBlockedBy AS bu_blocked_by");
            if (isset($user_id) && $user_id != "")
            {
                $this->db->or_where("bu.iBlockedBy =", $user_id);
            }
            if (isset($user_id) && $user_id != "")
            {
                $this->db->or_where("bu.iBlockedTo =", $user_id);
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
