<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Get Message List Controller
 *
 * @category webservice
 *
 * @package friends
 *
 * @subpackage controllers
 *
 * @module Get Message List
 *
 * @class Get_message_list.php
 *
 * @path application\webservice\friends\controllers\Get_message_list.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.06.2019
 */

class Get_message_list extends Cit_Controller
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
            "custom_function",
            "get_message",
        );
        $this->block_result = array();

       $this->load->library('wsresponse');
        $this->load->model('get_message_list_model');
        $this->load->model("comments/messages_model");
        $this->load->model("basic_appineers_master/users_model");
       $this->load->model("basic_appineers_master/rebound_user_model");
    }

    /**
     * rules_get_message_list method is used to validate api input params.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_message_list($request_arr = array())
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
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_message_list");

        return $valid_res;
    }

    /**
     * start_get_message_list method is used to initiate api execution flow.
     * @created priyanka chillakuru | 09.05.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_get_message_list($request_arr = array(), $inner_api = FALSE)
     {
        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
          case 'GET':
             $output_response = $this->get_message_list($request_arr);
             return  $output_response;
             break; 
         case 'DELETE':
             $output_response = $this->delete_message($request_arr);
             return  $output_response;
             break; 
              
        }

    }

    public function get_message_list($request_arr = array()){
        try
        {
            $validation_res = $this->rules_get_message_list($request_arr);
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

    
             $input_params = $this->custom_function($input_params);


            $input_params = $this->get_message($input_params);

           

            $condition_res = $this->check_for_message($input_params);

            if ($condition_res["success"])
            {
                        
                $input_params = $this->start_loop($input_params);

                $output_response = $this->messages_finish_success_1($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->messages_finish_success($input_params);
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
     * custom_function method is used to process custom function.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this->general, "prepareWhere"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->general->prepareWhere($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * get_message method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_message($input_params = array())
    {
       //print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            // $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
             $status = isset($input_params["status"]) ? $input_params["status"] : "";


            // $this->block_result = $this->messages_model->get_message($user_id,$receiver_id);
            $where_clause = isset($input_params["where_clause"]) ? $input_params["where_clause"] : "";
            $this->block_result = $this->messages_model->get_message($where_clause, $status);
            
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
                    $strConnectionType  ='';
                    if($data_arr["sender_id"] == $user_id){
                        $data_arr["sender_id"] = $data_arr["receiver_id"];
                    }else{
                        $data_arr["sender_id"] = $data_arr["sender_id"];
                    }
                   //echo $user_id; 
                    /* echo $data_arr["user_id"];
                    echo $receiver_id;exit;*/

                    $arrConnectionType = $this->get_users_connection_details($user_id,$data_arr["sender_id"],$data_arr["receiver_id"]);
                    
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

                     //print_r($result_arr);exit;

                    //****************************
                    $data1 = $data_arr["message_upload"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data1;
                     $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                    $image_arr["path"] ="rebound/chat_uploads";
                    $data1 = $this->general->get_image_aws($image_arr);
                    
                    $result_arr[$data_key]["message_upload"] = (false == empty($data1))?$data1:"";

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
        $input_params["get_message"] = $this->block_result["data"];

        //print_r($input_params);
        //exit;


        return $input_params;


    }
    /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_users_connection_details($user_id = '',$connection_id='',$other_user_id='')
    {

        $this->block_result = array();
        try
        {
            
            $this->block_result = $this->rebound_user_model->get_users_connection_details($user_id,$connection_id,$other_user_id);
            
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
     * check_for_message method is used to process conditions.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_message($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_message"]) ? 0 : 1);
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
     * start_loop method is used to process loop flow.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function start_loop($input_params = array())
    {

        
        $this->iterate_start_loop($input_params["get_message"], $input_params);
        return $input_params;
    }

    /**
     * get_send_image method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_send_image($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $sender_id = isset($input_params["sender_id"]) ? $input_params["sender_id"] : "";
            $this->block_result = $this->users_model->get_send_image($sender_id);
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
                    $image_arr["path"] = "Rebound/user_profile";
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
        $input_params["get_send_image"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_receiver_images method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_receiver_images($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->users_model->get_receiver_images($receiver_id);
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
                    $data = $data_arr["ui_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = "Rebound/user_profile";
                    $data = $this->general->get_image_aws($image_arr);
                    $result_arr[$data_key]["ui_image"] = $data;

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
        $input_params["get_receiver_images"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * custom_function_1 method is used to process custom function.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function_1($input_params = array())
    {
        if (!method_exists($this, "format_images"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->format_images($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function_1"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;  
    }
    /**
     * delete_notification method is used to initiate api execution flow.
     * @created aditi billore | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function delete_message($request_arr = array())
    {
      try
        {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;

            $input_params = $this->check_message_exist($input_params);
            if ($input_params["status"])
            {


               $input_params = $this->delete_selected_message($input_params);
               if ($input_params["affected_rows"])
                {
                    $output_response = $this->delete_message_finish_success($input_params);
                    return $output_response;
                }else{
                    $output_response = $this->delete_message_finish_success_1($input_params);
                    return $output_response;
                }
              
            }

            else
            {
                $output_response = $this->delete_message_finish_success_2($input_params);
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
     * check_notification_exist method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_message_exist($input_params = array())
    {

        if (!method_exists($this, "checkMessageExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $input_params['message_id'] = isset($input_params['message_id']) ? $input_params['message_id'] : "";
           if(is_array($input_params['message_id'])){
             $input_params['message_id'] = implode(",",$input_params['message_id']);
           }
            
            $result_arr["data"] = $this->checkMessageExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        /*print_r($format_arr);exit;*/
        $input_params["checkmessagestatus"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        //print_r($input_params);
        return $input_params;
    }

    /**
     * iterate_start_loop method is used to iterate loop.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $get_message_lp_arr get_message_lp_arr array to iterate loop.
     * @param array $input_params_addr $input_params_addr array to address original input params.
     */
    public function iterate_start_loop(&$get_message_lp_arr = array(), &$input_params_addr = array())
    {


        $input_params_loc = $input_params_addr;
        $_loop_params_loc = $get_message_lp_arr;
        $_lp_ini = 0;


        $_lp_end = count($_loop_params_loc);
        for ($i = $_lp_ini; $i < $_lp_end; $i += 1)
        {
            $get_message_lp_pms = $input_params_loc;

            unset($get_message_lp_pms["get_message"]);
            if (is_array($_loop_params_loc[$i]))
            {
                $get_message_lp_pms = $_loop_params_loc[$i]+$input_params_loc;
            }
            else
            {
                $get_message_lp_pms["get_message"] = $_loop_params_loc[$i];
                $_loop_params_loc[$i] = array();
                $_loop_params_loc[$i]["get_message"] = $get_message_lp_pms["get_message"];
            }

            $get_message_lp_pms["i"] = $i;
            $input_params = $get_message_lp_pms;

            $input_params = $this->get_send_image($input_params);

            $input_params = $this->get_receiver_images($input_params);


            $input_params = $this->custom_function_1($input_params);


            $get_message_lp_arr[$i] = $this->wsresponse->filterLoopParams($input_params, $_loop_params_loc[$i], $get_message_lp_pms);


           
        }
    }

     /**
     * delete message method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_selected_message($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
            //print_r($input_params["message_id"]);exit;
           
            $arrResult['message_id']  = isset($input_params["message_id"]) ? $input_params["message_id"] : "";
            /* print_r($arrResult['message_id']);exit;*/
           if(is_array($arrResult['message_id'])){
             $arrResult['message_id'] = implode(",",$arrResult['message_id']);
           }
          /*  print_r($arrResult['message_id']);exit;*/
            $this->block_result = $this->messages_model->delete_message_id($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_message"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
      /*  print_r($input_params);exit;*/
       return $input_params;

    }


    /**
     * messages_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success_1($input_params = array())
    {
       // echo "sucess m here";
        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success_1",
        );
        $output_fields = array(
            'message_id',
            'sender_id',
            'receiver_id',
            'message',
            'sender_name',
            'receiver_name',
            'updated_at',
            'sender_image',
            'receiver_image',
            'message_upload',
            'message_date',
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
        );
        $output_keys = array(
            'get_message',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_message_list";
        $func_array["function"]["output_keys"] = $output_keys;
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
            "success" => "0",
            "message" => "messages_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_message_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

  /**
     * users_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified kavita Sawant | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_message_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "Deleted successfully",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;


        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    
     /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_message_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "Message delete failed",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
     /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_message_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "Message not found in the sytem",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

}
