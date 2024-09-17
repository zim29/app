<?php
/**
  * Modelo Alumnos 
  *
  * @author David Nieves
  * @fecha 22/08/2013 18:45:00
  */
    class Ticket extends AppModel 
	{
    	var $name = 'Ticket';
    	var $useTable = 'tickets';

      public $virtualFields = array(
        'select_name' => 'CONCAT(Ticket.id, " - ", Ticket.subject)'
      );

	    var $belongsTo = array(
          	'Sale' => array(
          		'className' => 'Sales.Sale',
	            'foreignKey' => false,
	            'conditions' => array('Sale.order_id = Ticket.id_license'),
	        ),
	        'Extension' => array(
          		'className' => 'Extensions.Extension',
	            'foreignKey' => false,
	            'conditions' => array('Extension.id = Ticket.extension_id'),
	        ),
            'User' => array(
          		'className' => 'User',
	            'foreignKey' => false,
	            'conditions' => array('User.id = Ticket.answered_user_id'),
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
		    foreach ($results as $key => $val) {
		        if (isset($val['Ticket']['date'])) {
		            $results[$key]['Ticket']['date'] = $this->dateFormatToShow($results[$key]['Ticket']['date']);
		        }

		        $responded_in = '';

		        if(array_key_exists('Ticket', $results[$key]) && $results[$key]['Ticket']['answered_date'] != '0000-00-00 00:00:00') {
                    $start_date = new DateTime($results[$key]['Ticket']['created']);
                    $since_start = $start_date->diff(new DateTime($results[$key]['Ticket']['answered_date']));

                    if ($since_start->days)
                        $responded_in .= $since_start->days . 'd';
                    if ($since_start->h)
                        $responded_in .= (!empty($responded_in) ? ' ' : '') . ($since_start->h . 'h');
                    if ($since_start->i)
                        $responded_in .= (!empty($responded_in) ? ' ' : '') . ($since_start->i . 'm');

                    if (empty($responded_in))
                        $responded_in = '1m';


                }
                if(array_key_exists('Ticket', $val)) {
                    $prev_purchase = $this->query('SELECT count(*) as count FROM intranet_tickets WHERE id_license != "" AND id_license = "' . $val['Ticket']['id_license'] . '"');

                    $results[$key]['Ticket']['number_of_tickets'] = $prev_purchase[0][0]['count'] ? $prev_purchase[0][0]['count'] : '-';
                }
                 else
                     $results[$key]['Ticket']['number_of_tickets'] = '-';

		        if(!empty($val['Ticket']['extension_id']) && !array_key_exists('Extension', $val)) {
                    $extension = $this->query('SELECT name FROM intranet_extensions WHERE id = "' . $val['Ticket']['extension_id'] . '"');
                    $val['Extension']['name'] = $extension[0]['intranet_extensions']['name'];
                }

                if(!empty($val['Ticket']['answered_user_id'])) {
                    $user = $this->query('SELECT username FROM intranet_users WHERE id = "' . $val['Ticket']['answered_user_id'] . '"');
                    $results[$key]['User']['username'] = $user[0]['intranet_users']['username'];
                }



		        
                if (array_key_exists('Extension', $val) && !empty($val['Extension'])) {
                    $words = explode(" ", $val['Extension']['name']);
                    $acronym = "";

                    foreach ($words as $w) {
                        $acronym .= !empty($w[0]) ? $w[0] : '';
                    }
                    $results[$key]['Extension']['name_initials'] = $acronym;
                }

                $results[$key]['Ticket']['responded_in'] = $responded_in;
		    }

		    return $results;
		}

		/**
         * 
         * Función beforeFind, con ella añadimos siempre la condición a la consulta de que eliminado = 0
         * 
         * @return array $queryData devuelve la consulta modificada
         */
		public function beforeSave($options = array())
		{
			if (isset($this->data['ticket']['date']))
				$this->data['ticket']['date'] = dateFormatToSave($this->data['ticket']['date']);
		} 

		public function dateFormatToShow($dateString) {
		    return date('d-m-Y', strtotime($dateString));
		}
		public function dateFormatToSave($dateString) {
		    return date('Y-m-d', strtotime($dateString));
		}
	}
?>