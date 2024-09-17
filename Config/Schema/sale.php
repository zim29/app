<?php
class SaleSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $sales = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'extension_id' => array('type' => 'integer', 'null' => false),
        'order_id' => array('type' => 'string', 'null' => false),
        'total' => array('type' => 'decimal', 'length' => '10,2', 'null' => false),
        'date_added' => array('type' => 'datetime', 'null' => false),
        'date_increase' => array('type' => 'datetime', 'null' => true),
        'buyer_email' => array('type' => 'string', 'null' => true),
        'buyer_username' => array('type' => 'string', 'null' => true),
        'system_version' => array('type' => 'string', 'null' => true),
        'marketplace' => array('type' => 'string', 'null' => true),
        'order_status' => array('type' => 'string', 'null' => true),
        'deleted' => array('type' => 'boolean', 'default' => 0),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_extension_id' => array('column' => 'extension_id', 'unique' => 0),
            'idx_order_id' => array('column' => 'order_id', 'unique' => 0)
        )
    );
}
