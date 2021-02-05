<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
* Description of User Sign Up Email Model
*
* @category webservice
*
* @package basic_appineers_master
*
* @subpackage models
*
*
* @class Rebound_user_model.php
*
* @path application\webservice\basic_appineers_master\models\Rebound_user_model.php
*
* @version 4.4
*
* @author CIT Dev Team
*
* @since 12.02.2020
*/

class Rebound_user_model extends CI_Model
{
/**
* __construct method is used to set model preferences while model object initialization.
*/
public function __construct()
{
parent::__construct();
$this->load->helper('listing');
}




public function get_posted_details_v1($user_id = '')
{
try
{
$result_arr = array();


$this->db->from("user_media AS p");


$this->db->select("p.iMediaId AS media_id"); //
$this->db->select("p.vMediaType AS media_type"); //
$this->db->select("p.vMediaUrl AS media_url"); //
$this->db->select("p.vMediaName AS media_name"); //
$this->db->select("p.vMediaFile AS media_file"); //


$this->db->select("p.dtAddedAt AS p_updated_date");
$this->db->select("(DATE_FORMAT(p.dtAddedAt,\"%b %d %Y\")) AS added_at", FALSE); ///



if (isset($user_id) && $user_id != "")
{
$this->db->where("p.iUserId =", $user_id);
}

// $this->db->limit(1);

$result_obj = $this->db->get();
$result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
if (!is_array($result_arr) || count($result_arr) == 0)
{
throw new Exception('No records found.');
}
$success = 1;
}
catch(Exception $e)
{
$success = 0;
$message = $e->getMessage();
}

$this->db->_reset_all();
//echo $this->db->last_query();
$return_arr["success"] = $success;
$return_arr["message"] = $message;
$return_arr["data"] = $result_arr;
return $return_arr;
}



/**
* get_users_list method is used to execute database queries for User Sign Up Email API.
* @created Kavita sawant | 27.05.2020
* @modified Kavita sawant | 27.05.2020
* @param string $insert_id insert_id is used to process query block.
* @return array $return_arr returns response of query block.
*/
public function get_users_list_details($arrParams = '')
{
	try
	{


		$page_no = 1;
          $start_offset = 0;
          $end_offset =  $this->config->item("PAGINATION_ROW_COUNT");

          if($arrParams["page_no"] != "" )
          {
              $page_no = isset($arrParams["page_no"]) ? $arrParams["page_no"] : 1;

              $start_offset = ($page_no * $end_offset) - $end_offset;
          }


		$result_arr = $old_arr = $tmp_arr = array();

		$this->db->from("users AS u");
		$this->db->select("u.iUserId AS user_id");
		$this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
		$this->db->select("u.vFirstName AS u_first_name");
		$this->db->select("u.vLastName AS u_last_name");
		$this->db->select("u.vEmail AS u_email");
		$this->db->select("u.vMobileNo AS u_mobile_no");
		$this->db->select("u.vProfileImage AS user_image");
		$this->db->select("u.eGender AS u_gender");
		//$this->db->select("u.vReligion AS u_religion");
		$this->db->select("u.eSexualPrefrence AS u_sexual_perference");

		$this->db->select("u.tAboutYou AS u_about");
		$this->db->select("u.vImage1 AS u_image1");
		$this->db->select("u.vImage2 AS u_image2");
		$this->db->select("u.vImage3 AS u_image3");
		$this->db->select("u.vImage4 AS u_image4");
		$this->db->select("u.vImage5 AS u_image5");
		$this->db->select("u.dDob AS u_dob");
		$this->db->select("u.tAddress AS u_address");
		$this->db->select("u.vCity AS city");
		$this->db->select("u.dLatitude AS u_latitude");
		$this->db->select("u.dLongitude AS u_longitude");
		$this->db->select("u.iStateId AS u_state_id");
		$this->db->select("u.vStateName AS state");
		$this->db->select("u.vZipCode AS u_zip_code");
		$this->db->select("u.eStatus AS u_status");

		$this->db->select("u.dHeight AS u_Height");
	    $this->db->select("u.iWeight AS u_Weight");
	    $this->db->select("u.vBodyType AS u_BodyType");
	    $this->db->select("u.vSign AS u_Sign");
	    $this->db->select("u.vEducation AS u_Education");
	    $this->db->select("u.vProfession AS u_Profession");
	    $this->db->select("u.vInfluencerCode AS u_InfluencerCode");
        $this->db->select("u.eIsSubscribed AS u_IsSubscribed");


		$this->db->select("(".$this->db->escape("").") AS connection_type_by_logged_user", FALSE);
		$this->db->select("(".$this->db->escape("").") AS connection_type_by_receiver_user", FALSE);

		if (isset($arrParams['gender']) && $arrParams['gender'] != "" && $arrParams['gender'] !='All')
		{

			if($arrParams['gender']=='Both')
			{
			$sqlquery="u.eGender IN('Male','Female')";
			$this->db->where($sqlquery);

			}else{

			$this->db->where("u.eGender =", $arrParams['gender']);
			}

		}

		if ((isset($arrParams['min_radius']) && $arrParams['min_radius'] > 0) && (isset($arrParams['max_radius']) && $arrParams['max_radius'] > 0))
		{

			//$this->db->where("FLOOR(".$arrParams['distance'].") ", $arrParams['search_radius']);

			$this->db->where("FLOOR('".$arrParams['distance']."') BETWEEN '". $arrParams['min_radius']." ' and '". $arrParams['max_radius']."'");
		}

		if ((isset($arrParams['min_age']) && $arrParams['min_age'] != "") && (isset($arrParams['max_age']) && $arrParams['max_age'] != ""))
		{

	        $min_age = $arrParams['min_age'];
	        $minlastYear = date("Y", strtotime("-$min_age years"));

	        $max_age = $arrParams['max_age'];
	        $maxlastYear = date("Y", strtotime("-$max_age years"));
	        // echo "last yr--".$lastYear;

	       // $where[]="YEAR(dDob) <='".$lastYear."'";

		$this->db->where('YEAR(dDob) BETWEEN "'. $maxlastYear. '" and "'. $minlastYear.'"');
		}

		if ((isset($arrParams['other_user_id']) && $arrParams['other_user_id'] != ""))
		{
		$this->db->where("u.iUserId =", $arrParams['other_user_id']);
		}else
		{

			$strWhere = "u.iUserId not in (SELECT DISTINCT(u.iUserId) AS user_id
			FROM users AS u
			LEFT JOIN users_connections AS uc ON uc.iConnectionUserId = u.iUserId
			LEFT JOIN users_connections AS ub ON ub.iUserId = u.iUserId
			WHERE u.eStatus = 'Active' AND (uc.iUserId = '".$arrParams['user_id']."')) AND u.iUserId <> '".$arrParams['user_id']."'";
			if (isset($strWhere) && $strWhere != "")
			{
			$this->db->where($strWhere);
			}

			$strWhere1 = "u.iUserId not in (SELECT DISTINCT(u.iUserId) AS user_id
			FROM users AS u
			LEFT JOIN user_block AS uc ON uc.iBlockUserId = u.iUserId
			LEFT JOIN user_block AS ub ON ub.iUserId = u.iUserId
			WHERE u.eStatus = 'Active' AND (uc.iUserId = '".$arrParams['user_id']."')) AND u.iUserId <> '".$arrParams['user_id']."'";
			if (isset($strWhere1) && $strWhere1 != "")
			{
			$this->db->where($strWhere1);
			}


			$strWhere3 = "u.iUserId not in (SELECT DISTINCT(u.iUserId) AS user_id
                FROM users AS u
                LEFT JOIN notification AS uc ON uc.iReceiverId = u.iUserId 
                LEFT JOIN notification AS ub ON ub.iSenderId = u.iUserId 
                WHERE u.eStatus = 'Active' AND ( uc.iSenderId = '".$arrParams['user_id']."' OR (ub.iReceiverId = '".$arrParams['user_id']."'))  AND uc.eNotificationType='Message') AND u.iUserId <> '".$arrParams['user_id']."'";

