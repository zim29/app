<?php
class ChangelogSchema extends CakeSchema {

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {
    }

    public $changelogs = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'extra' => 'auto_increment'),
        'id_extension' => array('type' => 'integer', 'null' => false),
        'description' => array('type' => 'text', 'null' => false),
        'deleted' => array('type' => 'boolean', 'default' => 0),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'idx_id_extension' => array('column' => 'id_extension', 'unique' => 0)
        )
    );
}
