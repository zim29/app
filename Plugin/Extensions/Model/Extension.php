<?php
/**
  * Modelo Alumnos
  *
  * @author David Nieves
  * @fecha 22/08/2013 18:45:00
  */
    class Extension extends AppModel
	{

    	var $name = 'Extension';
    	var $useTable = 'extensions';

    	/*public $validate = array(
	        'name' => array(
	            'nonEmpty' => array(
	                'message' => 'A name is required',
	                'allowEmpty' => false,
	               	'required' => true
	            )
	        )
	    );*/

    	public $virtualFields = array(
		    'system_extension_name' => 'CONCAT(CONCAT(UCASE(MID(Extension.system,1,1)),MID(Extension.system,2)), " - ", Extension.name)'
		);

	 	public $hasMany = array(
	 		'Changelog' => array(
				'className' => 'Changelogs.Changelog',
				'conditions' => array(
					'Changelog.id_extension' => 'Extension.id',
				),
				'foreignKey' => 'id_extension',
				'order' => 'Changelog.created DESC'
			),
            'ExtensionFeature' => array(
				'className' => 'Extensions.ExtensionFeature',
				'conditions' => array(
					'ExtensionFeature.extension_id' => 'Extension.id',
				),
				'foreignKey' => 'extension_id',
				'order' => 'ExtensionFeature.sort_order ASC'
			),
	    );

		/**
         *
         * Función beforeFind, con ella añadimos siempre la condición a la consulta de que eliminado = 0
         *
         * @return array $queryData devuelve la consulta modificada
         */
		function beforeFind($queryData)
		{
			$conditions = $queryData['conditions'];
			//Si no es array lo convertimos.
			if (!is_array($conditions)) {
				if (!$conditions) {
					$conditions = array();
				} else {
					$conditions = array($conditions);
				}
			}

			if (!isset($conditions['deleted'])) {
				$conditions[$this->alias . '.deleted'] = 0;
			}

			$queryData['conditions'] = $conditions;
			return $queryData;
		}

		public function afterFind($results, $primary = false) {
			$black_friday = $this->is_black_friday();
		    foreach ($results as $key => $val) {
		        /*if (isset($val['Extension']['created'])) {
		            $results[$key]['Extension']['created'] = $this->dateFormatToShow($results[$key]['Extension']['date']);
		        }*/
		        if(array_key_exists('price', $val['Extension']) && is_numeric($val['Extension']['price']) && $val['Extension']['price'] > 0) {

					$discounts = Configure::read('discount_pack');
					$extension_price = $val['Extension']['price'];

					if(!empty($black_friday)) {
						$discount = (100-$black_friday['discount'])/100;
						$extension_price = $results[$key]['Extension']['special'] = $val['Extension']['price']*$discount;
						$results[$key]['Extension']['old_price'] = $val['Extension']['price'];
					}

                    $prices = array();
                    foreach ($discounts as $unit => $discount) {
                        $prices[$unit] = number_format($extension_price*((100-$discount)/100), 2);
                    }
                    $results[$key]['Extension']['prices'] = $prices;

                }

                if(array_key_exists('title_main', $val['Extension']))
                    $results[$key]['Extension']['name_formatted'] = $this->formatName($val['Extension']['title_main']);

		        $results[$key]['Extension']['features_formatted'] = '';
                if(array_key_exists('features', $val['Extension']) && !empty($val['Extension']['features'])) {
                    $temp_features = explode("\n", $val['Extension']['features']);
                    $final_features = '<ul><li>'.implode('</li><li>', $temp_features).'</li></ul>';
                    $results[$key]['Extension']['features_formatted'] = $final_features;
                } else
                    $results[$key]['Extension']['features_formatted'] = '';




		    }
		    return $results;
		}

		public function formatName($name) {
            return strtolower(str_replace(array(' ', '/', '-', '.'), '_', $name));
        }

		public function dateFormatToShow($dateString) {
		    return date('d-m-Y', strtotime($dateString));
		}

		public function formatExtensionToDatalayer($extension, $quantity = 0) {
			if(!is_array($extension)) {
				$this->recursive = -1;
				$extension = $this->findById($extension);
			}

			$extension_data = array(
				'id' => $extension['Extension']['id'],
				'name' => $extension['Extension']['name'],
				'system' => $extension['Extension']['system'],
				'price' => $extension['Extension']['price'],
				'image' => Router::url('/', true).'img/pages/shop/' . $extension['Extension']['system'] . '/' . $extension['Extension']['name_formatted'] . '/logo.png',
				'url' => Router::url('/', true).$extension['Extension']['seo_url'],
				'quantity' => $quantity,
			);

			return $extension_data;
		}

		public function is_black_friday() {
			$black_friday = Configure::read("black_friday");

			if(!empty($black_friday['status']) || !empty($_GET['black_demo'])) {
				$current_date = strtotime(date("Y-m-d H:i:s"));
				if(!empty($_GET['black_demo']))
					$black_friday['from'] = date("Y-m-d 00:00:00");

				$from = strtotime($black_friday['from']);
				$to = strtotime($black_friday['to']);

				if($current_date > $from && $current_date < $to) {
					$black_friday['to_formatted'] = date("F j, Y, g:i a", strtotime($black_friday['to']));
					return $black_friday;
				}
			}



			return false;
		}
	}
?>
