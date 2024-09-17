<?php
class ExtensionSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $extensions = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'name' => array('type' => 'string', 'null' => false),
        'system' => array('type' => 'string', 'null' => false),
        'price' => array('type' => 'decimal', 'length' => '10,2', 'null' => true),
        'seo_url' => array('type' => 'string', 'null' => true),
        'features' => array('type' => 'text', 'null' => true),
        'deleted' => array('type' => 'boolean', 'default' => 0),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_name' => array('column' => 'name', 'unique' => 0),
            'idx_system' => array('column' => 'system', 'unique' => 0),
        )
    );
}
