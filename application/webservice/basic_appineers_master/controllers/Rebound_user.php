<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email.php
 *
 * @path application\webservice\basic_appineers_master\controllers\User_sign_up_email.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Rebound_user extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    

     public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "get_users_list_details",
        );

        $this->multiple_keys = array();
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('rebound_user_model');
        $this->load->model("basic_appineers_master/users_model");
      
       
    }

     

    /**
     * rules_user_sign_up_email method is used to validate api input params.
     * @created kavita Sawant | 25-05-2020
     * @modified kavita Sawant | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_users_list($request_arr = array())
    {
       
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_users_list");
        return $valid_res;
    }
    

    /**
     * start_user_sign_up_email method is used to initiate api execution flow.
     * @created kavita Sawant | 25-05-2020
     * @modified kavita Sawant | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_rebound_user($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];

        $output_response = array();
        switch ($method) {
          case 'GET':
             $output_response = $this->get_users_list($request_arr);
             return  $output_response;
             break;
        case 'POST':
             $output_response = $this->set_profile($request_arr);
             return  $output_response;
             break;  
         case 'DELETE':
             $output_response = $this->delete_media_images($request_arr);
             return  $output_response;
             break; 
              
        }
    }
    
	public function get_users_list($request_arr = array(), $inner_api = FALSE)
   { 
		try
		{
            
            //http://18.211.58.235/mad_collab/WS/mad_collab_user?gender_type=All
			$validation_res = $this->rules_get_users_list($request_arr);
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
          
			$input_params = $this->get_users_list_details($input_params);
			$condition_res = $this->is_posted($input_params);
			if ($condition_res["success"])
			{
				//print_r($input_params);exit();  
				$output_response = $this->users_finish_success($input_params);
				return $output_response;
			}

			else
			{

				$output_response = $this->users_finish_success_1($input_params);
			
				return $output_response;
			}
		}
		catch(Exception $e)
		{
			$message = $e->getMessage();
		}
		return $output_response;
  }

 public function prepare_distance($input_params = array())
    {
        if (!method_exists($this, "prepareDistanceQuery"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->prepareDistanceQuery($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["prepare_distance"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }
 
    /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_users_list_details($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
            $arrParams=array();

            if ((isset($input_params['min_radius']) && $input_params['min_radius'] != "") && (isset($input_params['max_radius']) && $input_params['max_radius'] != ""))
            {
               $input_params = $this->prepare_distance($input_params);

            }

            $arrParams['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $arrParams['min_radius'] = isset($input_params["min_radius"]) ? $input_params["min_radius"] : "";

            $arrParams['max_radius'] = isset($input_params["max_radius"]) ? $input_params["max_radius"] : "";

            $arrParams['gender'] = isset($input_params["gender"]) ? $input_params["gender"] : "";
            $arrParams['min_age'] = isset($input_params["min_age"]) ? $input_params["min_age"] : "";
            $arrParams['max_age'] = isset($input_params["max_age"]) ? $input_params["max_age"] : "";
            $arrParams['other_user_id'] = isset($input_params["other_user_id"]) ? $input_params["other_user_id"] : "";

           /* $arrParams['search_radius'] = isset($input_params["search_radius"]) ? $input_params["search_radius"] : "";*/
            
             $arrParams['distance'] = isset($input_params["distance"]) ? $input_params["distance"] : "";
      
            $this->block_result = $this->rebound_user_model->get_users_list_details($arrParams);

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
                         $strConnectionType  ='';
                       if($data_arr["user_id"] == $arrParams['user_id'])
                       {
                          $data_arr["user_id"] = $arrParams['other_user_id'];
                       }
                  
                        $arrConnectionType = $this->get_users_connection_details($arrParams['user_id'],$data_arr["user_id"],$arrParams['other_user_id']);
                      
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

                        $data = $data_arr["user_image"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $dest_path = "rebound/user_profile";
                        $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                         $data = $this->general->get_image_aws($image_arr);

                        $result_arr[$data_key]["user_image"] = $data;
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
        $input_params["get_users_list_details"] = $this->block_result["data"];
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
            //echo "used id is---".$input_params["user_id"]; exit();
            $cc_lo_0 = $input_params["user_id"];
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
    

    public function users_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "Users details fetched successfully",
        );
        $output_fields = array(
            'user_id',
            'user_name',
            'u_first_name',
            'u_last_name',
            'user_image',
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
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
            'u_Height',
            'u_Weight',
            'u_BodyType',
            'u_Sign',
            'u_Education',
            'u_Profession',
        );
        $output_keys = array(
            'get_users_list_details',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);

        //$output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
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
    
     /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "No data found",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    
  
}
