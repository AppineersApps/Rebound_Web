<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Message Controller
 *
 * @category webservice
 *
 * @package friends
 *
 * @subpackage controllers
 *
 * @module Send Message
 *
 * @class Send_message.php
 *
 * @path application\webservice\friends\controllers\Send_message.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Send_message extends Cit_Controller
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
            "if_blocked",
            "check_chat_intiated_or_not",
            "update_message",
            "get_user_details_for_send_notifi",
            "post_notification",
            "get_sender_image",
            "add_message",
        );
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('send_message_model');
        $this->load->model("friends/blocked_user_model");
        $this->load->model("comments/messages_model");
        $this->load->model("notifications/notification_model");
        $this->load->model("basic_appineers_master/users_model");
    }

   
    /**
     * rules_send_message method is used to validate api input params.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Devangi Nirmal | 31.07.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_send_message($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            "receiver_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "receiver_id_required",
                )
            ),
           "firebase_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "firebase_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "send_message");

        return $valid_res;
    }

    /**
     * start_send_message method is used to initiate api execution flow.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Devangi Nirmal | 31.07.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_send_message($request_arr = array(), $inner_api = FALSE)
    {
       
        try
        {
            
            $validation_res = $this->rules_send_message($request_arr);

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

           $input_params = $this->if_blocked($input_params);


            $condition_res = $this->is_blocked($input_params);

        
           if ($condition_res["success"])
            {

                $input_params = $this->check_chat_intiated_or_not($input_params);

 
                $condition_res = $this->is_intiated($input_params);

                if ($condition_res["success"])
                {
                //    echo "msg updating";
                    $input_params = $this->update_message($input_params);
                }

                else
                {
               // echo "msg adding";
                $input_params = $this->add_message($input_params);

                }
  
                    $input_params = $this->get_user_details_for_send_notifi($input_params);
             
                    $input_params = $this->custom_function($input_params);

            
                    $input_params = $this->post_notification($input_params);

 
                    $input_params = $this->get_sender_image($input_params);
                    
                    $condition_res = $this->check_receiver_device_token($input_params);

                    if ($condition_res["success"])
                    {

                        $input_params = $this->push_notification($input_params);

                        $output_response = $this->messages_finish_success_1($input_params);
                        return $output_response;
                    }

                    else
                    {

                        $output_response = $this->messages_finish_success($input_params);
                        return $output_response;
                    }
                

                
            }
            else
            {

                $output_response = $this->blocked_user_finish_success($input_params);
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
     * if_blocked method is used to process query block.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 04.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function if_blocked($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->blocked_user_model->if_blocked($user_id, $receiver_id);
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
        $input_params["if_blocked"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_blocked method is used to process conditions.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 30.05.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_blocked($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["if_blocked"]) ? 0 : 1);
            $cc_ro_0 = 0;

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
     * check_chat_intiated_or_not method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_chat_intiated_or_not($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $firebase_id = isset($input_params["firebase_id"]) ? $input_params["firebase_id"] : "";
            $this->block_result = $this->messages_model->check_chat_intiated_or_not($user_id, $receiver_id, $firebase_id);
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
        $input_params["check_chat_intiated_or_not"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_intiated method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_intiated($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["check_chat_intiated_or_not"]) ? 0 : 1);
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
     * update_message method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_message($input_params = array())
    {
        //print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["m_message_id"]))
            {
                $where_arr["m_message_id"] = $input_params["m_message_id"];
            }
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["receiver_id"]))
            {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["message"]))
            {
                $params_arr["message"] = $input_params["message"];
            }
            
            $params_arr["_dtmodifieddate"] = "NOW()";
            $this->block_result = $this->messages_model->update_message($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * add_message method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 21.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function add_message($input_params = array())
    {
        //print_r($inpu_params);exit;
        $this->block_result = array();
        try
        {

            $params_arr = array();
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["receiver_id"]))
            {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["firebase_id"]))
            {
                $params_arr["firebase_id"] = $input_params["firebase_id"];
            }
            if (isset($input_params["message"]))
            {
                $params_arr["message"] = $input_params["message"];
            }
           
            $params_arr["_dtaddeddate"] = "NOW()";
            $params_arr["_dtmodifieddate"] = "NOW()";
            $this->block_result = $this->messages_model->add_message($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["add_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * get_user_details_for_send_notifi method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->messages_model->get_user_details_for_send_notifi($user_id, $receiver_id);
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
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * custom_function method is used to process custom function.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
   public function custom_function($input_params = array())
    {
        if (!method_exists($this, "PrepareHelperMessage"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->PrepareHelperMessage($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

     /**
     * post_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_notification($input_params = array())
    {
        //print_r($input_params);
        $this->block_result = array();
        try
        {

            $params_arr = array();
            if (isset($input_params["notification_message"]))
            {
                $params_arr["notification_message"] = $input_params["notification_message"];
            }
            if (isset($input_params["receiver_id"]))
            {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            $params_arr["_enotificationtype"] = "Message";
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Unread";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }



            //check if same notification exists:
            $params_arr["check_notification_exists"] = $this->check_notification_exists($params_arr);
            //print_r($params_arr["check_notification_exists"]);exit;
            $this->block_result = $this->notification_model->post_notification($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    public function check_notification_exists($params_arr = array()){
        if (!method_exists($this, "checkNotificationExists"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkNotificationExists($params_arr);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checknotificationexists"] = $format_arr;

        $params_arr = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $params_arr;
    }

    /**
     * get_sender_image method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_sender_image($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $s_users_id = isset($input_params["s_users_id"]) ? $input_params["s_users_id"] : "";
            $this->block_result = $this->users_model->get_sender_image($s_users_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                  $data =array();
                    $data = $data_arr["u_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = "fern/user_profile";
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["u_image"] = $data;

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
        $input_params["get_sender_image"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * check_receiver_device_token method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
    {
      
        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["r_device_token"];

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
     * push_notification method is used to process mobile push notification.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
        $this->block_result = array();
        try
        {

            $device_id = $input_params["r_device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "type",
                    "value" => "Message",
                    "send" => "Yes",
                ),
                array(
                    "key" => "receiver_id",
                    "value" => $input_params["r_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["s_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_name",
                    "value" => $input_params["s_name"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_profile",
                    "value" => $input_params["s_profile_image"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_image",
                    "value" => $input_params["ui_image"],
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["notification_message"]."";
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
     * messages_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success_1($input_params = array())
    {
      
        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * messages_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * blocked_user_finish_success method is used to process finish flow.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 31.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "3",
            "message" => "blocked_user_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
