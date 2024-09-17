<?php
/**
  * Modelo Alumnos 
  *
  * @author David Nieves
  * @fecha 22/08/2013 18:45:00
  */
    class Changelog extends AppModel 
	{
    	var $name = 'Changelog';
    	var $useTable = 'changelogs';

    	var $belongsTo = array(
	        'Extension' =>
	            array(
	                'className' => 'Extension',
	                'joinTable' => 'extensions',
	                'foreignKey' => 'id_extension'
	            ),
	    );
    	/*public $validate = array(
	        'name' => array(
	            'nonEmpty' => array(
	                'message' => 'A name is required',
	                'allowEmpty' => false,
	               	'required' => true
	            )
	        )
	    );*/
	 
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

		    foreach ($results as $key => $val) {
		        if (isset($val['changelog']['created'])) {
		            $results[$key]['changelog']['created'] = $this->dateFormatToShow($results[$key]['changelog']['date']);
		        }
		    }
		    return $results;
		}

		public function dateFormatToShow($dateString) {
		    return date('d-m-Y', strtotime($dateString));
		}
	}
?>