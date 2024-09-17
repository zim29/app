<?php
    class Testimonial extends AppModel 
    {
        var $name = 'Testimonial';
        var $useTable = 'testimonials';

        var $belongsTo = array(
			'Country' => array(
				'className' => 'Country',
				'foreignKey' => false,
				'conditions' => array('Country.id = Testimonial.country_id'),
			),
            'Extension' => array(
				'className' => 'Extensions.Extension',
				'foreignKey' => false,
				'conditions' => array('Extension.id = Testimonial.extension_id'),
			)
		);
    }
?>
