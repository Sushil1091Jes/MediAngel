<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Add_cancel_reasons_to_appointments
 */
class Migration_Add_cancel_reasons_to_appointments extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('cancelreasons', 'appointments'))
        {
            $fields = [
                'cancelreasons' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'after' => 'status'
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
        $this->dbforge->drop_column('appointments', 'cancelreasons');
    }
}