			if (isset($strWhere3) && $strWhere3 != "")
			{
			$this->db->where($strWhere3);
			}



			//$this->db->group_by('user_id');



		}

		$this->db->where("u.eStatus =", 'Active');

		   if($arrParams["page_no"] != "" )
          {
             $this->db->limit($end_offset,$start_offset); 
          }
		$result_obj = $this->db->get();
		//echo $this->db->last_query();exit;
		$result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

		if (!is_array($result_arr) || count($result_arr) == 0)
		{
		throw new Exception('No records found.');
		}
		$success = 1;
	}
	catch(Exception $e)
	{
	$success = 0;
	$message = $e->getMessage();
	}

	$this->db->_reset_all();
	// echo $this->db->last_query();
	$return_arr["success"] = $success;
	$return_arr["message"] = $message;
	$return_arr["data"] = $result_arr;
	return $return_arr;
}
/**
* get_users_list method is used to execute database queries for User Sign Up Email API.
* @created Kavita sawant | 27.05.2020
* @modified Kavita sawant | 27.05.2020
* @param string $insert_id insert_id is used to process query block.
* @return array $return_arr returns response of query block.
*/
public function get_users_connection_details($user_id = '',$connection_id='',$other_user_id='')
{
		try
		{

			$result_arr = array();

			$strSql=
			"SELECT '' AS connection_type,
			(SELECT eConnectionType
			FROM users_connections
			WHERE iUserId=".$user_id." AND iConnectionUserId = ".$connection_id.") AS connection_type_by_logged_user,
			(SELECT eConnectionType
			FROM users_connections
			WHERE iUserId=".$connection_id." AND iConnectionUserId = ".$user_id.") AS connection_type_by_receiver_user
			FROM users_connections LIMIT 1";



			$result_obj = $this->db->query($strSql);
			//echo $this->db->last_query();exit;
			$result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

			if(isset($result_arr[0]['connection_type_by_logged_user'])){

			$result_arr[0]['connection_type_by_logged_user'] = $result_arr[0]['connection_type_by_logged_user'];
			}
			else if(isset($result_arr[0]['connection_type_by_receiver_user'])){

			$result_arr[0]['connection_type_by_receiver_user'] = $result_arr[0]['connection_type_by_receiver_user'];
			}
			if (!is_array($result_arr) || count($result_arr) == 0)
			{
			throw new Exception('No records found.');
			}
			$success = 1;

		}
		catch(Exception $e)
		{
		$success = 0;
		$message = $e->getMessage();
		}

		$this->db->_reset_all();
		// echo $this->db->last_query();exit;
		$return_arr["success"] = $success;
		$return_arr["message"] = $message;
		$return_arr["data"] = $result_arr;
		return $return_arr;
}



}