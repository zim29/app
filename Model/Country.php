<?php
    class Country extends AppModel 
    {
        var $name = 'Country';
        var $useTable = 'countries';

        public $hasMany = array(
            'Zone' => array(
                'className' => 'Zone',
                'conditions' => array(
                    'Zone.country_id' => 'Country.id',
                ),
                'foreignKey' => 'country_id',
                'order' => 'Zone.name ASC'
            ),
        );

    }
?>
