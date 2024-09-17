<?php
	class InvoicesComponent extends Component {

		public function initialize(Controller $controller) {
            $this->Invoice = ClassRegistry::init('Invoice');
            $this->Extension = ClassRegistry::init('Extension');
            $this->paypal_fee = Configure::read('paypal_fee');
	        $this->stripe_fee = Configure::read('stripe_fee');
	        $this->discount_add_domain = 20;
            $this->discount_renew = 25;
            $this->months_allow_download = 6;
        }

        function startup($controller) {
		    $this->controller = $controller;
		}

        public $components = array(
            'ApiLicenses',
            'CountryTools',
            'OpencartExtension',
            'Email',
            'Client',
            'ExtensionTool',
            'Cart',
            'Session'
        );

		public function new_invoice($params, $request) {
		    $this->request = $request;

		    $system = !empty($params['system']) ? $params['system'] : '';

            $saving_invoice = $this->request && $this->request->is(array('post','put')) && array_key_exists('Invoice', $this->request->data);

			$cart_count = $this->Cart->count_products();
			$this->controller->set('cart_count', $cart_count);
			
			$license_id = !empty($this->request->data['support_id']) ? $this->request->data['support_id'] : (array_key_exists('support_id', $_GET) ? $_GET['support_id'] : '');
			$domain = !empty($this->request->data['domain']) ? $this->request->data['domain'] : '';
			$type = !empty($this->request->data['type']) ? $this->request->data['type'] : (array_key_exists('type', $_GET) ? $_GET['type'] : '');
			$quantity = !empty($this->request->data['quantity']) ? $this->request->data['quantity'] : (array_key_exists('quantity', $_GET) ? $_GET['quantity'] : '');

			if(empty($license_id) && !empty($this->request->data['Invoice']['license_id']))
			    $license_id = $this->request->data['Invoice']['license_id'];

			if(empty($type) && !empty($this->request->data['Invoice']['type']))
			    $type = $this->request->data['Invoice']['type'];

			if(!$saving_invoice) {
                if (empty($type))
                    die('ERROR: Type invoice empty');

                if ($type == 'renew_license' && empty($license_id))
                    die('ERROR: License empty');

                if ($type == 'add_domain' && (empty($license_id) || empty($domain)))
                    die('ERROR: License/domain empty');
            }


			if($saving_invoice && $this->validate_invoice_form())
    		{
    			//Devman Extensions - info@devmanextensions.com - 2017-09-09 18:57:09 - Generate invoice
					try {
						$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
						$ds->begin($this);
						$ds->commit();
                        $this->request->data['Invoice']['system'] = $system;
					    $invoice_id = $this->generate_invoice($this->request->data);
					    if(in_array($this->request->data['Invoice']['payment_method'], array('Paypal', 'Credit Card', 'Stripe'))) {
					        $pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$invoice_id;
					        $this->controller->redirect($pay_url);
                        }
					   	elseif($this->request->data['Invoice']['payment_method'] == 'Bank Transfer')
					   		$this->Session->setFlash(sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment instructions.'),$this->request->data['Invoice']['customer_email']), 'default', array('class' => 'success'));

					} catch (Exception $e) {
						$ds->rollback();
						$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
					}
    			//END
	        }
            $this->request->data['Invoice']['system'] = $system;
	        //Connect to API to get support_id information
                if(!empty($license_id)) {
                    $license_info = $this->ApiLicenses->get_license_info($license_id);

                    if (!is_array($license_info) || empty($license_info['email']) || empty($license_info['price']))
                        die('ERROR: API Connection error.');
                }
            //END

			$title = $button_tittle = $button_icon = 'NOT DEFINED';

			$this->request->data['Invoice']['quantity'] = !empty($quantity) ? $quantity : 1;

			//Devman Extensions - info@devmanextensions.com - 2017-09-09 15:10:48 - Load basic datas of invoice ONLY WHEN ENTRY FIRST TIME
				switch ($type) {
					case 'renew_license':
						try {
							$price = $license_info['price'];
						    $discount = $this->get_discount_renew();
						    $total = $license_info['price'];
 						} catch (Exception $e) {
							die($e->getMessage());
						}

						if(empty($this->request->data['Invoice']['customer_email']))
							$this->request->data['Invoice']['customer_email'] = $license_info['email'];
						$this->request->data['Invoice']['type'] = 'Renew';
						$this->request->data['Invoice']['payment_method'] = 'Credit Card';
						$this->request->data['Invoice']['description'] = 'Renew license <b>#'.$license_id.'</b>';
						$this->request->data['Invoice']['price'] = $price;
						$this->request->data['Invoice']['total'] = $total;
						$this->request->data['Invoice']['discount'] = $discount;
						$this->request->data['Invoice']['tax'] = '0';
					break;

					case 'add_domain':
						try {
							$price = $license_info['price'];
						    $discount = $this->get_discount_add_domain();
						    $total = $license_info['price'];
 						} catch (Exception $e) {
							die($e->getMessage());
						}

						if(empty($this->request->data['Invoice']['customer_email']))
							$this->request->data['Invoice']['customer_email'] = $license_info['email'];
						$this->request->data['Invoice']['type'] = 'Add domain';

						$this->request->data['Invoice']['payment_method'] = 'Credit Card';
						$this->request->data['Invoice']['description'] = 'Increase license <b>#'.$license_id.'</b> add domain: <b>'.$domain.'</b>';
						$this->request->data['Invoice']['price'] = $price;
						$this->request->data['Invoice']['total'] = $total;
						$this->request->data['Invoice']['discount'] = $discount;
						$this->request->data['Invoice']['tax'] = '0';
						$this->request->data['Invoice']['new_domain'] = $domain;
					break;

					case 'new_gmt_container':
						try {
							$price = $license_info['price'];
						    $discount = $this->get_discount_renew();
						    $total = $license_info['price'];
 						} catch (Exception $e) {
							die($e->getMessage());
						}

						if(empty($this->request->data['Invoice']['customer_email']))
							$this->request->data['Invoice']['customer_email'] = $license_info['email'];
						$this->request->data['Invoice']['type'] = 'New GMT Container';

						$this->request->data['Invoice']['payment_method'] = 'Credit Card';
						$this->request->data['Invoice']['description'] = 'Increase containers number GMT <b>#'.$license_id.'</b>';
						$this->request->data['Invoice']['price'] = $price;
						$this->request->data['Invoice']['total'] = $total;
						$this->request->data['Invoice']['discount'] = $discount;
						$this->request->data['Invoice']['tax'] = '0';

					break;

					case 'license':
					    $extension_data = $this->ApiLicenses->get_extension($params['platform'], $params['extension']);
                        $extension_price = $extension_data['price'];
                        $licenses[$extension_data['platform_slug'].'-'.$extension_data['extension']['slug']] = 1;
                        $licenses_text = '1 x '.$extension_data['extension']['name'].__(' for ').$extension_data['platform_name'].' - $'.$extension_price.'<br>';
						$licenses_text = '<i>'.trim($licenses_text, '<br>').'</i>';
						$this->request->data['Invoice']['description_avanced'] = $licenses_text;
						$this->request->data['Invoice']['type'] = 'License';
						$this->request->data['Invoice']['licenses'] = json_encode($licenses);
						$this->request->data['Invoice']['payment_method'] = 'Credit Card';
						$this->request->data['Invoice']['description'] = __('Purchase license');
						$this->request->data['Invoice']['price'] = $extension_price;
						$this->request->data['Invoice']['total'] = $extension_price;
						$this->request->data['Invoice']['discount'] = 0;
						$this->request->data['Invoice']['tax'] = 0;
					break;

					default:
						if (!$this->request->is(array('post','put')))
							die('didnt loaded basic datas');
					break;
				}
			//END

			if($this->request && !$this->request->is(array('post','put')))
				$this->Session->write('total_invoice', $this->request->data['Invoice']['total']);
			//Devman Extensions - info@devmanextensions.com - 2017-09-09 17:39:59 - Need apply tax??
				$is_eu = !empty($this->request->data['Invoice']['customer_country_id']) && $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
				$is_spain = !empty($invoice['Invoice']['customer_country_id']) && $invoice['Invoice']['customer_country_id'] == 195;
				$apply_tax = $is_spain || ($is_eu && empty($this->request->data['Invoice']['customer_vat']));

				$this->controller->set(compact('apply_tax', 'is_eu'));
			//END

			if(in_array($type, array('renew_license', 'Renew')))
			{
				$title = __('Renew license').' <b>#'.$license_id.'</b>';
				$button_title = 'Renew license';
				$button_icon = 'ticket';
			}
			if(in_array($type, array('add_domain', 'Add domain')))
			{
				$title = __('Add new domain').' <b>#'.$license_id.'</b>';
				$button_title = 'Add new domain';
				$button_icon = 'globe';
			}
			if(in_array($type, array('new_gmt_container', 'New GMT Container')))
			{
				$title = __('New GMT Container').' <b>#'.$license_id.'</b>';
				$button_title = 'Increase containers number';
				$button_icon = 'ticket';
			}
			if(in_array($type, array('license', 'License')))
			{
				$title = __('Finish purchase');
				$button_title = 'Finish purchase';
				$button_icon = 'ticket';
			}

			$this->request->data['paypal_fee'] = $this->paypal_fee;
			$this->request->data['stripe_fee'] = $this->stripe_fee;

			$this->controller->set(compact('title','button_title','button_icon'));

			$this->request->data['Invoice']['license_id'] = $license_id;

			$countries = $this->CountryTools->select_format_countries();

			$this->controller->set(compact('countries'));

			$zones = array();

			if(!empty($this->request->data['Invoice']['customer_country_id']))
				$zones = $this->CountryTools->select_format_zones($this->request->data['Invoice']['customer_country_id']);

			$this->controller->set(compact('zones'));

			$this->request->data['eur_currency_value'] = Configure::read('eur_currency_value');
			$this->request->data['dollar_currency_value'] = Configure::read('dollar_currency_value');
        }

        public function validate_invoice_form()
		{
			$copy_data = $this->request->data;

			if(empty($copy_data['Invoice']['customer_name']))
			{
				$this->Session->setFlash('Error: Fill name', 'default', array('class' => 'error'));
				return false;
			}

			if(empty($copy_data['Invoice']['customer_email']))
			{
				$this->Session->setFlash('Error: Fill email', 'default', array('class' => 'error'));
				return false;
			}

			if(empty($copy_data['Invoice']['customer_country_id']))
			{
				$this->Session->setFlash('Error: Select country', 'default', array('class' => 'error'));
				return false;
			}

			if($copy_data['Invoice']['customer_zone_id'] === '')
			{
				$this->Session->setFlash('Error: Select region/state', 'default', array('class' => 'error'));
				return false;
			}

			if($copy_data['Invoice']['customer_city'] === '')
			{
				$this->Session->setFlash('Error: Fill city', 'default', array('class' => 'error'));
				return false;
			}

			if($copy_data['Invoice']['customer_address'] === '')
			{
				$this->Session->setFlash('Error: Fill address', 'default', array('class' => 'error'));
				return false;
			}

			if($copy_data['Invoice']['customer_post_code'] === '')
			{
				$this->Session->setFlash('Error: Fill post code', 'default', array('class' => 'error'));
				return false;
			}

			$is_eu = $this->CountryTools->is_eu($copy_data['Invoice']['customer_country_id']);

			if($is_eu && !empty($copy_data['Invoice']['customer_vat']))
			{
				try {
					$validate_vat = $this->CountryTools->validate_vat($copy_data['Invoice']['customer_country_id'], $copy_data['Invoice']['customer_vat']);
				} catch (Exception $e) {
					$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
				}

				if(!$validate_vat)
				{
					$this->Session->setFlash('Error: Insert a valid VAT number', 'default', array('class' => 'error'));
					return false;
				}
			}

			return true;
		}

        public function generate_invoice($invoice)
        {
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
                    }
        		}
        	//END

			$is_eu = $this->CountryTools->is_eu($invoice['Invoice']['customer_country_id']);
			$is_spain = $invoice['Invoice']['customer_country_id'] == 195;

			//$temp_total = $this->Session->read('total_invoice');
            $temp_total = $this->request->data['Invoice']['price'];

			if($is_spain || ($is_eu && empty($invoice['Invoice']['customer_vat'])))
			{
				$temp_total = $temp_total * (($invoice['Invoice']['tax']/100) + 1);
				/*$temp_total = $invoice['Invoice']['price']*$invoice['Invoice']['quantity'];
				$invoice['Invoice']['total'] = $temp_total * (($invoice['Invoice']['tax']/100) + 1);*/
			}
			else
				$invoice['Invoice']['tax'] = 0;

			if($is_eu)
				$invoice['Invoice']['currency'] = 'EUR';

			if($invoice['Invoice']['payment_method'] == 'Paypal') {
				//$invoice['Invoice']['total'] *= Configure::read('paypal_fee');
				$temp_total *= Configure::read('paypal_fee');
			}

			if($invoice['Invoice']['payment_method'] == 'Stripe') {
				//$invoice['Invoice']['total'] *= Configure::read('stripe_fee');
				$temp_total *= Configure::read('stripe_fee');
			}


			$invoice['Invoice']['customer_country'] = $this->CountryTools->get_country_name($invoice['Invoice']['customer_country_id']);
			$invoice['Invoice']['customer_zone'] = $this->CountryTools->get_zone_name($invoice['Invoice']['customer_zone_id']);
			$invoice['Invoice']['system'] = !empty($invoice['Invoice']['system']) ? $invoice['Invoice']['system']: 'Opencart';

			//Devman Extensions - info@devmanextensions.com - 2017-09-15 15:44:13 - Discount
				$discount = !empty($invoice['Invoice']['discount']) ? $invoice['Invoice']['discount'] : 0;

				$general_coupon = $this->Cart->get_general_coupon();
				$exist_general_coupon = !empty($general_coupon) && array_key_exists('discount', $general_coupon);
				if($discount > 0 && !$exist_general_coupon)
				{
					$real_discount = (100-$discount) / 100;
					//$invoice['Invoice']['total'] = $invoice['Invoice']['total'] * $real_discount;
                    $temp_total = $temp_total * $real_discount;
				}
			//END

			$invoice['Invoice']['total'] = round($invoice['Invoice']['total'], 2);
			$temp_total = round($temp_total, 2);

			if($invoice['Invoice']['total'] != $temp_total) {
                throw new Exception ('Invoice total is not correct.');
            }

			//2018-12-29 Control zone_id is numeric
            if(!is_numeric($invoice['Invoice']['customer_zone_id']))
                throw new Exception ('Select "<b>Region / State</b>"');

        	if(!$this->Invoice->saveAll($invoice))
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

        public function validate_invoice($id_invoice, $request)
        {
            $this->request = $request;

            $this->layout = 'frontend';

			if(!empty($id_invoice))
				$invoice = $this->Invoice->findById($id_invoice);
			else
				$invoice = $this->request->data;

			if(!$this->request->is(array('post','put')) && !empty($invoice))
				$this->request->data = $invoice;

			$this->request->data['Invoice']['currency_euro_value'] = $invoice['Invoice']['currency_euro_value'];
			$this->request->data['Invoice']['system'] = !empty($invoice['Invoice']['system']) ? $invoice['Invoice']['system'] : '';

			if($this->request->is(array('post','put')) && $this->validate_invoice_form())
    		{
    			//Devman Extensions - info@devmanextensions.com - 2017-09-09 18:57:09 - Generate invoice
					try {
						$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
						$ds->begin($this);
					    $this->generate_invoice($this->request->data);
                        $ds->commit();

					    if(in_array($this->request->data['Invoice']['payment_method'], array('Paypal', 'Credit Card', 'Stripe'))) {
					        $pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$this->request->data['Invoice']['id'];
					        $this->controller->redirect($pay_url);
                            //$this->Session->setFlash(sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment link.'),$this->request->data['Invoice']['customer_email']), 'default', array('class' => 'success'));
                        }
					   	elseif($this->request->data['Invoice']['payment_method'] == 'Bank Transfer') {
                            $success_message = sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment instructions.'), $this->request->data['Invoice']['customer_email']);
                            $this->Session->setFlash($success_message, 'default', array('class' => 'success'));
                        }
					} catch (Exception $e) {
						$ds->rollback();
						$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
					}
    			//END
    		} else {
				$this->Session->write('total_invoice', $this->request->data['Invoice']['price']*$this->request->data['Invoice']['quantity']);
    		}

    		$countries = $this->CountryTools->select_format_countries();

			$this->controller->set(compact('countries'));

			$zones = array();

			if(!empty($this->request->data['Invoice']['customer_country_id']))
				$zones = $this->CountryTools->select_format_zones($this->request->data['Invoice']['customer_country_id']);

			$this->controller->set(compact('zones'));
			$zone_id = !empty($invoice['Invoice']['customer_zone_id']) ? $invoice['Invoice']['customer_zone_id'] : 0;
            $this->controller->set(compact('zone_id', $zone_id));
			//Devman Extensions - info@devmanextensions.com - 2017-09-09 17:39:59 - Need apply tax??
				$is_eu = !empty($this->request->data['Invoice']['customer_country_id']) && $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
				$is_spain = $invoice['Invoice']['customer_country_id'] == 195;
				$apply_tax = $is_spain || ($is_eu && empty($this->request->data['Invoice']['customer_vat']));

				$this->controller->set(compact('apply_tax', 'is_eu'));
			//END

			//$tax = $this->request->data['Invoice']['tax'] > 0 ? ($this->request->data['Invoice']['tax']/100 + 1) : 0;
			$this->request->data['paypal_fee'] = $this->paypal_fee;
			$this->request->data['stripe_fee'] = $this->stripe_fee;

			$this->request->data['dollar_currency_value'] = Configure::read('dollar_currency_value');

        }

        public function send_emails_renew($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$license_info = $this->ApiLicenses->get_license_info($license_id);

				$email = $license_info['email'];
				$price = $license_info['price'];
                $extension_name = $license_info['extension'];
                $current_domains = $license_info['domains_formatted'];

				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][Renew]['.$license_id.']['.$extension_name.'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price renew:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $extension_name);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $current_domains);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_add_domain($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
				$license_id = $invoice['Invoice']['license_id'];
				$license_info = $this->ApiLicenses->get_license_info($license_id);

				$email = $license_info['email'];
				$price = $license_info['price'];
                $extension_name = $license_info['extension'];
                $current_domains = $license_info['domains_formatted'];

				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][Add domain]['.$license_id.']['.$extension_name.'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price add domain:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $extension_name);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $current_domains);
					$message .= sprintf('<b>New domain:</b> %s<br>', $invoice['Invoice']['new_domain']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_license($invoice)
        {
			$tax_text = '';

			if(!empty($invoice['Invoice']['tax']) && $invoice['Invoice']['tax'] > 0)
				$tax_text .= '<br><b>Taxes:</b> '.$invoice['Invoice']['tax'].'% - $'.(number_format($invoice['Invoice']['total']-$invoice['Invoice']['price'], 2));

        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][License] $'.$invoice['Invoice']['total'];


				$message = '<b>'.__('Products').':</b><br>';
				$message .= $invoice['Invoice']['description_avanced'];
				$message .= $tax_text.'<br>';

				$message .= sprintf('<b>Total:</b> $%s<br>', $invoice['Invoice']['total']);
				$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function send_emails_add_new_gmt_container($invoice)
        {
        	//Devman Extensions - info@devmanextensions.com - 2017-09-09 19:08:00 - Send alert email to admin
        		$license_id = $invoice['Invoice']['license_id'];
				$license_info = $this->ApiLicenses->get_license_info($license_id);

				$email = $license_info['email'];
				$price = $license_info['price'];
                $extension_name = $license_info['extension'];
                $current_domains = $license_info['domains_formatted'];

				$subject = '[New invoice]['.$invoice['Invoice']['payment_method'].'][GMT New container]['.$license_id.']['.$extension_name.'] $'.$invoice['Invoice']['total'];

				$message = '';
					$message .= sprintf('<b>Price new container:</b> $%s<br>', $invoice['Invoice']['total']);
					$message .= sprintf('<b>Extension:</b> %s<br>', $extension_name);
					$message .= sprintf('<b>Buyer email:</b> <a href="mailto:%s">%s</a><br>', $invoice['Invoice']['customer_email'], $invoice['Invoice']['customer_email']);
					$message .= sprintf('<b>Current domains:</b> %s<br>', $current_domains);
				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			//END
        }

        public function renew_license($invoice) {
            $this->ApiLicenses->renew_license($invoice['Invoice']['license_id']);
        }

        public function add_domain($invoice) {
            $this->ApiLicenses->add_domain($invoice['Invoice']['license_id'], $invoice['Invoice']['new_domain']);
        }

        public function increase_containers_number($invoice) {
            $this->ApiLicenses->increase_containers_number($invoice['Invoice']['license_id'], $invoice['Invoice']['quantity']);
        }

        public function register_license($invoice) {
		    $products = array();
		    $extensions = json_decode($invoice['Invoice']['licenses'], true);
		    foreach ($extensions as $extension_id => $quantity) {
		        $id_exploded = explode('-',$extension_id);

		        if(count($id_exploded) == 3) {
                    $platform = $id_exploded[0] . '-' . $id_exploded[1];
                    $extension_slug = $id_exploded[2];
                }
		        else {
                    $platform = $id_exploded[0];
                    $extension_slug = $id_exploded[1];
                }

		        $products[] = array(
		            'platform' => $platform,
                    'extension' => $extension_slug,
                    'domains' => $quantity,
                    'workspaces' => $extension_slug == 'gmt' ? $quantity : 0,
                );

		    }
		    $params = array(
		        'email' => $invoice['Invoice']['customer_email'],
                'name' => $invoice['Invoice']['customer_name'],
                'products' => $products
            );

		    $this->ApiLicenses->register_license($params);
        }

        public function get_invoice_total($invoice_data) {		    
            //echo 'Original total: '.$total.'<br>';
		    //Extensions prices
		    if($invoice_data['Invoice']['type'] == 'License') {
		        $total = 0;
		        $general_coupon = $this->Cart->get_general_coupon();
		        $licenses = json_decode($invoice_data['Invoice']['licenses'], true);
		        foreach ($licenses as $extension_id => $quantity) {
                    $extension = $this->Extension->findById($extension_id);
                    $extension_price = $extension['Extension']['prices'][$quantity] * $quantity;
                    if($quantity == 1 && !empty($general_coupon['discount'])) {
                        $extension_price *= (100-$general_coupon['discount'])/100;
                    }
                    $total += $extension_price;
		        }
            } else {

                //Devman Extensions - info@devmanextensions.com - 21/1/21 16:51 - New code for calculate total
                $total = $invoice_data['Invoice']['price'] * $invoice_data['Invoice']['quantity'];
                if (!empty($invoice_data['Invoice']['discount']))
                    $total = $total * ((100-$invoice_data['Invoice']['discount']) / 100);
            }
            //Possible country taxes
                $is_ue = $this->CountryTools->is_eu($invoice_data['Invoice']['customer_country_id']);
		        $vat_number = $invoice_data['Invoice']['customer_vat'];
                $is_spain = $invoice_data['Invoice']['customer_country_id'] == 195;

                $tax = $is_spain || ($is_ue && !$is_spain && empty($vat_number)) || ($is_ue && !empty($vat_number) && !$is_spain && !$this->CountryTools->validate_vat($invoice_data['Invoice']['customer_country_id'], $vat_number)) ? $this->CountryTools->get_tax($invoice_data['Invoice']['customer_country_id']) : 0;

                if($tax > 0)
                    $total *= $tax;


            //Possible fees by payment method
                if(in_array($invoice_data['Invoice']['payment_method'], array('Stripe', 'Paypal'))) {
                    $total *= $this->{strtolower($invoice_data['Invoice']['payment_method']).'_fee'};
                }
                
            return $total;
        }

        public function get_discount_renew()
		{
			return $this->discount_renew;
		}

		public function get_discount_add_domain()
		{
			return $this->discount_add_domain;
		}
    }
?>