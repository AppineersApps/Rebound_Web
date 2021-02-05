<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Manage Likes Count Controller
 *
 * @category notification
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Manage Likes Count
 *
 * @class Manage_likes_count.php
 *
 * @path application\notifications\user\controllers\Manage_likes_count.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */

class Manage_likes_count extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
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
            "get_likes_per_day",
            "update_the_likes_per_day_of_user",
        );
        $this->block_result = array();

        $this->load->library('notifyresponse');
        $this->load->model('manage_likes_count_model');
        $this->load->model("tools/setting_model");
        $this->load->model("users/users_model");
    }

    /**
     * start_manage_likes_count method is used to initiate api execution flow.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 29.07.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $output_response returns output response of API.
     */
    public function start_manage_likes_count($request_arr = array())
    {
        try
        {



            $output_response = array();
            $input_params = $request_arr;
            $output_array = array();

            $input_params = $this->get_likes_per_day($input_params);

          

            $input_params = $this->update_the_likes_per_day_of_user($input_params);

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
     * get_likes_per_day method is used to process query block.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_likes_per_day($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $this->block_result = $this->setting_model->get_likes_per_day();
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
        $input_params["get_likes_per_day"] = $this->block_result["data"];
        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * update_the_likes_per_day_of_user method is used to process query block.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_the_likes_per_day_of_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["ms_value"]))
            {
                $params_arr["ms_value"] = $input_params["ms_value"];
            }
            $this->block_result = $this->users_model->update_the_likes_per_day_of_user($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_the_likes_per_day_of_user"] = $this->block_result["data"];
        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * finish_success method is used to process finish flow.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "likes per day of the user updated successfully.",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "manage_likes_count";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
