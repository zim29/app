<?php
	class PaypalController extends InvoicesAppController 
	{
		public  $uses = array(
			'Invoices.Invoice'
		); 

		public $components = array('Invoices.InvoicesOpencart', 'OpencartExtension', 'Email');

		public function beforeFilter() {
	        $this->Auth->allow('callback');
	        $this->sandbox_mode =  false;

	        if($this->sandbox_mode)
	        	$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	        else
	        	$this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	    }

		function callback()
		{
			$invoice_id = isset($this->request->data['custom']) ? $this->request->data['custom'] : '';

			if(empty($invoice_id))
			{
				$this->send_email_error('License ID not found in param custom from paypal callback');
				die();
			}

			$invoice = $this->Invoice->findById($invoice_id);

			if(!empty($invoice))
			{
				$request = 'cmd=_notify-validate';

				foreach ($this->request->data as $key => $value) {
					$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
				}

				$curl = curl_init($this->paypal_url);

				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

				$response = curl_exec($curl);

				if (!$response)
				{
					$this->send_email_error('Fail in response', array('Datas json' => json_encode($this->request->data)));
					die();
				}

				if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($this->request->data['payment_status'])) {
					if(!in_array($this->request->data['payment_status'], array('Completed')))
					{
						$this->send_email_error('Order not complete', array('Payment status' => $this->request->data['payment_status'], 'Datas json' => json_encode($this->request->data)));
						die();
					}
					else
					{
					    $array_return = array(
                            'error' => false,
                            'message' => '',
                            'redirect' => Router::url("/", true).'invoices/invoices/pay_success'
                        );

						$invoice['Invoice']['paypal_id_transaction'] = $this->request->data['txn_id'];

						try {
                            $ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
                            $ds->begin($this);
                            $this->InvoicesOpencart->process_invoice($invoice);
                            $ds->commit();
                        } catch (Exception $e) {
                            $this->send_email_error('Error during update invoice', array('Error message' => $e->getMessage()));
                            $array_return['error'] = true;
                            $array_return['message'] = __('Error during update invoice.');
                            $ds->rollback();
                        }
                        echo json_encode($array_return); die;
					}
				}
			}
			else
			{
				$this->send_email_error('Invoice not found: '.$invoice_id);
				die();
			}

			die("finish");
		}

		public function error_in_payment()
		{
			$invoice_id = isset($this->request->data['custom']) ? $this->request->data['custom'] : '';
			$this->send_email_error('Payment error from paypal', array('Datas returned' => json_encode($this->request->data)));

			$message = 'Error during payment. Devman was alert, don\'t worry! =)';
			$this->Session->setFlash($message, 'default', array('class' => 'error'));
			$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));

			die("finish");
		}

		public function send_email_error($subject, $extra_variables = array())
		{
			$subject = '[PaypalController][ERROR] - '.$subject;

			$message = '';
			if(!empty($extra_variables))
			{
				foreach ($extra_variables as $label => $value) {
					$message .= '<b>'.$label.'</b>: '.$value.'<br>';
				}
			}

			$this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
		}
	}
?>