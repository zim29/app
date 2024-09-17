<?php
	class CompetitionsController extends CompetitionsAppController 
	{
		public  $uses = array(
			'Competitions.Competition',
			'Competitions.CompetitionExtension',
			'Competitions.CompetitionSale',
			'Zone'
		); 

		/**
         * 
         * Función Index
         * 
         */
		function index()
		{
			$conditions = array(
				'num_sales_today >' => 0
			);
			//Filters
				if (!array_key_exists('search', $this->request->data))
				{
					$this->request->data['search'] = $this->Session->read('competitions_filters');
					if(empty($this->request->data['search']))
					{
						$this->request->data['search']['from'] = date('Y-m-d', strtotime("-1 week"));
						$this->request->data['search']['to'] = date('Y-m-d');
					}
					$filters = $this->Session->read('competitions_filters');
				}
				else
				{
					$this->Session->write('competitions_filters', $this->request->data['search']);
					$filters = $this->request->data['search'];
				}
				if(!empty($filters))
				{
					foreach ($filters as $key => $value) {
						if(!empty($value))
						{
							switch ($key) {
								case 'from':
									$conditions['CompetitionSale.created >='] = $value.' 00:00:00';
								break;

								case 'to':
									$conditions['CompetitionSale.created <='] = $value.' 23:59:59';
								break;
								
								default:
									die('Filter '.$key.' not processed');
								break;
							}
						}
					}
				}
			//END

			
			$fields = array(
				'(SUM(num_sales_today*current_price)*0.75)*'.Configure::read('eur_currency_value').' AS total',
				'SUM(num_sales_today) as total_sales',
				'CompetitionSale.id_competition',
				'CompetitionSale.id_extension',
				'CompetitionSale.current_price',
				'CompetitionExtension.name',
				'Competition.developer',
			);
			$group = array(
				'CompetitionSale.id_extension'
			);
			$order = array(
				'total DESC',
				'current_price DESC'
			);

			$sales = $this->CompetitionSale->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group, 'order' => $order));
		
			$this->set('sales', $sales);
		}
	}
?>