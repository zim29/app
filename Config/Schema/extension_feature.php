<?php
class ExtensionFeatureSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $extensions_features = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'extension_id' => array('type' => 'integer', 'null' => false),
        'feature_name' => array('type' => 'string', 'null' => false),
        'feature_value' => array('type' => 'string', 'null' => true),
        'sort_order' => array('type' => 'integer', 'null' => true, 'default' => 0),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_extension_id' => array('column' => 'extension_id', 'unique' => 0)
        )
    );
}
