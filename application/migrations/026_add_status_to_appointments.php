<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Add_status_to_appointments
 */
class Migration_Add_status_to_appointments extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('status', 'appointments'))
        {
            $fields = [
                'status' => [
                    'type' => 'int',
                    'constraint' => '1',
                    'default' => '1',
                    'Comment'=>'Confirmed=1,Canceled=-1'
                ]
            ];

            $this->dbforge->add_column('appointments', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->dbforge->drop_column('appointments', 'status');
    }
}
