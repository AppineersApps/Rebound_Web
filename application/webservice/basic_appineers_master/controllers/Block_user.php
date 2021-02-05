<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of States List Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module States List
 *
 * @class States_list.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Category_list.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Block_user extends Cit_Controller
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
        $this->single_keys = array(
            "get_block_user_details_v1_v1",
            "check_is_blocked",
            "block_user",
            "delete_like_and_dislike",
            "is_data_found"
        );
        $this->multiple_keys = array('get_blocked_users');
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('Block_user_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * rules_block_user method is used to validate api input params.
     * @created Chetan Dvs | 13.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_block_user($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
             "block_status" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "block_status_required",
                )
            ),
            "block_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "block_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "block_user");

        return $valid_res;
    }

    public function rules_get_blocked_users($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "block_user");

        return $valid_res;
    }
    /**
     * start_block_user method is used to initiate api execution flow.
     * @created Chetan Dvs | 13.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */

    public function start_block_user($request_arr = array(), $inner_api = FALSE)
    {

    // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':
            
            $output_response =  $this->get_blocked_users($request_arr);

            return  $output_response;
            break;

         case 'POST':
           // print_r($request_arr);exit;
            $output_response =  $this->block_unblock_user($request_arr);

            return  $output_response;
            break;
        }
    }

    public function get_blocked_users($request_arr = array())
    {
        try
        {
            
            $validation_res = $this->rules_get_blocked_users($request_arr);
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

            $input_params = $this->get_blocked_users_list($input_params);

             $condition_res = $this->is_data_found($input_params);
            if ($condition_res["success"])
            {

                $output_response = $this->get_blocked_users_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->get_blocked_users_finish_success_1($input_params);
                return $output_response;
            }
            
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

     /**
     * get_blocked_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_blocked_users_list($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
            $arrParams=array();

            $arrParams['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

      
            $this->block_result = $this->Block_user_model->get_blocked_users_list($arrParams['user_id']);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

             if (is_array($result_arr) && count($result_arr) > 0)
            {
                $arrInterest =array();
                $arrMedia =array();

                 foreach ($result_arr as $data_key => $data_arr)
                {
                    if((false == empty($data_arr["user_id"])) &&(false == empty($arrParams['user_id'])) )
                    {

                        $data = $data_arr["u_profile_image"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $dest_path = "rebound/user_profile";
                        $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                         $data = $this->general->get_image_aws($image_arr);

                        $result_arr[$data_key]["u_profile_image"] = $data;
                    }
                }

                $this->block_result["data"] = $result_arr;
            }

        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_blocked_users_list"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        //print_r($input_params); exit();

        return $input_params;
    }

    public function is_data_found($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_blocked_users_list"]) ? 0 : 1);
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

    public function block_unblock_user($request_arr = array())
    {
        try
        {
            
            $validation_res = $this->rules_block_user($request_arr);
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

            $input_params = $this->get_block_user_details_v1_v1($input_params);
            

            $condition_res = $this->is_exists1($input_params);

            if ($condition_res["success"])
            {
                $block_status=$input_params['block_status'];
                if($block_status=='block'){

                    $input_params = $this->check_is_blocked($input_params);

                    $condition_res = $this->condition($input_params);


                    if ($condition_res["success"])
                    {

                        $input_params = $this->block_user($input_params);
                        $condition_res = $this->if_blocked($input_params);
                        if ($condition_res["success"])
                        {

                            $input_params = $this->delete_like_and_dislike($input_params);

                            $input_params = $this->delete_like_and_dislike_block_user($input_params);

                            $input_params = $this->delete_notification($input_params);
                             $input_params = $this->delete_notification_block_user($input_params);

                            $input_params = $this->delete_message_thread($input_params);

                            $output_response = $this->blocked_user_finish_success($input_params);
                            return $output_response;
                        }

                        else
                        {

                            $output_response = $this->blocked_user_finish_success_1($input_params);
                            return $output_response;
                        }
                    }

                    else
                    {

                        $output_response = $this->blocked_user_finish_success_2($input_params);
                        return $output_response;
                    }

                }else
                {
                    $input_params = $this->unblock_user($input_params);

                    //$input_params = $this->update_message_thread($input_params);


                      
                      if($input_params["affected_rows"]==1)
                        {
                            $output_response = $this->unblocked_user_finish_success($input_params);
                            return $output_response;

                        }else
                        {
                            $output_response = $this->unblocked_user_finish_success1($input_params);
                            return $output_response;

                        }
                     
                }


            }
            else
            {

                $output_response = $this->blocked_user_finish_success_3($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_liked_user_details_v1_v1 method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 20.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_block_user_details_v1_v1($input_params = array())
    {

        $this->block_result = array();
        try
        {
           
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->get_block_user_details_v1_v1($block_id);
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
        $input_params["get_block_user_details_v1_v1"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_exists1 method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 20.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_exists1($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_block_user_details_v1_v1"]) ? 0 : 1);
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
     * check_is_blocked method is used to process query block.
     * @created Devangi Nirmal | 21.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_is_blocked($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->Block_user_model->check_is_blocked($block_id, $user_id);
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
        $input_params["check_is_blocked"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * condition method is used to process conditions.
     * @created Devangi Nirmal | 21.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["check_is_blocked"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 != $cc_ro_0) ? TRUE : FALSE;
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
     * block_user method is used to process query block.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 21.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function block_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = array();
            if (isset($input_params["block_id"]))
            {
                $params_arr["block_id"] = $input_params["block_id"];
            }
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            $params_arr["_daddeddate"] = "NOW()";
            $this->block_result = $this->Block_user_model->block_user($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["block_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * if_blocked method is used to process conditions.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function if_blocked($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["insert_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
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
     * delete_like_and_dislike method is used to process query block.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_like_and_dislike($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->delete_like_and_dislike($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_like_and_dislike"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



    /**
     * delete_like_and_dislike method is used to process query block.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_like_and_dislike_block_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->delete_like_and_dislike_block_user($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_like_and_dislike_block_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



     /**
     * delete_like_and_dislike method is used to process query block.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_notification($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->delete_notification($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_like_and_dislike"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    
     /**
     * delete_like_and_dislike method is used to process query block.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_notification_block_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->delete_notification_block_user($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_notification_block_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



    /**
     * delete_message_thread method is used to process query block.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_message_thread($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->delete_message_thread($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_message_thread"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * delete_like_and_dislike method is used to process query block.
     * @created Chetan Dvs | 16.05.2019
     * @modified Chetan Dvs | 16.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function unblock_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->unblock_user($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["unblock_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


     /**
     * delete_message_thread method is used to process query block.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Mangal Rathore | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_message_thread($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $block_id = isset($input_params["block_id"]) ? $input_params["block_id"] : "";
            $this->block_result = $this->Block_user_model->update_message_thread($user_id, $block_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_message_thread"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * blocked_user_finish_success method is used to process finish flow.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "blocked_user_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * blocked_user_finish_success_1 method is used to process finish flow.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "blocked_user_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * blocked_user_finish_success_2 method is used to process finish flow.
     * @created Devangi Nirmal | 21.05.2019
     * @modified Devangi Nirmal | 30.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "blocked_user_finish_success_2",
        );
        $output_fields = array(
            'bu_blocked_user_id',
        );
        $output_keys = array(
            'check_is_blocked',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * blocked_user_finish_success_3 method is used to process finish flow.
     * @created Devangi Nirmal | 20.06.2019
     * @modified Devangi Nirmal | 20.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "blocked_user_finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * blocked_user_finish_success method is used to process finish flow.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function unblocked_user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "unblocked_user_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * blocked_user_finish_success method is used to process finish flow.
     * @created Chetan Dvs | 13.05.2019
     * @modified Chetan Dvs | 13.05.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function unblocked_user_finish_success1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "unblocked_user_finish_success1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "block_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     public function get_blocked_users_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "Blocked users fetched successfully",
        );
        $output_fields = array(
            'user_id',
            'user_name',
            'u_first_name',
            'u_last_name',
            'u_profile_image',
            'u_email',
            'u_mobile_no',
            'u_dob',
            
        );
        $output_keys = array(
            'get_blocked_users_list',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);

        //$output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_blocked_users_list";
        $func_array["function"]["output_keys"] = $output_keys;
        //$func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);
      //  print_r($output_array);
        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

       // print_r($responce_arr); exit();

        return $responce_arr;
    }

    public function get_blocked_users_finish_success_1($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "0",
            "message" => "No data found",
        );
        $output_fields = array();

        $output_keys = array();

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);

        //$output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_blocked_users_list";
        $func_array["function"]["output_keys"] = $output_keys;
        //$func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
      //  $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);
      //  print_r($output_array);
        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

       // print_r($responce_arr); exit();

        return $responce_arr;
    }
   
}
