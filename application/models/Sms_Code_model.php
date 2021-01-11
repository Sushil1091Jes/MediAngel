<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Sms Code model
 *
 * @package Models
 */
class Sms_Code_model extends EA_Model {
    /**
     * Sms_Code_model constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('general');
        $this->load->helper('string');
    }

    
     /**
     * generate a new verification code and send it with an email.
     *
     * @param string $email User's email.
     *
     * @return string verification_code.
     */
    public function add($email)
    {
         // verification_code
        $verification_code = random_string('numeric', 6);
        // utc date
        $date_utc = date("Y-m-d H:i:s", time() - date("Z"));
        $sms_code = [
                'email' => $email,
                'validate_code' => $verification_code,
                'is_used' =>FALSE,
                'create_date' =>$date_utc
            ];
        $this->db->insert('sms_code', $sms_code);
        return $verification_code;
    }


     /**
     * Get all, or specific records from sms_code's table.
     *
     * Example:
     *
     * $this->model->get_batch(['id' => $record_id]);
     *
     * @param mixed $where
     * @param mixed $order_by
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where = NULL, $order_by = NULL, $limit = NULL, $offset = NULL)
    {
        if ($where !== NULL)
        {
            $this->db->where($where);
        }
        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }
        return $this->db->get('sms_code', $limit, $offset)->result_array();
    }

   /**
     * Update sms_code record.
     *
     * @param array $sms_code Contains the sms_code data. The record id needs to be included in the array.
     *
     * @throws Exception If sms_code record could not be updated.
     */
    public function update($sms_code)
    {
        $this->db->where('id', $sms_code['id']);
        if ( ! $this->db->update('sms_code', $sms_code))
        {
            throw new Exception('Could not update sms_code record');
        }
    }
}
