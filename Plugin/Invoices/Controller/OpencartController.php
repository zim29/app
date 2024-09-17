<?php
	class OpencartController extends InvoicesAppController {

		public  $uses = array(
      		'Sales.Sale',
      		'Invoices.Invoice',
			'Accounts.Account',
			'Extensions.Extension'
		);

		public $components = array(
		    'OpencartExtension',
		    'Email',
		    'OpencartFormGenerator',
		    'OpencartTicket',
		    'CountryTools',
		    'Invoices.InvoicesOpencart',
		    'Cart',
		    'Session',
            'Invoices.Invoices',
            'ApiLicenses'
		);

		public function beforeFilter() {
	        $this->Auth->allow('new_invoice', 'validate_invoice');
	        $this->action_renew_license = 'increase_license_confirm';
	        $this->action_add_domain = 'add_domain_confirm';
	        $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
	        $this->paypal_fee = Configure::read('paypal_fee');
	        $this->stripe_fee = Configure::read('stripe_fee');
	    }

	    public function new_invoice()
		{
			$is_logged = $this->Session->read("logged");

			if(!$is_logged)
				$this->redirect(array('plugin' => 'accounts', 'controller' => 'accounts', 'action'=>'index'));

			if(empty($this->request->data) && $is_logged) {
				$account = $this->Account->find('first', array('conditions' => array('Account.id' => $is_logged)));
				$this->request->data['Invoice']['customer_name'] = $account['Account']['name'];
				$this->request->data['Invoice']['customer_vat'] = $account['Account']['vat'];
				$this->request->data['Invoice']['customer_email'] = $account['Account']['email'];
				$this->request->data['Invoice']['customer_country_id'] = $account['Account']['country_id'];
				$this->request->data['Invoice']['customer_zone_id'] = $account['Account']['zone_id'];
				$this->request->data['Invoice']['customer_city'] = $account['Account']['city'];
				$this->request->data['Invoice']['customer_address'] = $account['Account']['address'];
				$this->request->data['Invoice']['customer_post_code'] = $account['Account']['post_code'];
			}

		    $this->set('noindex', true);
		    $cart_count = $this->Cart->count_products();
            $this->set('cart_count', $cart_count);
            $this->layout = 'frontend';

			//Datalayer checkout started
				$cart_products = $this->Cart->get_products();
				$this->Extension->recursive = -1;
				$products = array();
				foreach ($cart_products as $key => $prod) {
					$extension = $this->Extension->findById($prod['id']);
					$extension_data = $this->Extension->formatExtensionToDatalayer($extension);
					$extension_data['price'] = $prod['price'];
					$extension_data['quantity'] = $prod['quantity'];
					$extension_data['total'] = $prod['total'];
					$products[] = $extension_data;
				}

				$cart_data = array(
					'event_id' => $this->generate_uuid(),
					'checkout_url' => Router::url('/', true).'invoices/opencart/new_invoice',
					'total' => $this->Cart->get_total(),
					'products' => $products
				);

				$datalayer = '
						<script>
							dataLayer.push({
								"event": "checkoutStarted",
								"checkoutStarted": ' . json_encode($cart_data) . '
							});
						</script>
					';

				$this->set("datalayer", $datalayer);
			//END - Datalayer checkout started


		    if(!empty($_GET['platform'])) {
		        $params = array(
                    'platform' => !empty($_GET['platform']) ? $_GET['platform'] : die('Param "platform" not found'),
                    'system' => !empty($_GET['platform']) ? $_GET['platform'] : die('Param "platform" not found'),
                    'extension' => !empty($_GET['extension']) ? $_GET['extension'] : die('Param "extension" not found')
                );
		        $this->Invoices->new_invoice($params, $this->request);
            } else {
                $license_id = !empty($this->request->data['Invoice']['license_id']) ? $this->request->data['Invoice']['license_id'] : (array_key_exists('license_id', $_GET) ? $_GET['license_id'] : '');
                $domain = !empty($this->request->data['Invoice']['domain']) ? $this->request->data['Invoice']['domain'] : (array_key_exists('domain', $_GET) ? $_GET['domain'] : '');
                $type = !empty($this->request->data['Invoice']['type']) ? $this->request->data['Invoice']['type'] : (array_key_exists('type', $_GET) ? $_GET['type'] : '');

                if (empty($type))
                    die('ERROR: Type invoice empty');

                if ($type == 'renew_license' && empty($license_id))
                    die('ERROR: License empty');

                if ($type == 'add_domain' && (empty($license_id) || empty($domain)))
                    die('ERROR: License/domain empty');

                if ($this->request->is(array('post', 'put')) && $this->validate()) {
                    //Devman Extensions - info@devmanextensions.com - 2017-09-09 18:57:09 - Generate invoice
                    try {
                        $ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
                        $ds->begin($this);
                        $ds->commit();

                        $invoice_id = $this->InvoicesOpencart->generate_invoice($this->request->data);
                        if (in_array($this->request->data['Invoice']['payment_method'], array('Paypal', 'Credit Card', 'Stripe'))) {
                            $pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/' . $invoice_id;
                            $this->redirect($pay_url);
                            //$this->Session->setFlash(sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment link.'),$this->request->data['Invoice']['customer_email']), 'default', array('class' => 'success'));
                        } elseif ($this->request->data['Invoice']['payment_method'] == 'Bank Transfer')
                            $this->Session->setFlash(sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment instructions.'), $this->request->data['Invoice']['customer_email']), 'default', array('class' => 'success'));

                    } catch (Exception $e) {
                        $ds->rollback();
                        $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
                    }
                    //END
                }

                $title = $button_tittle = $button_icon = 'NOT DEFINED';

                //Devman Extensions - info@devmanextensions.com - 2017-09-09 15:10:48 - Load basic datas of invoice ONLY WHEN ENTRY FIRST TIME
                switch ($type) {
                    case 'renew_license':
                        try {
                            $license_info = $this->OpencartExtension->license_get_license($license_id);
                            $price = $this->OpencartExtension->get_extension_price($license_id);
                            $discount = $this->OpencartExtension->get_discount_renew();
                            $total = $this->OpencartExtension->get_extension_price_renew($license_id);
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }

                        if (empty($this->request->data['Invoice']['customer_email']))
                            $this->request->data['Invoice']['customer_email'] = $license_info['Sale']['buyer_email'];
                        $this->request->data['Invoice']['type'] = 'Renew';
                        $this->request->data['Invoice']['payment_method'] = 'Credit Card';
                        $this->request->data['Invoice']['description'] = 'Renew license <b>#' . $license_id . '</b>';
                        $this->request->data['Invoice']['price'] = $price;
                        $this->request->data['Invoice']['total'] = $total;
                        $this->request->data['Invoice']['discount'] = $discount;
                        $this->request->data['Invoice']['tax'] = '0';
                        break;

                    case 'add_domain':
                        try {
                            $license_info = $this->OpencartExtension->license_get_license($license_id);
                            $price = $this->OpencartExtension->get_extension_price($license_id);
                            $discount = $this->OpencartExtension->get_discount_add_domain();
                            $total = $this->OpencartExtension->get_extension_price_add_domain($license_id);
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }

                        if (empty($this->request->data['Invoice']['customer_email']))
                            $this->request->data['Invoice']['customer_email'] = $license_info['Sale']['buyer_email'];
                        $this->request->data['Invoice']['type'] = 'Add domain';

                        $this->request->data['Invoice']['payment_method'] = 'Credit Card';
                        $this->request->data['Invoice']['description'] = 'Increase license <b>#' . $license_id . '</b> add domain: <b>' . $domain . '</b>';
                        $this->request->data['Invoice']['price'] = $price;
                        $this->request->data['Invoice']['total'] = $total;
                        $this->request->data['Invoice']['discount'] = $discount;
                        $this->request->data['Invoice']['tax'] = '0';
                        $this->request->data['Invoice']['new_domain'] = $domain;
                        break;

                    case 'new_gmt_container':
                        try {
                            $license_info = $this->OpencartExtension->license_get_license($license_id);
                            $price = $this->OpencartExtension->get_extension_price($license_id);
                            $discount = $this->OpencartExtension->get_discount_renew();
                            $total = $this->OpencartExtension->get_extension_price_renew($license_id);
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }

                        if (empty($this->request->data['Invoice']['customer_email']))
                            $this->request->data['Invoice']['customer_email'] = $license_info['Sale']['buyer_email'];
                        $this->request->data['Invoice']['type'] = 'New GMT Container';

                        $this->request->data['Invoice']['payment_method'] = 'Credit Card';
                        $this->request->data['Invoice']['description'] = 'Increase containers number GMT <b>#' . $license_id . '</b>';
                        $this->request->data['Invoice']['price'] = $price;
                        $this->request->data['Invoice']['total'] = $total;
                        $this->request->data['Invoice']['discount'] = $discount;
                        $this->request->data['Invoice']['tax'] = '0';
                        break;

                    case 'license':
                        $cart = $this->Cart->get_products();
                        $total = $this->Cart->get_total();

                        $general_coupon = $this->Cart->get_general_coupon();
                        if (!empty($general_coupon) && array_key_exists('discount', $general_coupon)) {
                            $discount = $general_coupon['discount'];
                            $total_without_discount = 100 * $total / (100 - $discount);
                        } else {
                            $discount = 0;
                            $total_without_discount = $total;
                        }

                        $licenses = array();
                        $licenses_text = '';
                        foreach ($cart as $key => $ext) {
                            $licenses[$ext['id']] = $ext['quantity'];
                            $licenses_text .= $ext['quantity'] . ' x ' . $ext['name'] . ' - $' . $ext['special'] . '<br>';
                        }
                        $licenses_text = '<i>' . trim($licenses_text, '<br>') . '</i>';
                        $this->request->data['Invoice']['description_avanced'] = $licenses_text;
                        $this->request->data['Invoice']['type'] = 'License';
                        $this->request->data['Invoice']['licenses'] = json_encode($licenses);
                        $this->request->data['Invoice']['payment_method'] = 'Credit Card';
                        $this->request->data['Invoice']['description'] = $cart_count > 1 ? __('Purchase pack licenses') : __('Purchase license');
                        $this->request->data['Invoice']['price'] = $total_without_discount;
                        $this->request->data['Invoice']['total'] = $total;
                        $this->request->data['Invoice']['discount'] = $discount;
                        $this->request->data['Invoice']['tax'] = 0;
                        break;

                    default:
                        if (!$this->request->is(array('post', 'put')))
                            die('didnt loaded basic datas');
                        break;
                }
                //END


                //Devman Extensions - info@devmanextensions.com - 11/1/21 19:40 - Nuevo campo para nueva API de facturación, aquí creamos la factura
                    /*if(!empty($license_info['Sale'])) {
                        $this->Invoice->save($this->request->data['Invoice']);
                        $this->request->data['Invoice']['id'] = $this->Invoice->getLastInsertId();
                        $suscription_types = array("Renew", "Add domain", "New GMT Container");
                        $license_types = array("License");
                        $type = in_array($this->request->data['Invoice']['type'], $license_types) ? 'EDS' : 'SBS';

                        $this->request->data['Invoice']['customer_name'] = $license_info['Sale']['buyer_username'];
                        $this->request->data['Invoice']['customer_email'] = $license_info['Sale']['buyer_email'];

                        try {
                            $invoice_api_id = $this->ApiLicenses->create_invoice($this->request->data, $type);
                        } catch (Exception $e) {
                            $this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
                            $this->redirect($this->referer());
                        }
                        $this->redirect("https://cloud.devmanextensions.com/business/invoice/" . $invoice_api_id);
                    }*/

                //echo '<pre>'; print_r($this->request->data);  echo '</pre>'; die;

                if (!$this->request->is(array('post', 'put')))
                    $this->Session->write('total_invoice', $this->request->data['Invoice']['total']);
                //Devman Extensions - info@devmanextensions.com - 2017-09-09 17:39:59 - Need apply tax??
                $is_eu = !empty($this->request->data['Invoice']['customer_country_id']) && $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
                $is_spain = !empty($invoice['Invoice']['customer_country_id']) && $invoice['Invoice']['customer_country_id'] == 195;
                $apply_tax = $is_spain || ($is_eu && empty($this->request->data['Invoice']['customer_vat']));

                $this->set(compact('apply_tax', 'is_eu'));
                //END

                if (in_array($type, array('renew_license', 'Renew'))) {
                    $title = __('Renew license') . ' <b>#' . $license_id . '</b>';
                    $button_title = 'Renew license';
                    $button_icon = 'ticket';
                }
                if (in_array($type, array('add_domain', 'Add domain'))) {
                    $title = __('Add new domain') . ' <b>#' . $license_id . '</b>';
                    $button_title = 'Add new domain';
                    $button_icon = 'globe';
                }
                if (in_array($type, array('new_gmt_container', 'New GMT Container'))) {
                    $title = __('New GMT Container') . ' <b>#' . $license_id . '</b>';
                    $button_title = 'Increase containers number';
                    $button_icon = 'ticket';
                }
                if (in_array($type, array('license', 'License'))) {
                    $title = __('Finish purchase');
                    $button_title = 'Finish purchase';
                    $button_icon = 'ticket';
                }

                $this->request->data['paypal_fee'] = $this->paypal_fee;
                $this->request->data['stripe_fee'] = $this->stripe_fee;

                $this->set(compact('title', 'button_title', 'button_icon'));

                $this->request->data['Invoice']['license_id'] = $license_id;

                $countries = $this->CountryTools->select_format_countries();

                $this->set(compact('countries'));

                $zones = array();

                if (!empty($this->request->data['Invoice']['customer_country_id']))
                    $zones = $this->CountryTools->select_format_zones($this->request->data['Invoice']['customer_country_id']);

                $this->set(compact('zones'));

                $this->request->data['Invoice']['quantity'] = 1;
                $this->request->data['eur_currency_value'] = Configure::read('eur_currency_value');
                $this->request->data['dollar_currency_value'] = Configure::read('dollar_currency_value');
            }
		}

		public function validate_invoice($id_invoice = null)
		{
		    $this->set('noindex', true);
			$this->layout = 'frontend';

			if(!empty($id_invoice))
				$invoice = $this->Invoice->findById($id_invoice);
			else
				$invoice = $this->request->data;

			if(!$this->request->is(array('post','put')) && !empty($invoice))
				$this->request->data = $invoice;

			if($this->request->is(array('post','put')) && $this->validate())
    		{
    			//Devman Extensions - info@devmanextensions.com - 2017-09-09 18:57:09 - Generate invoice
					try {
						$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
						$ds->begin($this);

						$this->request->data['Invoice']['system'] = !empty($invoice['Invoice']['system']) ? $invoice['Invoice']['system'] : '';

						if($this->request->data['Invoice']['payment_method'] == 'Bank Transfer')
						    $this->layout = 'ajax';

						$this->InvoicesOpencart->validate_invoice($this->request->data);
                        $ds->commit();

					    if(in_array($this->request->data['Invoice']['payment_method'], array('Paypal', 'Credit Card', 'Stripe'))) {
					        $pay_url = 'https://devmanextensions.com/invoices/invoices/pay_invoice/'.$this->request->data['Invoice']['id'];
					        $this->redirect($pay_url);
                            //$this->Session->setFlash(sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment link.'),$this->request->data['Invoice']['customer_email']), 'default', array('class' => 'success'));
                        }
					   	elseif($this->request->data['Invoice']['payment_method'] == 'Bank Transfer') {
                            /*$success_message = sprintf(__('Invoice data saved, you received an email to <b>%s</b> with the payment instructions.'), $this->request->data['Invoice']['customer_email']);
                            $this->Session->setFlash($success_message, 'default', array('class' => 'success'));*/
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

			$this->set(compact('countries'));

			$zones = array();

			if(!empty($this->request->data['Invoice']['customer_country_id']))
				$zones = $this->CountryTools->select_format_zones($this->request->data['Invoice']['customer_country_id']);

			$this->set(compact('zones'));
			$zone_id = !empty($invoice['Invoice']['customer_zone_id']) ? $invoice['Invoice']['customer_zone_id'] : 0;
            $this->set(compact('zone_id', $zone_id));
			//Devman Extensions - info@devmanextensions.com - 2017-09-09 17:39:59 - Need apply tax??
				$is_eu = !empty($this->request->data['Invoice']['customer_country_id']) && $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
				$is_spain = $invoice['Invoice']['customer_country_id'] == 195;
				$apply_tax = $is_spain || ($is_eu && empty($this->request->data['Invoice']['customer_vat']));

				$this->set(compact('apply_tax', 'is_eu'));
			//END

			//$tax = $this->request->data['Invoice']['tax'] > 0 ? ($this->request->data['Invoice']['tax']/100 + 1) : 0;
			$this->request->data['paypal_fee'] = $this->paypal_fee;
			$this->request->data['stripe_fee'] = $this->stripe_fee;
			$this->request->data['eur_currency_value'] = $this->request->data['Invoice']['currency_euro_value'];
			$this->request->data['dollar_currency_value'] = Configure::read('dollar_currency_value');
		}

		public function validate()
		{
		    $this->set('noindex', true);
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
	}
?>
