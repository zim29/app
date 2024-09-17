<?php
	class CountriesController extends AppController {

		public  $uses = array(
      		'Country',
      		'Zone'
		); 

		public  $components = array('CountryTools');

		public function beforeFilter() {
	        $this->Auth->allow('ajax_get_zones');
	    }

	    public function ajax_get_zones()
	    {
	    	$country_id = array_key_exists('country_id', $this->request->data) ? $this->request->data['country_id'] : '';

	    	$country = $this->Country->find('first', array('conditions' => array('Country.id' => $country_id), array('recursive' => -1)));
	    	$country['Zone'] = $this->CountryTools->select_format_zones($country_id);

	    	/*
	    	if(empty($country['Zone']))
	    	{
	    		$country['Zone'] = array(
	    			array(
	    				'id' => 0,
	    				'name' => 'None region / state' 
	    			)
	    		);
	    	}
	    	else
	    	{
	    		foreach ($country['Zone'] as $key => $value) {
	    			$country['Zone'][$key]['name'] = utf8_encode($value['name']);
	    		}
	    	}*/

	    	echo json_encode($country); die;
	    }
	}
?>