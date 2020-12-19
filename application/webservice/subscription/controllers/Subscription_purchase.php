<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Subscription Purchase Controller
 *
 * @category webservice
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Subscription Purchase
 *
 * @class Subscription_purchase.php
 *
 * @path application\webservice\master\controllers\Subscription_purchase.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 29.05.2020
 */

class Subscription_purchase extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "subscription_purchase",
            "subscription_purchase_android",
        );
        $this->multiple_keys = array(
            "validate_reciept",
            "get_subscription_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('subscription_purchase_model');
        // $this->load->model("users/users_model");
    }

    /**
     * rules_subscription_purchase method is used to validate api input params.
     * @created priyanka chillakuru | 18.12.2019
     * @modified Devangi Nirmal | 26.05.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_subscription_purchase($request_arr = array())
    {
        $valid_arr = array(
            "receipt_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "receipt_type_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "subscription_purchase");

        return $valid_res;
    }

    /**
     * start_subscription_purchase method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.12.2019
     * @modified Devangi Nirmal | 26.05.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_subscription_purchase($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_subscription_purchase($request_arr);
            if ($validation_res["success"] == "-5")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
           
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $condition_res = $this->check_receipt_type($input_params);
            if ($condition_res["success"])
            {



                $condition_res = $this->check_for_receipt_data($input_params);
                if ($condition_res["success"])
                {


                    $input_params = $this->validate_reciept($input_params);


                    $condition_res = $this->is_validate_receipt($input_params);
                    if ($condition_res["success"])
                    {


                        $input_params = $this->subscription_purchase($input_params);

                       

                        $output_response = $this->users_finish_success($input_params);
                        return $output_response;
                    }
                    else
                    {

                        $output_response = $this->users_finish_success_1($input_params);
                        return $output_response;
                    }
                }
                else
                {

                    $output_response = $this->finish_success_1($input_params);
                    return $output_response;
                }
            }

            else
            {

                $condition_res = $this->check_for_subscription_id($input_params);
                if ($condition_res["success"])
                {

                    $input_params = $this->get_subscription_details($input_params);

                    $condition_res = $this->is_android_subscription($input_params);
                    if ($condition_res["success"])
                    {

                        $input_params = $this->subscription_purchase_android($input_params);

                        $output_response = $this->users_finish_success_3($input_params);
                        return $output_response;
                    }

                    else
                    {

                        $output_response = $this->users_finish_success_4($input_params);
                        return $output_response;
                    }
                }

                else
                {

                    $output_response = $this->users_finish_success_2($input_params);
                    return $output_response;
                }
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * check_receipt_type method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receipt_type($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["receipt_type"];
            $cc_ro_0 = "ios";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * check_for_receipt_data method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_receipt_data($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["receipt_data"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * validate_reciept method is used to process custom function.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.04.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function validate_reciept($input_params = array())
    {
        if (!method_exists($this, "validateReceiptCheck"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->validateReceiptCheck($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["validate_reciept"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * is_validate_receipt method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 09.05.2020
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_validate_receipt($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["success"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * subscription_purchase method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 26.05.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function subscription_purchase($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"]))
            {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["transaction_id"]))
            {
                $params_arr["transaction_id"] = $input_params["transaction_id"];
            }
            if (isset($input_params["expiry_date"]))
            {
                $params_arr["expiry_date"] = $input_params["expiry_date"];
            }
            $params_arr["_ereceipttype"] = "ios";
            if (isset($input_params["receipt_data_v1"]))
            {
                $params_arr["receipt_data_v1"] = $input_params["receipt_data_v1"];
            }
            $params_arr["_eissubscribed"] = "1";
            if (isset($input_params["product_id"]))
            {
                $params_arr["product_id"] = $input_params["product_id"];
            }
            $this->block_result = $this->subscription_purchase_model->subscription_purchase($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * users_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 09.05.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 09.05.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * check_for_subscription_id method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_subscription_id($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["subscription_id"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * get_subscription_details method is used to process custom function.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_subscription_details($input_params = array())
    {
        if (!method_exists($this, "subscriptionDetails"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->subscriptionDetails($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["get_subscription_details"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * is_android_subscription method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_android_subscription($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["success_v1"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * subscription_purchase_android method is used to process query block.
     * @created CIT Dev Team
     * @modified saikrishna bellamkonda | 18.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function subscription_purchase_android($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"]))
            {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            $params_arr["_eonetimetransaction"] = "Yes";
            if (isset($input_params["expiry_date_v1"]))
            {
                $params_arr["expiry_date_v1"] = $input_params["expiry_date_v1"];
            }
            if (isset($input_params["subscription_id"]))
            {
                $params_arr["subscription_id"] = $input_params["subscription_id"];
            }
            if (isset($input_params["purchase_token"]))
            {
                $params_arr["purchase_token"] = $input_params["purchase_token"];
            }
            $params_arr["_ereceipttype"] = "android";
            $this->block_result = $this->subscription_purchase_model->subscription_purchase_android($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase_android"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * users_finish_success_3 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success_4 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_4($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success_2 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
