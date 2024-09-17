<?php
	//require_once VENDORS . 'stripe/autoload.php';
    require_once VENDORS . 'stripe-php-6.38.0/init.php';
	class StripeController extends InvoicesAppController 
	{
		public  $uses = array(
			'Invoices.Invoice'
		); 

		public $components = array('Invoices.InvoicesOpencart', 'OpencartExtension', 'Email');

		public function beforeFilter() {
	        $this->Auth->allow('callback','error');
	    }

		function callback()
		{
		    $array_return = array(
		        'error' => false,
                'message' => '',
                'redirect' => Router::url("/", true).'invoices/invoices/pay_success'
            );
            $payment_info = array_key_exists('paymentIntent', $this->request->data) ? (array)$this->request->data['paymentIntent'] : null;

            if(!$payment_info) {
                $array_return['error'] = true;
                $array_return['message'] = __('Payment information not found');
                echo json_encode($payment_info); die;
            }

            if($payment_info['status'] != 'succeeded') {
                $array_return['error'] = true;
                $array_return['message'] = __('Error processing payment. Payment status: '.$payment_info['status'].'Cacellation reason: '.$payment_info['cancellation_reason']);
                echo json_encode($payment_info); die;
            }

            $stripe_api_private = Configure::read('stripe_api_private');
            \Stripe\Stripe::setApiKey($stripe_api_private);
            $intent = \Stripe\PaymentIntent::retrieve($payment_info['id']);
            
            $invoice_id = $intent['metadata']['order_id'];
                
            $invoice = $this->Invoice->findById($invoice_id);

            try {
                $ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
                $ds->begin($this);
                $invoice['Invoice']['stripe_id_transaction'] = $intent['charges']['data'][0]['id'];
                $this->InvoicesOpencart->process_invoice($invoice);
                $ds->commit();
            } catch (Exception $e) {
                $this->send_email_error('Error during update invoice', array('Error message' => $e->getMessage()));
                $array_return['error'] = true;
                $array_return['message'] = __('Error during update invoice.');
                $ds->rollback();
            }
            echo json_encode($array_return); die;

		    /* \Stripe\Stripe::setApiKey($stripe_api_private);
            $session_id = $this->Session->read('session_id_stripe');
		    $session = \Stripe\Checkout\Session::($session_id);
            echo '<pre>'; print_r($session);  echo '</pre>'; die;
			\Stripe\Stripe::setApiKey(Configure::read('stripe_api_private'));


			$token  = array_key_exists('stripeToken', $this->request->data) ? $this->request->data['stripeToken'] : '';
			$email  = array_key_exists('stripeEmail', $this->request->data) ? $this->request->data['stripeEmail'] : '';
			$invoice_id = array_key_exists('invoice_id', $this->request->data) ? $this->request->data['invoice_id'] : '';
			$invoice_description = array_key_exists('invoice_description', $this->request->data) ? $this->request->data['invoice_description'] : '';
			if(empty($token) || empty($email) || empty($invoice_id))
			{
				$message = __('Empty token or email');
				$this->Session->setFlash($message, 'default', array('class' => 'error'));
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
			}

			$invoice = $this->Invoice->findById($invoice_id);

			$customer = \Stripe\Customer::create(array(
				'email' => $email,
				'source'  => $token
			));

			try {
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $invoice['Invoice']['total']*100,
					'description' => strip_tags($invoice_description),
					'currency' => 'usd'
				));
			} catch (Exception $e) {
				$message = $e->getMessage();
				$this->Session->setFlash($message, 'default', array('class' => 'error'));
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
			}
			
			if(!empty($charge->id))
			{
				try {
					$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
					$ds->begin($this);
					$invoice['Invoice']['stripe_id_transaction'] = $charge->id;
			    	$this->InvoicesOpencart->process_invoice($invoice);
			    	$ds->commit();
				} catch (Exception $e) {
					$this->send_email_error('Error during update invoice', array('Error message' => $e->getMessage()));
					die();
					$ds->rollback();
				}

				$this->redirect(array('plugin' => 'invoices', 'controller' => 'invoices', 'action' => 'pay_success'));
			}*/
			
			die("finish");
		}

		public function send_email_error($subject, $extra_variables = array())
		{
			$subject = '[StripeController][ERROR] - '.$subject;

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