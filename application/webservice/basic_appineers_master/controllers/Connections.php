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

class Connections extends Cit_Controller
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
            "if_blocked",
            "check_chat_intiated_or_not",
            "update_message",
            "get_user_details_for_send_notifi",
            "post_notification",
            "get_sender_image",
            "add_message",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('connections_model');
        $this->load->model('rebound_user_model');
        $this->load->model("basic_appineers_master/users_model");
    }

   
     /**
     * start_states_list method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_connections($request_arr = array(), $inner_api = FALSE)
    {

   

        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
        case 'GET':            
            $output_response =  $this->get_connection_detail($request_arr);
          break;
        case 'POST':
            $output_response =  $this->add_connection_status($request_arr);
            break;
        case 'PUT':
            // $output_response =  $this->update_technique_status($request_arr);
            break;
        }
        return $output_response;

    }

     public function rules_get_connection_detail($request_arr = array())
    {
        $valid_arr = array(
            "connection_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "connection_type_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "connections");

        return $valid_res;
    }

    public function get_connection_detail($request_arr = array())
    {
            $validation_res = $this->rules_get_connection_detail($request_arr);
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

             $input_params = $this->get_config_params($input_params);

            $input_params = $this->get_connection_detail_v1($input_params);

            $condition_res = $this->condition_connection($input_params);


            if ($condition_res["success"])
            {

                $output_response = $this->get_connection_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->get_connection_finish_success_1($input_params);
                return $output_response;
            }
            
          
        return $output_response;
        
    }

    /**
     * get_config_params method is used to process custom function.
     * @created priyanka chillakuru | 19.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_config_params($input_params = array())
    {
        if (!method_exists($this, "returnConfigParams"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->returnConfigParams($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["get_config_params"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }


       public function get_connection_detail_v1($input_params = array())
    {
        

        $this->block_result = array();
        try
        {

            
            $this->block_result = $this->connections_model->connection_details($input_params);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }

            $result_arr = $this->block_result["data"];
            //print_r( $result_arr);exit;
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                { 

                    $data = $data_arr["user_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = "fern/user_profile";
                   // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["user_image"] = $data;

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
          
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_connection_detail_v1"] = $result_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_connection_detail_v1"]);



        return $input_params;
    }


     public function condition_connection($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_connection_detail_v1"]) ? 0 : 1);
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
     * rules_set_store_item method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_connection_status($request_arr = array())
    {        
        $valid_arr = array(
            "connection_user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "connection_user_id_required",
                )
            ),

            "connection_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "connection_type_required",
                )
            ),
        );
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_technique_status");

        return $valid_res;
    }


     public function check_connection_exist($input_params = array())
    {
        if (!method_exists($this, "checkConnectionExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkConnectionExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkconnectionexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

      public function get_match_user($input_params = array())
    {
        

        $this->block_result = array();
        try
        {

            
            $this->block_result = $this->connections_model->get_match_user($input_params);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }

            $result_arr = $this->block_result["data"];
            //print_r( $result_arr);exit;
          
          
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_match_user"] = $result_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_match_user"]);



        return $input_params;
    }


    /**
     * get_liked_user_details method is used to process query block.
     * @created Devangi Nirmal | 20.06.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_liked_user_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $liked_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";
            $this->block_result = $this->connections_model->get_liked_user_details($liked_id);
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
        $input_params["get_liked_user_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * condition_1 method is used to process conditions.
     * @created Devangi Nirmal | 20.06.2019
     * @modified Devangi Nirmal | 20.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition_1($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_liked_user_details"]) ? 0 : 1);
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
     * check_eligibility_of_liking method is used to process query block.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 29.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_eligibility_of_liking($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->connections_model->check_eligibility_of_liking($user_id);
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
        $input_params["check_eligibility_of_liking"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * condition_2 method is used to process conditions.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition_2($input_params = array())
    {

         $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["check_eligibility_of_liking"]) ? 0 : 1);
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
     * delete_record method is used to process query block.
     * @created Chetan Dvs | 13.05.2019
     * @modified Devangi Nirmal | 05.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_record($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $liked_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";
            $this->block_result = $this->connections_model->delete_record($user_id, $liked_id);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_record"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * get_user_device_token method is used to process query block.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_device_token($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $liked_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";
            $this->block_result = $this->connections_model->get_user_device_token($liked_id);
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
        $input_params["get_user_device_token"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * entry_for_match method is used to process query block.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function entry_for_match($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = array();
            $params_arr["_vmessage"] = "'Your profile is matched with ".$input_params["user_name"].".'";
            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["liked_id"] = $input_params["connection_user_id"];
            }
            $params_arr["_enotificationtype"] = "Match";
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_estatus"] = "active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            $this->block_result = $this->connections_model->entry_for_match($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["entry_for_match"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * condition method is used to process conditions.
     * @created Devangi Nirmal | 19.06.2019
     * @modified Devangi Nirmal | 30.07.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_user_device_token"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $cc_lo_1 = $input_params["u_notification"];
            $cc_ro_1 = "Yes";

            $cc_fr_1 = ($cc_lo_1 == $cc_ro_1) ? TRUE : FALSE;
            if (!$cc_fr_1)
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
     * notification_entry method is used to process query block.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 19.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function notification_entry($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = array();
            $params_arr["_vmessage"] = "'".$input_params["user_name"]." ".$input_params["connection_type"]." your profile'";
            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["liked_id"] = $input_params["connection_user_id"];
            }
            $params_arr["_enotificationtype"] = $input_params["connection_type"];
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_estatus"] = "active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            $this->block_result = $this->connections_model->notification_entry($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["notification_entry"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * push_notification method is used to process mobile push notification.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 30.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $device_id = $input_params["u_device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                 array(
                    "key" => "type",
                    "value" =>$input_params["connection_type"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["user_id"],
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["user_name"]." ".$input_params["connection_type"]." your profile.";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id)
            {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }


    
    /**
     * push_notification_1 method is used to process mobile push notification.
     * @created Devangi Nirmal | 05.06.2019
     * @modified Devangi Nirmal | 19.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification_1($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $device_id = $input_params["u_device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                 array(
                    "key" => "type",
                    "value" => "Match",
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["user_id"],
                    "send" => "Yes",
                )
            );
            $push_msg = "Your profile is matched with ".$input_params["user_name"].".";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id)
            {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification_1"] = $this->block_result["success"];

        return $input_params;
    }



    public function add_connection_status($input){
        // print_r($input);exit;
        try
        {


            
            $validation_res = $this->rules_add_connection_status($input);
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
            $input_params = $validation_res['input_params'];



            $input_params = $this->get_liked_user_details($input_params);

             $input_params = $this->check_eligibility_of_liking($input_params);

            
           // $input_params = $this->get_match_user($input_params);
            //echo count($input_params['get_match_user']);
            //exit;



            if($input_params['u_is_subscribed']==1){

              

                        $condition_res = $this->condition_1($input_params);

                         if ($condition_res["success"])
                        {

                            $input_params = $this->check_eligibility_of_liking($input_params);

                            $condition_res = $this->condition_2($input_params);

                            if ($condition_res["success"])
                            {

                                $input_params = $this->delete_record($input_params);

                                $input_params = $this->set_connection_status($input_params);

                                $input_params = $this->get_user_device_token($input_params);

                                
                                $input_params = $this->get_users_list_details($input_params);

                                //check match user condition like and like or superlike and like or like and Superlike
                                        if((($input_params['connection_type_by_logged_user']=='Like') && ($input_params['connection_type_by_receiver_user']=='Like')) OR (($input_params['connection_type_by_logged_user']=='Superlike') && ($input_params['connection_type_by_receiver_user']=='Like')) OR (($input_params['connection_type_by_logged_user']=='Like') && ($input_params['connection_type_by_receiver_user']=='Superlike')) OR (($input_params['connection_type_by_logged_user']=='Superlike') && ($input_params['connection_type_by_receiver_user']=='Superlike')))
                                        {

                                            $input_params = $this->entry_for_match($input_params);

                                            
                                            if ($input_params["u_device_token"]!=''){

                                               
                                                $input_params = $this->push_notification_1($input_params);
                                                $output_response = $this->connection_add_finish_success($input_params);
                                               return $output_response;
                                            }else{
                                            $output_response = $this->connection_add_finish_success_2($input_params);
                                                return $output_response;
                                            }

                                        }
                                        else
                                        {


             
                                           if (($input_params["connection_type"]=='Like') OR ($input_params["connection_type"]=='Superlike'))
                                            {

                                                    if ($input_params["u_device_token"]!=''){ 


                                                    $input_params = $this->notification_entry($input_params);

                                                    if($input_params['u_is_subscribed_1']==1){
                                                    $input_params = $this->push_notification($input_params);
                                                    }
                                                    
                                                    $output_response = $this->connection_add_finish_success($input_params);
                                                     return $output_response;
                                                    }else
                                                    {
                                                         $output_response = $this->connection_add_finish_success_2($input_params);
                                                                    return $output_response;
                                                    }

                                            }

                                            else
                                            {

                                                $output_response = $this->connection_add_finish_success($input_params);
                                                                return $output_response;
                                            }

                                        }
                              

                                   }else
                                   {

                                     $output_response = $this->connection_add_finish_success_1($input_params);
                                     return $output_response;
                                   }     
                        }
                         else
                        {

                            $output_response = $this->connection_add_finish_success_1($input_params);
                            return $output_response;
                        }                


            }else  //end loged in user if issubscribe
            {



               $input_params = $this->get_match_user($input_params);

               if(count($input_params['get_match_user'])<=2)
                {




      

                    $condition_res = $this->condition_1($input_params);



                         if ($condition_res["success"])
                        {


                            $input_params = $this->check_eligibility_of_liking($input_params);

                            $condition_res = $this->condition_2($input_params);



                            if ($condition_res["success"])
                            {




                                $input_params = $this->delete_record($input_params);

                                $input_params = $this->set_connection_status($input_params);


                                

                                $input_params = $this->get_user_device_token($input_params);





                                
                                $input_params = $this->get_users_list_details($input_params);

                        

                                //check match user condition like and like or superlike and like or like and Superlike
                                        if((($input_params['connection_type_by_logged_user']=='Like') && ($input_params['connection_type_by_receiver_user']=='Like')) OR (($input_params['connection_type_by_logged_user']=='Superlike')))
                                        {

                                            $input_params = $this->entry_for_match($input_params);

                                            
                                            if ($input_params["u_device_token"]!=''){

                                               
                                                $input_params = $this->push_notification_1($input_params);
                                                $output_response = $this->connection_add_finish_success($input_params);
                                               return $output_response;
                                            }else{
                                            $output_response = $this->connection_add_finish_success_2($input_params);
                                                return $output_response;
                                            }

                                        }
                                        else
                                        {


             
                                           if (($input_params["connection_type"]=='Like'))
                                            {
                                              

                                                    if ($input_params["u_device_token"]!=''){ 


                                                    $input_params = $this->notification_entry($input_params);

                                                    if($input_params['u_is_subscribed_1']==1){
                                                    $input_params = $this->push_notification($input_params);
                                                    }
                                                    
                                                    $output_response = $this->connection_add_finish_success($input_params);
                                                     return $output_response;
                                                    }else
                                                    {
                                                         $output_response = $this->connection_add_finish_success_2($input_params);
                                                                    return $output_response;
                                                    }

                                            }

                                            else
                                            {



                                                $output_response = $this->connection_add_finish_success($input_params);
                                                                return $output_response;
                                            }

                                        }
                              

                                   }else
                                   {

                                     $output_response = $this->connection_add_finish_success_1($input_params);
                                     return $output_response;
                                   }     
                        }
                         else
                        {

                            $output_response = $this->connection_add_finish_success_1($input_params);
                            return $output_response;
                        }   
                    


                }else
                {

                    

                      $output_response = $this->connection_add_finish_success_4($input_params);
                            return $output_response;

                      
                }//end mathc if



            }

            
           
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }
  

    public function set_connection_status($input_params = array())
    {
        
        $this->block_result = array();
        try
        {


            $params_arr = array();
            
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtaddedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtaddedat"] = 'NOW()'; 
            }
          
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["connection_user_id"] = $input_params["connection_user_id"];
            }
           
            if (isset($input_params["connection_type"]))
            {
                $params_arr["connection_type"] = $input_params["connection_type"];
            }
            
            $this->block_result = $this->connections_model->add_connection_status($params_arr);

            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }

           
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["add_connection_status"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }



     public function get_users_list_details($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
            $arrParams=array();
             

           // $arrParams['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
             $arrParams['other_user_id'] =  isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            
            //print_r($arrParams);
            //exit;
            $this->block_result = $this->rebound_user_model->get_users_list_details($arrParams);
            
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

            //print_r($result_arr);
            //exit;
           
           if (is_array($result_arr) && count($result_arr) > 0)
            {
                

                
                foreach ($result_arr as $data_key => $data_arr)
                {

                  // print_r($data_arr["user_id"]);
                   //print_r($input_params);
                   //exit;
                
                    if((false == empty($data_arr["user_id"])) &&(false == empty($input_params['user_id'])) ){

                       
                    $strConnectionType  ='';
                   
                        $data_arr["user_id"] = $input_params['user_id'];
                         $data_arr["connection_id"] = $input_params['connection_user_id'];

                    $arrConnectionType = $this->get_users_connection_details($data_arr["user_id"], $data_arr["connection_id"]);
                  
                    if(false == empty($arrConnectionType['0']['connection_type'])){

                        $strConnectionType =$arrConnectionType['0']['connection_type'];
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  $strConnectionType ;
                    }else{
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  '' ;
                    }


                     if(false == empty($arrConnectionType['0']['connection_type_by_logged_user'])){

                        $strConnectionType =$arrConnectionType['0']['connection_type_by_logged_user'];
                        $result_arr[$data_key]["connection_type_by_logged_user"] =  $strConnectionType ;
                    }else{

                        $result_arr[$data_key]["connection_type_by_logged_user"] =  '';
                    }


                     if(false == empty($arrConnectionType['0']['connection_type_by_receiver_user'])){

                        $strConnectionType =$arrConnectionType['0']['connection_type_by_receiver_user'];
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  $strConnectionType ;
                    }
                     
                    }
                }
                //print_r($result_arr);exit;
                $this->block_result["data"] = $result_arr;
            }
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_users_list_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


     public function get_users_connection_details($user_id = '',$connection_id='')
    {

        $this->block_result = array();
        try
        {
            
            $this->block_result = $this->rebound_user_model->get_users_connection_details($user_id,$connection_id);
            
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        return $result_arr;
    }

    /**
     * is_posted method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
            $cc_lo_0 = (empty($input_params["add_connection_status"]) ? 0 : 1);
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

  
    /*public function connection_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "Connection added successfully";
        $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_connection_status");

        return $responce_arr;
    }*/

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


