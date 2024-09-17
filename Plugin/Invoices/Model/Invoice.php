<?php
    class Invoice extends AppModel
	{
    	var $name = 'Invoice';
    	var $useTable = 'invoices';

		var $belongsTo = array(
			'Sale' => array(
				'className' => 'Sales.Sale',
				'foreignKey' => false,
				'conditions' => array('Sale.order_id = Invoice.license_id'),
			)
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
