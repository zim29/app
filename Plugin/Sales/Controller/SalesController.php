<?php
	class SalesController extends SalesAppController
	{
		public  $uses = array(
			'Sales.Sale',
			'Invoices.Invoice',
            'Sales.Coupon'
		);

		public function beforeFilter() {
	        $this->Auth->allow('ajax_add_domain','ajax_renew');
	    }

		/**
         *
         * Función Index
         *
         */
		function index()
		{

			$sales = $this->Sale->find('all', array('conditions' => array('Sale.order_id LIKE' => '%ML0%')));


			foreach ($sales as $key => $sale) {
				$conditions = array(
					'Invoice.customer_email' => $sale['Sale']['buyer_email'],

					'Invoice.payed_date >=' => date('Y-m-d H:i:s', strtotime($sale['Sale']['date_added'] . ' -10 seconds')),
					'Invoice.payed_date <=' => $sale['Sale']['date_added']
					);

				$invoice = $this->Invoice->find('first', array('conditions' => $conditions));


				if(!empty($invoice)) {
					$query = "UPDATE intranet_invoices SET license_id = '".$sale['Sale']['order_id']."' WHERE id = '".$invoice['Invoice']['id']."'";
					$this->Sale->query($query);
				}
			}
			die("finished!");


		    /*$this->Sale->recursive = -1;
            $sales = $this->Sale->find('all', array('conditions' => array('Sale.domain !=' => '')));

            foreach ($sales as $key => $sale) {
                $domains = $sale['Sale']['domain'];
                $id = $sale['Sale']['order_id'];
                $domains = explode('|', $domains);

                $final_domains = array();
                foreach ($domains as $key => $domain) {
                    $final_domains[] = $this->get_domain($domain);
                }

                $final_domains = implode('|', $final_domains);
                $query = "UPDATE intranet_sales SET domain = '".$final_domains."' WHERE order_id = '".$id."'";
                $this->Sale->query($query);
            }
            die("asdfas");*/
		    //$this->Coupon->create_coupon('of-863440', 16803);

			if (empty($this->request->data))
			{
				$this->request->data = $this->Session->read('filter_sales');
			}
			else
			{
				$this->Session->write('filter_sales', $this->request->data);
			}

			$currency_eur = Configure::read('eur_currency_value');

			$years = array(
                date('Y')-2,
                date('Y')-1,
                date('Y')
            );
            $data_to_char = array();
			foreach ($years as $year) {
			    //DATA TO CHAR - SALES
                    $results = $this->Sale->get_sales_by_month('', $year);

                    $sales = array();

                    foreach ($results as $key => $data) {
                        if((int)explode("-", $data['auxiliar']['month_date'])[1] <= (int)date("m"))
                        $sales[] = array(
                            'date' => $data['auxiliar']['month_date'],
                            'num_sales' => $data[0]['num_sales'],
                            'euros' => $data[0]['euros'],
                        );
                    }
                    $sales_to_char = array();
                    foreach ($results as $key => $data) {
                        if((int)explode("-", $data['auxiliar']['month_date'])[1] <= (int)date("m"))
                        $sales_to_char[$data['auxiliar']['month_date']] = $data[0]['euros'];
                    }
                //END

                //DATA TO CHAR - Personal develops
                    /*$results = $this->Sale->query("SELECT
                        CONCAT(YEAR(sales.payed_date),'-',LPAD(MONTH(sales.payed_date), 2, '0')) as month_date,
                        count(*) as num_sales,
                        ROUND(SUM(total*currency_euro_value),2) as euros
                    FROM intranet_invoices sales
                    WHERE
                        sales.state = 'Payed'
                        AND
                        sales.type IN ('Personal develop', 'GMT Extra service')
                        AND
                        sales.deleted = 0
                        AND
                        sales.payed_date >= '".$year."-01-01'
                        AND
                        sales.payed_date <= '".$year."-12-31'
                    GROUP BY YEAR(sales.payed_date), MONTH(sales.payed_date)
                    ORDER BY YEAR(sales.payed_date) DESC, MONTH(sales.payed_date) DESC");

                    $personal_works = array();

                    foreach ($results as $key => $data) {
                        if((int)explode("-", $data[0]['month_date'])[1] <= (int)date("m"))
                        $personal_works[] = array(
                            'date' => $data[0]['month_date'],
                            'num_sales' => $data[0]['num_sales'],
                            'euros' => $data[0]['euros'],
                        );
                    }

                    $personal_works_to_char = array();
                    foreach ($results as $key => $data) {
                        if((int)explode("-", $data[0]['month_date'])[1] <= (int)date("m"))
                        $personal_works_to_char[$data[0]['month_date']] = $data[0]['euros'];
                    }*/
                //END

                //DATA TO CHAR - TOTAL
                    $total = array();

                    foreach ($sales as $key => $value) {
                        if(!array_key_exists($value['date'], $total))
                            $total[$value['date']] = 0;

                        $total[$value['date']] += $value['euros'];
                    }
                    /*foreach ($personal_works as $key => $value) {
                        if(!array_key_exists($value['date'], $total))
                            $total[$value['date']] = 0;

                        $total[$value['date']] += $value['euros'];
                    }*/
                //END

                if($year == date('Y')) {
                    $data_to_char['total'] = $total;
                    $data_to_char['sales'] = $sales_to_char;
                    //$data_to_char['personal_works'] = $personal_works_to_char;
                } else {
                    $data_to_char[$year] = $total;
                }
			}

			$this->set('current_year', date("Y"));
			$this->set('past_year', date("Y")-1);
			$this->set('repast_year', date("Y")-2);


			foreach ($data_to_char as $key => $value) {
				$data_to_char[$key] = array_reverse($value);
			}
			$this->set('data_to_char', $data_to_char);

			$conditions = array();
			$conditions = array('order_status' => 'Complete');

			//Filters
				if (is_array($this->request->data) && !array_key_exists('search', $this->request->data))
				{
					$this->request->data['search'] = $this->Session->read('sales_filters');
					$filters = $this->Session->read('sales_filters');
				}
				else
				{
					$this->Session->write('sales_filters', $this->request->data['search']);
					$filters = $this->request->data['search'];
				}
				if(!empty($filters))
				{
					foreach ($filters as $key => $value) {
					    $value = trim($value);
						if(!empty($value))
						{
							if(in_array($key, array('order_id')))
							{
							    $conditions['OR'] = array(
							        array('Sale.'.$key.' LIKE' => '%'.$value.'%'),
                                    array('Sale.domain LIKE' => '%'.$value.'%'),
                                    array('Sale.download_id LIKE' => '%'.$value.'%'),
                                    array('Sale.buyer_email LIKE' => '%'.$value.'%'),
                                    array('Sale.buyer_username LIKE' => '%'.$value.'%'),
                                    array('Sale.date_added LIKE' => '%'.$value.'%'),
                                    array('Sale.system_version LIKE' => '%'.$value.'%'),
                                );
							}
						}
					}
				}
			//END

            $user = $this->Session->read('Auth.User');
            if($user['role'] == 'employ-russia') {
                $conditions['Sale.order_id LIKE'] = 'of-%';
            }

			$this->paginate = array(
			    'recursive' => -1,
				'limit' => 10,
				'conditions' => $conditions,
				'order' => array(
					'date_added' => 'desc'
				)
			);

			$data = $this->paginate('Sale');
			$data = $this->Sale->add_additional_information($data);
			$this->set("sales", $data);

			$total = 0;

			foreach ($data_to_char['total'] as $key => $value) {
				$total += $value;
			}

			$total = round($total, 2);

			$this->set('total_sum', $total);
		}

		public function ajax_add_domain() {
		    $return = array('error' => false, 'message' => 'Domain added successful');

            $license_id = array_key_exists('license_id', $this->request->data) ? $this->request->data['license_id'] : '';
            $new_domain = array_key_exists('new_domain', $this->request->data) ? $this->request->data['new_domain'] : '';

            if(empty($license_id)) {
                $return['error'] = 1;
                $return['message'] = 'License ID not sent';
                echo json_encode($return); die;
            }

            if(empty($new_domain)) {
                $return['error'] = 1;
                $return['message'] = 'Fill new domain';
                echo json_encode($return); die;
            }

            $sale = $this->Sale->findByOrderId($license_id);

            if(empty($sale)) {
                $return['error'] = 1;
                $return['message'] = 'Sale not found';
                echo json_encode($return); die;
            }

            if($new_domain == 'clean')
                $final_domains = null;
            else {
                $new_domain = $this->get_domain($new_domain);

                if(empty($sale['Sale']['domain']))
                    $final_domains = $new_domain;
                else {
                    $split_domains = explode('|', $sale['Sale']['domain']);
                    array_push($split_domains, $new_domain);
                    $final_domains = implode('|', $split_domains);
                }
            }

            $return['message'] = $new_domain;

            $this->Sale->query('UPDATE intranet_sales SET domain ="'.$final_domains.'" WHERE order_id = "'.$license_id.'"');

			//Increase workspace number in case is GMT
			if($sale['Sale']['extension_id'] == 15609)
				$this->Sale->query('UPDATE intranet_sales SET gmt_containers_num = gmt_containers_num + 1  WHERE order_id = "'.$license_id.'"');

            echo json_encode($return); die;
        }

        public function ajax_renew() {
		    $return = array('error' => false, 'message' => 'License renewed');

            $license_id = array_key_exists('license_id', $this->request->data) ? $this->request->data['license_id'] : '';

            if(empty($license_id)) {
                $return['error'] = 1;
                $return['message'] = 'License ID not sent';
                echo json_encode($return); die;
            }

            $sale = $this->Sale->findByOrderId($license_id);


            $this->Sale->query('UPDATE intranet_sales SET date_increase ="'.date('Y-m-d H:i:s').'" WHERE order_id = "'.$license_id.'"');

            echo json_encode($return); die;
        }
	}
?>