/**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_exist_user_connection_status($input_params = array())
    {
        $this->block_result = array();
        try
        {

            $params_arr = array();
        
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtupdatedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtupdatedat"] = "NOW()"; 
            }

            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["connection_user_id"] = $input_params["connection_user_id"];
            }
            if (isset($input_params["connection_type"]))
            {
                $params_arr["connection_type"] = $input_params["connection_type"];
            }
            

           
            
            $this->block_result = $this->connections_model->update_exist_connection_status($params_arr,$where_arr);

            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_connection_status"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        // print_r($input_params);exit;
        return $input_params;
    }



 /**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
 



    


       public function connection_add_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "connection added successfully",
        );
        $output_fields = array(
            
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
        );
        $output_keys = array(
            'get_users_list_details',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

       // print_r( $responce_arr );
        //exit;

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_add_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_added_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }





  public function connection_add_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "connection_add_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



  public function connection_add_finish_success_4($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_add_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
  
  

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    /*public function connection_update_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "connection updated successfully";
        $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "update_connection_status");

        return $responce_arr;
    }*/



     public function connection_update_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "connection updated successfully",
        );
        $output_fields = array(
            
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
        );
        $output_keys = array(
            'get_users_list_details',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

       // print_r( $responce_arr );
        //exit;

        return $responce_arr;
    }


    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_update_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_update_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "update_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


     public function get_connection_finish_success($input_params = array())
    {
        $flag = 1;
        
        $setting_fields = array(
            "success" => "1",
            "message" => "Get connection details fetched successfully.",
            "total_connection_count"=> count($input_params["get_connection_detail_v1"])

        );
        $output_fields = array(
            'user_id',
            'user_name',
            'first_name',
            'last_name',
            'user_image',
            'age',
            'connection_type'
        );
        $output_keys = array(
            'get_connection_detail_v1',
        );
        $ouput_aliases = array(
            "user_id" => "user_id",
            "user_name" => "user_name",
             "first_name" => "first_name",
              "last_name" => "last_name",
            "user_image" => "user_image",
             "connection_type" => "connection_type",
             "age" => "age",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        // print_r($output_array);
        $func_array["function"]["name"] = "get_connection_detail";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;


        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        return $responce_arr;
    }

    /**
     * mod_state_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_connection_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_connection_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_connection_detail";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }




    

}
