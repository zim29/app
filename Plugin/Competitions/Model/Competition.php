<?php
    class Competition extends AppModel 
	{
    	var $name = 'Competition';
    	var $useTable = 'competitions';

 
	 	public $hasMany = array(
	 		'CompetitionSale' => array(
				'className' => 'Competitions.CompetitionSale',
				'conditions' => array(
					'CompetitionSale.id_competition' => 'Competition.id',
				),
				'foreignKey' => 'id_competition',
				'order' => 'CompetitionSale.created DESC'
			),
			'CompetitionExtension' => array(
				'className' => 'Competitions.CompetitionExtension',
				'conditions' => array(
					'CompetitionExtension.id_competition' => 'Competition.id',
				),
				'foreignKey' => 'id_competition',
				'order' => 'CompetitionExtension.created DESC'
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
	}
?>