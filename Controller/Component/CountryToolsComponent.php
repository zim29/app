<?php
	class CountryToolsComponent extends Component {

		public function initialize(Controller $controller) {
            $this->Country = ClassRegistry::init('Country');
            $this->Zone = ClassRegistry::init('Zone');
        }

        public function select_format_countries()
        {
        	$countries_array = array('' => 'Nothing selected');

        	$countries = $this->Country->find('list', array('recursive' => -1, 'conditions' => array('Country.status' => 1)));

        	foreach ($countries as $id => $name) {
        		$countries_array[$id] = $name;
        	}

			return $countries_array; 
        }

        public function select_format_zones($country_id)
        {
        	$zones = $this->Zone->find('list', array('conditions' => array('Zone.country_id' => $country_id)));

	    	if(empty($zones))
	    	{
	    		$zones = array(
	    			0 => 'None region / state' 
	    		);
	    	}
	    	else
	    	{
	    		foreach ($zones as $key => $value) {
	    			$final_zones[$key] = html_entity_decode($value);
	    		}
	    		$zones = $final_zones;
	    	}

	    	return $zones;
        }

        public function is_eu($country_id)
        {
        	$country = $this->Country->find('first', array('conditions' => array('Country.id' => $country_id), 'recursive' => -1));
        	
        	return !empty($country['Country']['is_eu']);
        }

        public function get_tax($country_id)
        {
        	$country = $this->Country->find('first', array('conditions' => array('Country.id' => $country_id), 'recursive' => -1));

        	return !empty($country['Country']['is_eu']) && !empty($country['Country']['tax']) ? ($country['Country']['tax']/100)+1 : 0;
        }

        public function validate_vat($country_id, $vat_number)
        {
        	$country = $this->Country->find('first', array('conditions' => array('Country.id' => $country_id), 'recursive' => -1));
        	
        	$countryCode = $country['Country']['iso_code_2'];

        	$client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");

            $temp_vat_number = str_replace($countryCode, '', strtoupper($vat_number));
            
            $validated = $client->checkVat(array(
              'countryCode' => $countryCode,
              'vatNumber' => $temp_vat_number
            ));

			$array = json_decode(json_encode($validated), True);

			return $array['valid'];
        }

        public function get_country_name($country_id)
        {
            $country = $this->Country->find('first', array('conditions' => array('Country.id' => $country_id), 'recursive' => -1));
            return $country['Country']['name'];
        }
        public function get_zone_name($country_id)
        {
            $zone = $this->Zone->find('first', array('conditions' => array('Zone.id' => $country_id), 'recursive' => -1));
            return array_key_exists('Zone', $zone) ? $zone['Zone']['name'] : '';
        }
    }
?>