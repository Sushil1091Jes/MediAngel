<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * MediAngel - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * service categories template Model
 *
 * @package Models
 */
class Categories_Template_model extends EA_Model {
    /**
     * Customers_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('data_validation');
    }

     /**
     * Add (insert or update) a service category template record into database.
     *
     * @param array $category Contains the service category template data.
     *
     * @return int Returns the record ID.
     *
     * @throws Exception If service category template data are invalid.
     */
    public function add($categories_template)
    {
        if ( ! $this->validate($categories_template))
        {
            throw new Exception('Service category template data are invalid.');
        }
        if ( ! isset($categories_template['id']))
        {
            $this->db->insert('service_categories_template', $categories_template);
            $categories_template['id'] = $this->db->insert_id();
        }
        else
        {
            $this->db->where('id', $categories_template['id']);
            $this->db->update('service_categories_template', $categories_template);
        }
        return (int)$categories_template['id'];
    }

    /**
     * Validate a service category template record data. This method must be used before adding
     * a service category record into database in order to secure the record integrity.
     *
     * @param array $category Contains the service category data.
     *
     * @return bool Returns the validation result.
     *
     * @throws Exception If required fields are missing.
     */
    public function validate($categories_template)
    {
        try
        {
            // Required Fields
            if ( ! isset($categories_template['name']))
            {
                throw new Exception('Not all required fields where provided ');
            }

            if ($categories_template['name'] == '' || $categories_template['name'] == NULL)
            {
                throw new Exception('Required fields cannot be empty or null ($categories_template: '
                    . print_r($categories_template, TRUE) . ')');
            }

            return TRUE;
        }
        catch (Exception $exception)
        {
            return FALSE;
        }
    }

    /**
     * Delete a service category template record from the database.
     *
     * @param int $category_template_id Record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception if Service category record was not found.
     */
    public function delete($category_template_id)
    {
        if ( ! is_numeric($category_template_id))
        {
            throw new Exception('Invalid argument given for $category_template_id: ' . $category_template_id);
        }

        $num_rows = $this->db->get_where('service_categories_template', ['id' => $category_template_id])
            ->num_rows();
        if ($num_rows == 0)
        {
            throw new Exception('Service category template record not found in database.');
        }

        $this->db->where('id', $category_template_id);
        return $this->db->delete('service_categories_template');
    }

     /**
     * Get a specific row from the service categories template table.
     *
     * @param int $category_template_id The record's id to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key has the same name as the
     * database field names.
     *
     * @throws Exception If $category_template_id argumnet is invalid.
     */
    public function get_row($category_template_id)
    {
        if ( ! is_numeric($categories_template_id))
        {
            throw new Exception('Invalid argument provided as $category_template_id : ' . $category_template_id);
        }
        return $this->db->get_where('service_categories_template', ['id' => $category_template_id])->row_array();
    }

     /**
     * Get all, or specific records from service's table.
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
        return $this->db->get('service_categories_template', $limit, $offset)->result_array();
    }
}
