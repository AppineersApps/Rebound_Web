<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Social Sign Up Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Social Sign Up
 *
 * @class Social_sign_up_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Social_sign_up_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Social_sign_up_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * update_social_info method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_social_info($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();

            $this->db->start_cache();
            if (isset($where_arr["email"]) && $where_arr["email"] != "")
            {
                $this->db->where("vEmail =", $where_arr["email"]);
            }
            $this->db->where_in("eStatus", array('Active'));
            $this->db->stop_cache();

            if (isset($params_arr["first_name"]) && $params_arr["first_name"] != "")
            {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (isset($params_arr["last_name"]) && $params_arr["first_name"] != "")
            {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (isset($params_arr["device_type"]))
            {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"]))
            {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"]))
            {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"]))
            {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
          
            if (isset($params_arr["auth_token"]))
            {
                $this->db->set("vAccessToken", $params_arr["auth_token"]);
            }
            if (isset($params_arr["social_login_type"]))
            {
                $this->db->set("eSocialLoginType", $params_arr["social_login_type"]);
            }
            if (isset($params_arr["social_login_id"]))
            {
                $this->db->set("vSocialLoginId", $params_arr["social_login_id"]);
            }

            if (isset($params_arr["profession"]) && $params_arr["profession"] != "")
            {
                $this->db->set("vProfession", $params_arr["profession"]);
            }

            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);


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
     * get_user_details_byemail method is used to execute database queries for Social Sign Up API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 23.12.2019
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_byemail($email = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS u");
            //$this->db->join("mod_state AS ms", "u.iStateId = ms.iStateId", "left");

            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("u.iStateId AS u_state_id");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS email_user_name", FALSE);
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            //$this->db->select("ms.vState AS ms_state");
            $this->db->select("u.eOneTimeTransaction AS e_one_time_transaction");
            $this->db->select("u.tOneTimeTransaction AS t_one_time_transaction");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.vTermsConditionsVersion AS terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            if (isset($email) && $email != "")
            {
                $this->db->where("u.vEmail =", $email);
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
}
