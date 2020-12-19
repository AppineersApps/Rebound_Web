<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Check Subscription Status Controller
 *
 * @category notification
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Check Subscription Status
 *
 * @class Check_subscription_status_v1.php
 *
 * @path application\notifications\master\controllers\Check_subscription_status_v1.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 27.04.2020
 */

class Check_subscription_status_v1 extends Cit_Controller
{
    public $settings_params;
    public $output_params;
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
        $this->multiple_keys = array(
            "fetch_the_subscribed_users",
            "check_the_subscription_status",
        );
        $this->block_result = array();

        $this->load->library('notifyresponse');
        $this->load->model('check_subscription_status_v1_model');
        $this->load->model("users/users_model");
    }

    /**
     * start_check_subscription_status_v1 method is used to initiate api execution flow.
     * @created priyanka chillakuru | 23.12.2019
     * @modified Devangi Nirmal | 27.04.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $output_response returns output response of API.
     */
    public function start_check_subscription_status_v1($request_arr = array())
    {
        try
        {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = array();

            $input_params = $this->fetch_the_subscribed_users($input_params);

            $input_params = $this->check_the_subscription_status($input_params);

            $output_response = $this->finish_success($input_params);
            return $output_response;
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * fetch_the_subscribed_users method is used to process query block.
     * @created CIT Dev Team
     * @modified saikrishna bellamkonda | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function fetch_the_subscribed_users($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $this->block_result = $this->users_model->fetch_the_subscribed_users();
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["fetch_the_subscribed_users"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * check_the_subscription_status method is used to process custom function.
     * @created CIT Dev Team
     * @modified saikrishna bellamkonda | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_the_subscription_status($input_params = array())
    {
        if (!method_exists($this, "checkSubscription"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkSubscription($input_params);
        }
        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $result_arr["data"]);

        $input_params["check_the_subscription_status"] = $this->notifyresponse->assignFunctionResponse($result_arr);

        return $input_params;
    }

    /**
     * finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "Data saved successfully.",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_subscription_status_v1";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
