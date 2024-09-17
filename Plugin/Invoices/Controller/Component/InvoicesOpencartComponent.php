<?php
	class InvoicesOpencartComponent extends Component {

		public function initialize(Controller $controller) {
            $this->Invoice = ClassRegistry::init('Invoice');
            $this->controllerTemp = $controller;
        }

        public $components = array('Invoices.Invoices', 'CountryTools', 'OpencartExtension', 'Email', 'Client', 'ExtensionTool', 'Cart', 'Session', 'Mpdf', 'ApiLicenses');

        public function generate_invoice($invoice)
        {
            /*if(!$invoice['Invoice']['total'] || in_array($invoice['Invoice']['customer_email'], array('menstroop@gmail.com', 'rohitrock7898@gmail.com', 'hatkepriceofficial@gmail.com', 'rohitkunduoff@gmail.com'))) {
                die("Fuck you....");
            }*/

            $invoice['Invoice']['total'] = $this->Invoices->get_invoice_total($invoice);

            //Devman Extensions - info@devmanextensions.com - 2017-09-12 16:28:30 - Check if have a pending invoice
        		if(!empty($invoice['Invoice']['license_id']))
        		{
        			$conditions = array(
        				'Invoice.state' => 'Pending',
        				'Invoice.license_id' => $invoice['Invoice']['license_id']
        			);
        			$invoices = $this->Invoice->find('first', array('conditions' => $conditions));

        			if(!empty($invoices)) {
                        $this->Invoice->query('DELETE FROM intranet_invoices WHERE id = "' . $invoices['Invoice']['id'] . '"');
                        //throw new Exception ('You have a pending invoice, wait to our team approve it.');
                    }
        		}
        	//END

			$is_eu = $this->CountryTools->is_eu($invoice['Invoice']['customer_country_id']);
			$is_spain = $invoice['Invoice']['customer_country_id'] == 195;

			if($is_eu)
				$invoice['Invoice']['currency'] = 'EUR';

			if($is_eu && !$is_spain && !empty($invoice['Invoice']['customer_vat']))
			    $invoice['Invoice']['tax'] = 0;

			$invoice['Invoice']['customer_country'] = $this->CountryTools->get_country_name($invoice['Invoice']['customer_country_id']);
			$invoice['Invoice']['customer_zone'] = $this->CountryTools->get_zone_name($invoice['Invoice']['customer_zone_id']);
			$invoice['Invoice']['system'] = !empty($invoice['Invoice']['system']) ? $invoice['Invoice']['system']: 'Opencart';

			$invoice['Invoice']['total'] = round($invoice['Invoice']['total'], 2);

			//2018-12-29 Control zone_id is numeric
            if(!is_numeric($invoice['Invoice']['customer_zone_id']))
                throw new Exception ('Select "<b>Region / State</b>"');

			if($invoice['Invoice']['type'] == 'Add domain')
				$invoice['Invoice']['new_domain'] = $this->Invoice->get_domain($invoice['Invoice']['new_domain']);
			
			if(!$this->Invoice->save($invoice['Invoice']))
        		throw new Exception ('Error generating Invoice');

        	$invoice['Invoice']['id'] = $this->Invoice->getLastInsertId();

        	switch ($invoice['Invoice']['type']) {
        		case 'Renew':
        			$this->send_emails_renew($invoice);
        		break;

        		case 'Add domain':
        			$this->send_emails_add_domain($invoice);
        		break;

        		case 'New GMT Container':
        			$this->send_emails_add_new_gmt_container($invoice);
        		break;

        		case 'License':
        			$this->send_emails_license($invoice);
        		break;

        		default:
        		break;
        	}

        	return $invoice['Invoice']['id'];
        }

        public function validate_invoice($invoice)
        {
			$is_eu = $this->CountryTools->is_eu($invoice['Invoice']['customer_country_id']);
			$is_spain = $invoice['Invoice']['customer_country_id'] == 195;

			$temp_total_session = $this->Session->read('total_invoice');

			if($is_spain || ($is_eu && empty($invoice['Invoice']['customer_vat'])))
			{
				$temp_total = $invoice['Invoice']['price']*$invoice['Invoice']['quantity'];;
				$invoice['Invoice']['total'] = $temp_total * (($invoice['Invoice']['tax']/100) + 1);

				$temp_total_session = $temp_total_session * (($invoice['Invoice']['tax']/100) + 1);
			}
			else
			{
				$invoice['Invoice']['total'] = $invoice['Invoice']['price']*$invoice['Invoice']['quantity'];
				$invoice['Invoice']['tax'] = 0;
			}

			if($is_eu)
				$invoice['Invoice']['currency'] = 'EUR';

			if($invoice['Invoice']['payment_method'] == 'Paypal') {
				$invoice['Invoice']['total'] *= Configure::read('paypal_fee');
				$temp_total_session *= Configure::read('paypal_fee');
			}
			if($invoice['Invoice']['payment_method'] == 'Stripe') {
				$invoice['Invoice']['total'] *= Configure::read('stripe_fee');
				$temp_total_session *= Configure::read('stripe_fee');
			}

			$invoice['Invoice']['customer_country'] = $this->CountryTools->get_country_name($invoice['Invoice']['customer_country_id']);
			$invoice['Invoice']['customer_zone'] = $this->CountryTools->get_zone_name($invoice['Invoice']['customer_zone_id']);
			$invoice['Invoice']['system'] = !empty($invoice['Invoice']['system']) ? $invoice['Invoice']['system'] : 'Opencart';

			//Devman Extensions - info@devmanextensions.com - 2017-09-15 15:44:13 - Discount
				$discount = !empty($invoice['Invoice']['discount']) ? $invoice['Invoice']['discount'] : 0;
				if($discount > 0)
				{
					$real_discount = (100-$discount) / 100;
					$invoice['Invoice']['total'] = $invoice['Invoice']['total'] * $real_discount;

					$temp_total_session = $temp_total_session * $real_discount;
				}
			//END
			$invoice['Invoice']['total'] = round($invoice['Invoice']['total'], 2);
			$temp_total_session = round($temp_total_session, 2);

			if($invoice['Invoice']['total'] != $temp_total_session)
				throw new Exception ('Invoice total is not correct.');

        	if(!$this->Invoice->saveAll($invoice))
        		throw new Exception ('Error generating Invoice');

        	switch ($invoice['Invoice']['type']) {
        		case 'Personal develop':
        			$this->send_emails_personal_develop($invoice);
        		break;

        		case 'Renew':
        			$this->send_emails_renew($invoice);
        		break;

        		case 'Add domain':
        			$this->send_emails_add_domain($invoice);
        		break;

        		case 'New GMT Container':
        			$this->send_emails_add_new_gmt_container($invoice);
        		break;

        		case 'License':
        			$this->send_emails_license($invoice);
        		break;

        		default:
        		break;
        	}
        }

        public function send_emails_renew($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];

				$license_info = $this->OpencartExtension->license_get_license($license_id);
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][Renew]['.$license_id.']['.$license_info['Extension']['name'].'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price renew:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $license_info['Sale']['domain']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
				//$pay_url = 'https://www.paypal.me/devmanextensions/'.$invoice['Invoice']['total'];
				$pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$invoice['Invoice']['id'];

				$subject = '[DevmanExtensions] Renew - license #'.$license_id;

				$message = '';
					$message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

					$message .= 'Your operation details:';

					$message .= '<ul>';
						$message .= '<li>'.sprintf('<b>License ID:</b> %s<br>', $license_id).'</li>';
						$message .= '<li>'.sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']).'</li>';
						$message .= '<li>'.sprintf('<b>Price renew:</b> $%s<br>', $invoice['Invoice']['total']).'</li>';
						$message .= '<li>'.sprintf('<b>Link to pay:</b> <a href="%s">Link to pay</a>', $pay_url).'</li>';
					$message .= '</ul>';

					$message .= sprintf('* If you payment was successful, you will received an automatic email to <b>%s</b> confirming your purchase.', $invoice['Invoice']['customer_email']);
					$message .= '<br>';
					$message .= sprintf('* <b style="color:#f00;">IMPORTANT NOTE</b>: Our TPV is refusing some types of credit cards, if you get an error paying with your credit cart use another payment method like "<b>Stripe</b>" or "<b>Paypal</b>" in your invoice link -> <a href="https://devmanextensions.com/invoices/opencart/validate_invoice/%s">invoice link</a> and press button "Validate invoice & get payment instructions" again.<br>', $invoice['Invoice']['id']);

				try {
				    //$this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'License Renewal System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_gmt_budget($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][GMT Extra service] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= nl2br($invoice['Invoice']['description_avanced']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				    $this->Email->send_email('marketing@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_license($invoice)
        {
			$cart = $this->Cart->get_products();
        	$licenses_text = '';
			foreach ($cart as $key => $ext) {
				$licenses_text .= $ext['quantity'].' x '.$ext['name'].' - $'.$ext['special'].'<br>';
			}

			$tax_text = '';

			if(!empty($invoice['Invoice']['tax']) && $invoice['Invoice']['tax'] > 0)
				$tax_text .= 'Tax '.$invoice['Invoice']['tax'].'% - $'.(number_format($invoice['Invoice']['total']-$invoice['Invoice']['price'], 2));

        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][License] $'.$invoice['Invoice']['total'];


				$message = '<b>'.__('Products').':</b><br>';
				$message .= $licenses_text;
				$message .= $tax_text.'<br>';

				$message .= sprintf('<b>Total:</b> $%s<br>', $invoice['Invoice']['total']);
				$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
				//$pay_url = 'https://www.paypal.me/devmanextensions/'.$invoice['Invoice']['total'];
				$pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$invoice['Invoice']['id'];

				$subject = '[DevmanExtensions] Finish purchase';

				$message = '';
					$message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

					$message .= 'Your operation details:';

					$message .= '<ul>';
						$message .= '<li>'.sprintf('<b>Products:</b><br> %s %s<br>', $licenses_text, $tax_text).'</li>';
						$message .= '<li>'.sprintf('<b>Total:</b> $%s<br>', $invoice['Invoice']['total']).'</li>';
						$message .= '<li>'.sprintf('<b>Link to pay:</b> <a href="%s">Link to pay</a>', $pay_url).'</li>';
					$message .= '</ul>';

					$message .= sprintf('* If you payment was successful, you will received an automatic email to <b>%s</b> confirming your purchase.', $invoice['Invoice']['customer_email']);
					$message .= '<br>';
					$message .= sprintf('* <b style="color:#f00;">IMPORTANT NOTE</b>: Our TPV is refusing some types of credit cards, if you get an error paying with your credit cart use another payment method like "<b>Stripe</b>" or "<b>Paypal</b>" in your invoice link -> <a href="https://devmanextensions.com/invoices/opencart/validate_invoice/%s">invoice link</a> and press button "Validate invoice & get payment instructions" again.<br>', $invoice['Invoice']['id']);

				try {
				    //$this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'License Renewal System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_add_domain($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$license_info = $this->OpencartExtension->license_get_license($license_id);
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][Add domain]['.$license_id.']['.$license_info['Extension']['name'].'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price add domain:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $license_info['Sale']['domain']);
					$message .= sprintf('<b>New domain:</b> %s<br>', $invoice['Invoice']['new_domain']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
				$pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$invoice['Invoice']['id'];

				$subject = '[DevmanExtensions] Add domain - license #'.$license_id;

				$message = '';
					$message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

					$message .= 'Your operation details:';

					$message .= '<ul>';
						$message .= '<li>'.sprintf('<b>License ID:</b> %s<br>', $license_id).'</li>';
						$message .= '<li>'.sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']).'</li>';
						$message .= '<li>'.sprintf('<b>New domain:</b> %s<br>', $invoice['Invoice']['new_domain']).'</li>';
						$message .= '<li>'.sprintf('<b>Price add domain:</b> $%s<br>', $invoice['Invoice']['total']).'</li>';
						$message .= '<li>'.sprintf('<b>Link to pay:</b> <a href="%s">Link to pay</a>', $pay_url).'</li>';
					$message .= '</ul>';

					$message .= sprintf('* If you payment was successful, you will received an automatic email to <b>%s</b> confirming your purchase.', $invoice['Invoice']['customer_email']);
					$message .= '<br>';
					$message .= sprintf('* <b style="color:#f00;">IMPORTANT NOTE</b>: Our TPV is refusing some types of credit cards, if you get an error paying with your credit cart use another payment method like "<b>Stripe</b>" or "<b>Paypal</b>" in your invoice link -> <a href="https://devmanextensions.com/invoices/opencart/validate_invoice/%s">invoice link</a> and press button "Validate invoice & get payment instructions" again.<br>', $invoice['Invoice']['id']);

				try {
				    //$this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'License Renewal System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_add_new_gmt_container($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$license_info = $this->OpencartExtension->license_get_license($license_id);
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][GMT New container]['.$license_id.']['.$license_info['Extension']['name'].'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price new container:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $license_info['Sale']['domain']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
				$pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$invoice['Invoice']['id'];

				$subject = '[DevmanExtensions] GMT New container - license #'.$license_id;

				$message = '';
					$message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

					$message .= 'Your operation details:';

					$message .= '<ul>';
						$message .= '<li>'.sprintf('<b>License ID:</b> %s<br>', $license_id).'</li>';
						$message .= '<li>'.sprintf('<b>Extension:</b> %s<br>', $license_info['Sale']['extension_name']).'</li>';
						$message .= '<li>'.sprintf('<b>Price increase containers number:</b> $%s<br>', $invoice['Invoice']['total']).'</li>';
						$message .= '<li>'.sprintf('<b>Link to pay:</b> <a href="%s">Link to pay</a>', $pay_url).'</li>';
					$message .= '</ul>';

					$message .= sprintf('* If you payment was successful, you will received an automatic email to <b>%s</b> confirming your purchase.', $invoice['Invoice']['customer_email']);
					$message .= '<br>';
					$message .= sprintf('* <b style="color:#f00;">IMPORTANT NOTE</b>: Our TPV is refusing some types of credit cards, if you get an error paying with your credit cart use another payment method like "<b>Stripe</b>" or "<b>Paypal</b>" in your invoice link -> <a href="https://devmanextensions.com/invoices/opencart/validate_invoice/%s">invoice link</a> and press button "Validate invoice & get payment instructions" again.<br>', $invoice['Invoice']['id']);
				try {
				    //$this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'License Renewal System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_personal_develop($invoice)
        {
            $temp_invoice = $this->Invoice->findById($invoice['Invoice']['id']);
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][Personal develop] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price personal develop:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Payment method:</b> %s<br>', $invoice['Invoice']['payment_method']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
                if($invoice['Invoice']['payment_method'] == 'Bank Transfer') {
                   $this->pdf_download($invoice['Invoice']['id'], false, true);
                }
			//END
        }

        public function process_invoice($invoice, $from_admin = false)
        {
            $is_new_license = false;
            if($invoice['Invoice']['type'] == 'License') {
                $licenses = json_decode($invoice['Invoice']['licenses'], true);
                foreach ($licenses as $extension_id => $quantity) {
                    if(strlen($extension_id) != 36)
                        $is_new_license = true;
                }
            }
            $new_component = in_array($invoice['Invoice']['system'], array('cs-cart', 'woo')) || $is_new_license;

            $component = $new_component ? $this->Invoices : $this->OpencartExtension;

        	if(empty($invoice))
        		throw new Exception('Empty invoice!');

        	if($invoice['Invoice']['state'] == 'Payed')
        		throw new Exception('Empty Payed!');

        	$invoices = $this->Invoice->find('first', array('fields' => 'Invoice.number', 'order' => array('Invoice.number DESC')));
        	$number = !empty($invoices['Invoice']['number']) ? $invoices['Invoice']['number'] + 1 : 1;

        	//Devman Extensions - info@devmanextensions.com - 2017-09-11 12:06:16 - Mark invoice like payed
        	$temp = array(
        		'Invoice.payed_date' => '"'.date('Y-m-d H:i:s').'"',
        		'Invoice.state' => '"Payed"',
        		'Invoice.number' => $number,
        		'Invoice.paypal_id_transaction' => '"'.(array_key_exists('paypal_id_transaction', $invoice['Invoice']) ? $invoice['Invoice']['paypal_id_transaction'] : '').'"',
        		'Invoice.tpv_id_transaction' => '"'.(array_key_exists('tpv_id_transaction', $invoice['Invoice']) ? $invoice['Invoice']['tpv_id_transaction'] : '').'"',
        		'Invoice.stripe_id_transaction' => '"'.(array_key_exists('stripe_id_transaction', $invoice['Invoice']) ? $invoice['Invoice']['stripe_id_transaction'] : '').'"'
        	);

        	if(in_array($invoice['Invoice']['type'], array('Renew', 'License', 'Add domain', 'New GMT Container')))
        	{
        		$temp['Invoice.solved_date'] = '"'.date('Y-m-d H:i:s').'"';
        		$temp['Invoice.solved'] = 1;
        	}

        	if(!$this->Invoice->updateAll($temp, array('Invoice.id' => $invoice['Invoice']['id'])))
				throw new Exception($e->getMessage());

        	//Create Invoice in new API system
            $invoice_data = $this->Invoice->findById($invoice['Invoice']['id']);
            $this->ApiLicenses->create_invoice($invoice_data['Invoice']);

        	//Devman Extensions - info@devmanextensions.com - 2017-09-11 11:53:59 - Depending type of invoice will realize some actions or anothers
        	switch ($invoice['Invoice']['type']) {
        		case 'Renew':
        			$component->renew_license($invoice);
        		break;

        		case 'Add domain':
        			$component->add_domain($invoice);
        		break;

        		case 'New GMT Container':
        			$component->increase_containers_number($invoice);
        		break;

        		case 'Personal develop':
        		break;

        		case 'GMT Extra service':
        		break;

        		case 'License':
        		    if(!$new_component) {
                        $licenses = json_decode($invoice['Invoice']['licenses']);
                        foreach ($licenses as $extension_id => $quantity) {
                            $this->ExtensionTool->create_license($extension_id, $quantity, $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_name']);
                        }
                    } else {
        		        $component->register_license($invoice);
                    }
        		break;

        		default:
        			throw new Exception('Not type found!');
        		break;
        	}

        	if(!$new_component) {
                //Devman Extensions - info@devmanextensions.com - 2017-09-26 10:03:56 - Send alert to customer
                    $subject = '[DevmanExtensions] Your invoice was paid';

                    $message = '';
                    $message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

                    $message .= 'Your invoice was paid, find extra information <a href="https://devmanextensions.com/invoices/invoices/pay_success">in this link</a>.';

                    try {
                        $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'Invoices System', $subject, $message);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                //END
            }

        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to me
				$subject = '[Invoice Payed]['.$invoice['Invoice']['payment_method'].']['.$invoice['Invoice']['type'].'] $'.$invoice['Invoice']['total'];

				$message = '';

				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				    if($invoice['Invoice']['type'] == 'GMT Extra service')
				        $this->Email->send_email('marketing@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END

			$this->Client->update_count_client_image();

			//Send PDF to determinate invoices types
			if($from_admin || (!$from_admin && !in_array($invoice['Invoice']['type'], array('Personal develop','Opencart Sales','Prestashop sales','GMT Extra service')))) {
                $path_pdf = $this->pdf_download($invoice['Invoice']['id'], true);
                $invoice = $this->Invoice->findById($invoice['Invoice']['id']);

                if($invoice['Invoice']['payment_method']  != 'Bank Transfer')
                $this->send_invoice_pdf($invoice, $path_pdf);
            }
        }

        public function send_invoice_pdf($invoice, $path_pdf, $ignore_number = false)
        {
            if(!$ignore_number) {
                if ($invoice['Invoice']['state'] != 'Payed')
                    throw new Exception('Invoice uncomplete');

                $temp = array(
                    'Invoice.pdf_send_date' => '"' . date('Y-m-d H:i:s') . '"'
                );

                if (!$this->Invoice->updateAll($temp, array('Invoice.id' => $invoice['Invoice']['id'])))
                    throw new Exception('Error updating pdf_send_date');

                //Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert to customer
                    $subject = '[DevmanExtensions] Your invoice #' . $invoice['Invoice']['number'];

                    $message = '';
                    $message .= sprintf('Hi %s!<br><br>', $invoice['Invoice']['customer_name']);

                    $message .= 'Attached your invoice. Thanks!';

                    try {
                        $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'Invoices System', $subject, $message, $path_pdf);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                //END
                return true;
            } else {
                $subject = '[DevmanExtensions] Bank transfer instructions';

                $message = '';
                $message .= sprintf('Hi, %s.<br><br>', $invoice['Invoice']['customer_name']);

                $message .= 'We attached PDF document with bank transfer data, once time that our team confirm that money arrived in to bank, we will start to work in your custom develop asap and we will send the final invoice PDF after custom work was finished.';

                try {
                    $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'Invoices System', $subject, $message, $path_pdf);
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        public function pdf_download($invoice_id, $save = false, $ignore_number = false)
		{
			$invoice = $this->Invoice->findById($invoice_id);

			if(!$ignore_number && empty($invoice['Invoice']['number']))
			{
				$this->Session->setFlash(
				    '<i class="fa fa-thumbs-down"></i>Invoiced not payed',
				    'default',
				    array('class' => 'error')
				);
				$this->redirect($this->referer());
			}

			//Has tax??
				$tax_price = 0;
				$total_without_tax = $invoice['Invoice']['total'];

				if(!empty($invoice['Invoice']['tax']))
				{
					$tax_temp = ($invoice['Invoice']['tax'] / 100) + 1;
					$total_without_tax = $invoice['Invoice']['total']/$tax_temp;
					$tax_price = $invoice['Invoice']['total']-$total_without_tax;
				}
			//END

			//Format currencies
				if($invoice['Invoice']['currency'] == 'EUR')
				{
					$currency_eur = $invoice['Invoice']['currency_euro_value'];
					$total_without_tax *= $currency_eur;
					$tax_price *= $currency_eur;

					$invoice['Invoice']['price'] = round($invoice['Invoice']['price']*$currency_eur, 2).'€';
					$invoice['Invoice']['total'] = round($invoice['Invoice']['total']*$currency_eur, 2).'€';

					$invoice['Invoice']['total_without_tax'] = round($total_without_tax, 2).'€';
					$invoice['Invoice']['tax_price'] = round($tax_price, 2).'€';
				}
				else
				{
					$invoice['Invoice']['price'] = '$'.round($invoice['Invoice']['price'], 2);
					$invoice['Invoice']['total'] = '$'.round($invoice['Invoice']['total'], 2);

					$invoice['Invoice']['total_without_tax'] = '$'.$total_without_tax;
					$invoice['Invoice']['tax_price'] = '$'.round($tax_price, 2);
				}

				//$this->set("invoice", $invoice);
			//END

			$this->layout = "ajax";

			$margin_top = 70;

			$this->Mpdf->init(array(
				'format' => 'A4',
				'font' => 'Calibri',
				'margin_top' => $margin_top.'px',
				'margin_bottom' => '20px',
				'allow_charset_conversion' => true,
				'charset_in' => 'UTF-8'
			));

			if(!$ignore_number) {
                $number_formatted = sprintf('%05d', $invoice['Invoice']['number']);
                $pdf_name = 'DevmanExtensions - Invoice ' . $number_formatted . '.pdf';
            } else {
                $pdf_name = 'Bank transfer instructions.pdf';
            }

			$final_file_path = APP.WEBROOT_DIR.DS.'files'.DS.'invoices_generated'.DS.$pdf_name;

            $this->autoRender = false;
            $view = new View($this->controllerTemp, false);
            $view->set("invoice", $invoice);

            if($ignore_number)
                $view_output = $view->render('/Invoices/bank_transfer_instructions');
            else
                $view_output = $view->render('/Invoices/pdf_download');

            $this->Mpdf->WriteHTML($view_output);

            if($save) {
                $this->Mpdf->Output($final_file_path, 'F');
                return $final_file_path;
            } else {
                $this->Mpdf->Output($pdf_name, 'D');
            }
        }


    }
?>
