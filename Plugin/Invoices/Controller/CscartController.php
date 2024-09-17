<?php
	class CscartController extends InvoicesAppController {

		public  $uses = array(
      		'Sales.Sale',
      		'Invoices.Invoice'
		);

		public $components = array(
		    'ApiLicenses',
		    'OpencartExtension',
		    'Email',
		    'CountryTools',
		    'Invoices.Invoices',
		    'Cart',
		    'Session'
		);

		public function beforeFilter() {
	        $this->Auth->allow('new_invoice', 'validate_invoice');
	        $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
	    }

	    public function new_invoice()
		{
		    $this->layout = 'frontend';
		    if(!empty($this->request->data['Invoice'])) {
		           $system = $this->request->data['Invoice']['system'];
            } else {
                $support_id = !empty($this->request->data['support_id']) ? $this->request->data['support_id'] : $_GET['support_id'];
                $license_info = $this->ApiLicenses->get_license_info($support_id);
                $system = $license_info['platform'];
            }

		    $params = array(
		        'system' => $system
            );
		    $this->Invoices->new_invoice($params, $this->request);

		    $this->render('Opencart/new_invoice');
		}

		public function validate_invoice($id_invoice = null)
		{
		    $this->layout = 'frontend';
		    $this->Invoices->validate_invoice($id_invoice, $this->request);
		    
		    $this->render('Opencart/validate_invoice');
		}
	}
?>