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
