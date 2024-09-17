<?php
class TrialLicenseSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $trial_licenses = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'license_id' => array('type' => 'string', 'null' => false),
        'extension_id' => array('type' => 'integer', 'null' => false),
        'extension_name' => array('type' => 'string', 'null' => false),
        'domain' => array('type' => 'string', 'null' => false),
        'customer_name' => array('type' => 'string', 'null' => false),
        'customer_email' => array('type' => 'string', 'null' => false),
        'activated' => array('type' => 'boolean', 'default' => 0),
        'form_recovered' => array('type' => 'integer', 'default' => 0),
        'oc_version' => array('type' => 'string', 'null' => true),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_license_id' => array('column' => 'license_id', 'unique' => 0),
            'idx_extension_id' => array('column' => 'extension_id', 'unique' => 0),
            'idx_domain' => array('column' => 'domain', 'unique' => 0)
        )
    );
}
