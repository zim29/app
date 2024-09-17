<?php
	class SalesWaitingController extends SalesAppController 
	{
		public  $uses = array(
			'Sales.Sale'
		); 

		public $components = array('OpencartExtension');

		public function beforeFilter() {
	        $this->Auth->allow('autovalidate');
	    }

		function index()
		{
			$conditions = array();
			$conditions = array('order_status' => array('Waiting for Proof of ID', 'pending_validate'));

			$this->paginate = array(
				'limit' => 20,
				'conditions' => $conditions,
				'order' => array(
					'date_added' => 'desc'
				)
			);

			$data = $this->paginate('Sale');

			$this->set("sales", $data);
		}

		public function complete()
		{
			$array_return = array('error' => false, 'message' => 'Order completed');
			$license_id = $this->request->data['id'];

			if(array_key_exists('sub_total', $this->request->data)) {
                $sub_total = array_key_exists('sub_total', $this->request->data) ? $this->request->data['sub_total'] : '';
                if(!is_numeric($sub_total)) {
                    $array_return['error'] = true;
                    $array_return['message'] = 'Insert valid sub total';
                    echo json_encode($array_return); die;
                }

                $commission = array_key_exists('commission', $this->request->data) ? $this->request->data['commission'] : '';
                if(!is_numeric($commission)) {
                    $array_return['error'] = true;
                    $array_return['message'] = 'Insert valid fee';
                    echo json_encode($array_return); die;
                }

                $total = array_key_exists('total', $this->request->data) ? $this->request->data['total'] : '';
                if(!is_numeric($total)) {
                    $array_return['error'] = true;
                    $array_return['message'] = 'Insert valid total';
                    echo json_encode($array_return); die;
                }

                $this->Sale->query('UPDATE intranet_sales SET total ="'.$total.'", sub_total ="'.$sub_total.'", commission ="'.$commission.'"  WHERE order_id = "'.$license_id.'"');
            }

			try {
				$this->OpencartExtension->complete_order($license_id);
				$array_return['redirect'] = 'https://devmanextensions.com/sales';
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			echo json_encode($array_return); die;
		}

		public function autovalidate($license_id) {
		    if(empty($license_id))
		        die("Parámetro no encontrado");
		    
		    $sale = $this->Sale->findByOrderId($license_id);

		    if(empty($sale))
		        die(sprintf("No se encuentra la licencia con ID: <b>%s</b>", $license_id));
		    if($sale['Sale']['order_status'] != 'pending_validate')
		        die(sprintf("El estado actual de esta licencia es: <b>%s</b>", $sale['Sale']['order_status']));

		    $this->request->data['id'] = $license_id;
		    $this->request->data['sub_total'] = 0;
		    $this->request->data['commission'] = 0;
		    $this->request->data['total'] = 0;
		    $this->complete();
        }
	}
?>