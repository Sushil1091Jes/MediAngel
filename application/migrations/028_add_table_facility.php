<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * MediAngel - 
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Add_table_facility
 */
class Migration_Add_table_facility extends CI_Migration {
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up()
    {
        $query="
            CREATE TABLE facility (
            `id` int(11) NOT NULL auto_increment,
            `uuid` binary(16) DEFAULT NULL,
            `name` varchar(255) default NULL,
            `phone` varchar(30) default NULL,
            `fax` varchar(30) default NULL,
            `street` varchar(255) default NULL,
            `city` varchar(255) default NULL,
            `state` varchar(50) default NULL,
            `postal_code` varchar(11) default NULL,
            `country_code` varchar(30) NOT NULL default '',
            `federal_ein` varchar(15) default NULL,
            `website` varchar(255) default NULL,
            `email` varchar(255) default NULL,
            `service_location` tinyint(1) NOT NULL default '1',
            `billing_location` tinyint(1) NOT NULL default '1',
            `accepts_assignment` tinyint(1) NOT NULL default '1',
            `pos_code` tinyint(4) default NULL,
            `x12_sender_id` varchar(25) default NULL,
            `attn` varchar(65) default NULL,
            `domain_identifier` varchar(60) default NULL,
            `facility_npi` varchar(15) default NULL,
            `facility_taxonomy` varchar(15) default NULL,
            `tax_id_type` VARCHAR(31) NOT NULL DEFAULT '',
            `color` VARCHAR(7) NOT NULL DEFAULT '',
            `primary_business_entity` INT(10) NOT NULL DEFAULT '1' COMMENT '0-Not Set as business entity 1-Set as business entity',
            `facility_code` VARCHAR(31) default NULL,
            `extra_validation` tinyint(1) NOT NULL DEFAULT '1',
            `mail_street` varchar(30) default NULL,
            `mail_street2` varchar(30) default NULL,
            `mail_city` varchar(50) default NULL,
            `mail_state` varchar(3) default NULL,
            `mail_zip` varchar(10) default NULL,
            `oid` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'HIEs CCDA and FHIR an OID is required/wanted',
            `iban` varchar(50) default NULL,
            `info` TEXT,
            UNIQUE KEY `uuid` (`uuid`),
            PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4;
        ";

        $this->db->query($query);
    }
	
    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down()
    {
        $this->db->query('
            DROP TABLE IF EXISTS `facility`;
        ');
    }
}
