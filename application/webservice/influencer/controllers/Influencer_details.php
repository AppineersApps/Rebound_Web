<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of States List Controller
 *
 * @category webservice
 *
 * @package influencer
 *
 * @subpackage controllers
 *
 * @module States List
 *
 * @class Influencer_details.php
 *
 * @path application\webservice\influencer\controllers\Influencer_details.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Influencer_details extends Cit_Controller
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
            "get_influencer_details_v1",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('influencer_type_list_model');
        //Model wali file
    }

    /**
     * rules_states_list method is used to validate api input params.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_influencer_details($request_arr = array())
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
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "influencer_list");

        return $valid_res;
    }

    /**
     * start_states_list method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_influencer_details($request_arr = array(), $inner_api = FALSE)
    {
       //print_r($request_arr); exit();

        //print_r($request_arr);//"influencer_id_required
         try
        {
            $validation_res = $this->rules_influencer_details($request_arr);
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
        
            $input_params = $this->get_influencer_details_v1($input_params);

            $condition_res = $this->condition($input_params);
            if ($condition_res["success"])
            {
                $output_response = $this->mod_influencer_finish_success($input_params);
                return $output_response;
            }

            else
            {

              if($input_params['influencer_code'] == "" || $input_params['influencer_code'] == null)
              {
                $output_response = $this->mod_influencer_finish_success_1($input_params);
                return $output_response;
              }
              else if($input_params['influencer_code'] != "")
              {

                  $output_response = $this->mod_influencer_finish_success_2($input_params);
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
     * get_business_type_list_v1 method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_influencer_details_v1($input_params = array())
    {
       // print_r($input_param); // no states found

        $this->block_result = array();
        try
        {
            $params_arr = array();

            if (isset($input_params["influencer_code"]) &&(!empty($input_params["influencer_code"])))
            {
                $params_arr["influencer_code"] = $input_params["influencer_code"];
            }

                $this->block_result = $this->influencer_type_list_model->get_influencer_details($params_arr);
                
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

                        $data = $data_arr["influencer_image"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "rebound/influencer_image";
                        $p_key = ($data_arr["influencer_id"] != "") ? $data_arr["influencer_id"] : $input_params["influencer_id"];
                        $image_arr["pk"] = $p_key;
                       // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                        $data = $this->general->get_image_aws($image_arr);

                        $result_arr[$data_key]["influencer_image"] = $data;

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
        $input_params["get_influencer_details_v1"] = $this->block_result["data"];

        return $input_params;
    }


    /**
     * condition method is used to process conditions.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {
        //print_r($input_params); // no state

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_influencer_details_v1"]) ? 0 : 1);
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
     * mod_influencer_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function mod_influencer_finish_success($input_params = array())
    {
      
  /*    print_r($input_params);
      exit();
      */
        $setting_fields = array(
            "success" => "1",
            "message" => "mod_influencer_finish_success",
            //"data" => $input_params
        );

        $output_fields = array(
            'influencer_id',
            'influencer_name',
            'influencer_code',
            'influencer_image',
        );

        $output_keys = array(
            'get_influencer_details_v1',
        );
       $ouput_aliases = array(
            "get_influencer_details_v1" => "get_influencers_list",
            "iInfluencerId" => "influencer_id",
            "vInfluencerName" => "influencer_name",
            "vInfluencerCode" => "influencer_code",
            "vInfluencerImage" => "influencer_image"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "influencer_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * mod_influencer_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function mod_influencer_finish_success_1($input_params = array())
    {
        //print_r($input_params);exit;
        $setting_fields = array(
            "success" => "0",
            "message" => "mod_influencer_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "influencer_list";//Function name
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

      /**
     * mod_influencer_finish_success_2 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function mod_influencer_finish_success_2($input_params = array())
    {
        //print_r($input_params);exit;
        $setting_fields = array(
            "success" => "1",
            "message" => "mod_influencer_finish_success_2",
        );
         $output_fields = array(
            'user_id',
            'influencer_code',
        );
        $responce_arr["settings"] = $setting_fields;
        $responce_arr["settings"]["fields"] = $output_fields;
        $responce_arr["data"] = array($input_params);

        return $responce_arr;
    }
}
