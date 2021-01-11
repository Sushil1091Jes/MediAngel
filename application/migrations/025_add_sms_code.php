<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Add_sms_code
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_sms_code extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
          $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '512'
            ],
            'validate_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20'
            ],
            'is_used' => [
                'type' => 'BIT',
                'null' => false
            ],
            'create_date' => [
                'type' => 'DATETIME'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('sms_code', TRUE, ['engine' => 'InnoDB']);
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
         $this->dbforge->drop_table('sms_code');
    }
}
