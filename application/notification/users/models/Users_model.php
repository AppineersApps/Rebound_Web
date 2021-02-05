<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Users Model
 *
 * @category notification
 *
 * @package users
 *
 * @subpackage models
 *
 * @module Users
 *
 * @class Users_model.php
 *
 * @path application\notification\users\models\Users_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 27.04.2020
 */

class Users_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
    }

      /**
     * update_the_likes_per_day_of_user method is used to execute database queries for Manage Likes Count notification.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_the_likes_per_day_of_user($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();
            if (isset($params_arr["ms_value"]))
            {
                $this->db->set("iLikesPerDay", $params_arr["ms_value"]);
            }
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
     * fetch_the_subscribed_users method is used to execute database queries for Check Subscription Status notification.
     * @created CIT Dev Team
     * @modified saikrishna bellamkonda | 23.12.2019
     * @return array $return_arr returns response of query block.
     */
    public function fetch_the_subscribed_users()
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vPurchaseToken AS u_purchase_token");
            $this->db->select("u.eReceiptType AS u_receipt_type");
            $this->db->select("u.vSubscriptionId AS u_subscription_id");
            $this->db->select("u.iTransactionId AS u_transaction_id");
            $this->db->select("u.dtExpiryDate AS u_expiry_date");
            $this->db->select("u.eIsSubscribed AS u_is_subscribed");
            $this->db->select("u.tReceiptData AS u_receipt_data");
            $this->db->where_in("u.eIsSubscribed", array('1'));
            $this->db->where_in("u.eStatus", array('Active'));
            $this->db->where("u.vSubscriptionId !=","com.app.Rebound.lifetime");
            $result_obj = $this->db->get();
            //echo $query=$this->db->last_query();
            //exit;
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
