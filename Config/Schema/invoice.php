<?php
class InvoiceSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $invoices = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'license_id' => array('type' => 'string', 'null' => false),
        'total' => array('type' => 'decimal', 'length' => '10,2', 'null' => false),
        'payed_date' => array('type' => 'datetime', 'null' => true),
        'state' => array('type' => 'string', 'null' => true, 'default' => 'Pending'),
        'type' => array('type' => 'string', 'null' => true),
        'deleted' => array('type' => 'boolean', 'default' => 0),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_license_id' => array('column' => 'license_id', 'unique' => 0)
        )
    );
}
