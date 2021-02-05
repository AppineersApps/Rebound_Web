<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Setting Model
 *
 * @category notification
 *
 * @package tools
 *
 * @subpackage models
 *
 * @module Setting
 *
 * @class Setting_model.php
 *
 * @path application\notification\tools\models\Setting_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */

class Setting_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
    }

    /**
     * get_likes_per_day method is used to execute database queries for Manage Likes Count notification.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @return array $return_arr returns response of query block.
     */
    public function get_likes_per_day()
    {
        try
        {
            $result_arr = array();

            $this->db->from("mod_setting AS ms");

            $this->db->select("ms.vName AS ms_name");
            $this->db->select("ms.vValue AS ms_value");
            $this->db->where("ms.vName =", "LIKES_PER_DAY");

            $this->db->limit(1);

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
}
