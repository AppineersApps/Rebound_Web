<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Login Email Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module User Login Email
 *
 * @class User_login_email.php
 *
 * @path application\webservice\basic_appineers_master\controllers\User_login_email.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Get_user_details extends Cit_Controller
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
            "get_user_login_details",
        );
        $this->multiple_keys = array();
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('rebound_user_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * rules_user_login_email method is used to validate api input params.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_user_details($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            "other_user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "other_user_id_required",
                )
            ),

           
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "user_profiles");

        return $valid_res;
    }

    /**
     * start_user_login_email method is used to initiate api execution flow.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_get_user_details($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_get_user_details($request_arr);
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
           	//print_r($input_params); exit;

               $input_params = $this->get_user_login_details($input_params);
 
                $condition_res = $this->check_user_exists($input_params);
                if ($condition_res["success"])
                {


                    $output_response = $this->users_finish_success_3($input_params);
                    return $output_response;
                       
                }

                else
                {

                    $output_response = $this->users_finish_success($input_params);
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
     * check_status method is used to process conditions.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_status($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["status"];
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
     * get_user_login_details method is used to process query block.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_login_details($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $arrParams['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $arrParams['other_user_id'] = isset($input_params["other_user_id"]) ? $input_params["other_user_id"] : ""; 

            $this->block_result = $this->users_model->get_user_details($arrParams['other_user_id']);

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

                     $strConnectionType  ='';
                       if($data_arr["u_user_id"] == $arrParams['user_id'])
                       {
                          $data_arr["u_user_id"] = $arrParams['other_user_id'];
                       }
                  
                        $arrConnectionType = $this->get_users_connection_details($arrParams['user_id'],$data_arr["u_user_id"],$arrParams['other_user_id']);
                      
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

                    $data = $data_arr["u_image1"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/personal_images";
                        $data = $this->general->get_image_aws($image_arr);
                        $result_arr[$data_key]["u_image1"] = $data;

                         $data = $data_arr["u_image2"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/personal_images";
                        $data = $this->general->get_image_aws($image_arr);
                        $result_arr[$data_key]["u_image2"] = $data;


                         $data = $data_arr["u_image3"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/personal_images";
                        $data = $this->general->get_image_aws($image_arr);
                        $result_arr[$data_key]["u_image3"] = $data;

                        $data = $data_arr["u_image4"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/personal_images";
                        $data = $this->general->get_image_aws($image_arr);
                        $result_arr[$data_key]["u_image4"] = $data;

                        $data = $data_arr["u_image5"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/personal_images";
                        $data = $this->general->get_image_aws($image_arr);
                        $result_arr[$data_key]["u_image5"] = $data;

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
        $input_params["get_user_login_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
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
     * check_user_exists method is used to process conditions.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_user_exists($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_user_login_details"]) ? 0 : 1);
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
     * users_finish_success_3 method is used to process finish flow.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success_3",
        );
        $output_fields = array(
            'u_user_id',
            'email_user_name',
            'u_first_name',
            'u_last_name',
            'u_profile_image',
            'u_email',
            'u_mobile_no',
            'u_dob',
            'u_gender',
            'u_sexual_perference',
            'u_about',
            'u_image1',
            'u_image2',
            'u_image3',
            'u_image4',
            'u_image5',
            'city',
            'state',
            'u_Height',
            'u_Weight',
            'u_BodyType',
            'u_Sign',
            'u_Education',
            'u_Profession',
            'u_state_name',
            'u_zip_code',
            'u_city',
            'u_InfluencerCode',
            'u_IsSubscribed',
            'u_latitude',
            'u_longitude',
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
        );
        $output_keys = array(
            'get_user_login_details',
        );
        $ouput_aliases = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_profiles";
        $func_array["function"]["output_keys"] = $output_keys;
       // $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    
    /**
     * users_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_profiles";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

}
