<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Subscription Purchase Model
 *
 * @category webservice
 *
 * @package master
 *
 * @subpackage models
 *
 * @module Subscription Purchase
 *
 * @class Subscription_purchase_model.php
 *
 * @path application\webservice\master\models\Subscription_purchase_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 29.05.2020
 */

class Subscription_purchase_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * subscription_purchase method is used to execute database queries for Subscription Purchase API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 26.05.2020
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function subscription_purchase($params_arr = array(), $where_arr = array())
    {
        try
        {
            
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "")
            {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["transaction_id"]))
            {
                $this->db->set("iTransactionId", $params_arr["transaction_id"]);
            }
            if (isset($params_arr["expiry_date"]))
            {
                $this->db->set("dtExpiryDate", $params_arr["expiry_date"]);
            }
            $this->db->set("eReceiptType", $params_arr["_ereceipttype"]);
            if (isset($params_arr["receipt_data_v1"]))
            {
                $this->db->set("tReceiptData", $params_arr["receipt_data_v1"]);
            }
            $this->db->set("eIsSubscribed", $params_arr["_eissubscribed"]);
            if (isset($params_arr["product_id"]))
            {
                $this->db->set("vSubscriptionId", $params_arr["product_id"]);
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


     public function get_user_influencer_deatils($user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");
            //$this->db->join("mod_state AS ms", "u.iStateId = ms.iStateId", "left");

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vInfluencerCode AS u_influencer_code");

            $this->db->where("u.iUserId=", $user_id);

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


     public function add_user_influencer_revenue($params_arr = array())
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
            if (isset($params_arr["u_influencer_code"]))
            {
                $this->db->set("vInfluencerCode", $params_arr["u_influencer_code"]);
            }

            if (isset($params_arr["expiry_date"]))
            {
                $this->db->set("dtExpiryDate", $params_arr["expiry_date"]);
            }

             if (isset($params_arr["product_id"]))
            {
                $this->db->set("tDuration", $params_arr["product_id"]);
            }
            
            if ($params_arr["product_id"]=='com.app.Rebound.monthly')
            {
                $this->db->set("dAmount", '24.99');
            }

            if ($params_arr["product_id"]=='com.app.Rebound.7days')
            {
                $this->db->set("dAmount", '14.99');
            }

            if ($params_arr["product_id"]=='com.app.Rebound.3month')
            {
                $this->db->set("dAmount", '59.99');
            }

            if ($params_arr["product_id"]=='com.app.Rebound.6month')
            {
                $this->db->set("dAmount", '99.99');
            }

            if ($params_arr["product_id"]=='com.app.Rebound.lifetime')
            {
                $this->db->set("dAmount", '149.99');
            }
            
            $this->db->insert("users_subscription");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "query_id";
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
     * subscription_purchase_android method is used to execute database queries for Subscription Purchase API.
     * @created CIT Dev Team
     * @modified saikrishna bellamkonda | 18.12.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function subscription_purchase_android($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "")
            {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }

            $this->db->set("eOneTimeTransaction", $params_arr["_eonetimetransaction"]);
            if (isset($params_arr["expiry_date_v1"]))
            {
                $this->db->set("dtExpiryDate", $params_arr["expiry_date_v1"]);
            }
            if (isset($params_arr["subscription_id"]))
            {
                $this->db->set("vSubscriptionId", $params_arr["subscription_id"]);
            }
            if (isset($params_arr["purchase_token"]))
            {
                $this->db->set("vPurchaseToken", $params_arr["purchase_token"]);
            }
            $this->db->set("eReceiptType", $params_arr["_ereceipttype"]);
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows1";
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
}
