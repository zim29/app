<?php
	//Devman Extensions - info@devmanextensions.com - 2017-01-20 16:33:18 - Excel library
	    require_once VENDORS . 'Spout/Autoloader/autoload.php';
	    use Box\Spout\Reader\ReaderFactory;
	    use Box\Spout\Writer\WriterFactory;
	    use Box\Spout\Common\Type;
	    use Box\Spout\Writer\Style\StyleBuilder;
	    use Box\Spout\Writer\Style\Color;
	    use Box\Spout\Writer\Style\Border;
	    use Box\Spout\Writer\Style\BorderBuilder;

	    require_once VENDORS . 'Redsys/RedsysAPI.php';

	    require_once VENDORS . 'Redsys/RedsysAPI.php';
	    //require_once VENDORS . 'stripe/autoload.php';
        require_once VENDORS . 'stripe-php-6.38.0/init.php';
	//END

	class InvoicesController extends InvoicesAppController
	{
		public  $uses = array(
			'Invoices.Invoice',
            'Country',
            'Extensions.Extension',
            'Sales.Coupon',
			'Sales.Sale'
		);

		public $components = array('Invoices.InvoicesOpencart','OpencartExtension','CountryTools', 'Mpdf', 'ApiLicenses', 'Cart', 'Klaviyo');

		public function beforeFilter() {
	        $this->Auth->allow('pay_success', 'pay_invoice', 'ajax_budget_gmt');

	        $this->sandbox_mode =  false;

	        if($this->sandbox_mode)
	        {
	        	$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	        	$this->business_email = 'business_test@devmanextensions.com';
	        }
	        else
	        {
	        	$this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	        	$this->business_email = 'info@devmanextensions.com';
	        }

	        //Devman Extensions - info@devmanextensions.com - 2017-10-10 09:52:42 - TPV
	        	$this->tpv_test = false;
	        //END

	        $this->paypal_fee = Configure::read('paypal_fee');
	        $this->stripe_fee = Configure::read('stripe_fee');
	    }

	    public function test_api() {
		    $invoices = array(
		        '62cd72e6-d5a8-4d71-9f90-4dc3a16198ff',
                '62cd5f81-b17c-4e0a-ac69-4c06a16198ff',
                '62c883fe-ec6c-4173-859a-4df3a16198ff',
                '62c6d1d0-4b44-4bee-95a4-484ca16198ff',
                '62c56aa2-8a94-4482-9d50-448aa16198ff',
                '62c55e31-9ab8-4dce-b136-43eea16198ff',
                '62c543f2-93f0-4e5c-bc20-4ea9a16198ff',
                '62c2e0d8-0274-4c34-aa51-4fa0a16198ff',
                '62c2b203-79e8-4cc5-b838-46f4a16198ff',
                '62c2ab52-f268-49b3-bf6a-4ddba16198ff',
            );

		    foreach ($invoices as $id) {
		        $invoice_data = $this->Invoice->findById($id);
                $this->ApiLicenses->create_invoice($invoice_data['Invoice']);
		    }
        }


		/**
		*
		* Función Index
		*
		*/
		function index()
		{

		    /*$results = $this->Invoice->query("SELECT * FROM intranet_invoices WHERE tax > 0 AND discount > 0");
		    foreach ($results as $key => $inv) {
		        $inv_id = $inv['intranet_invoices']['id'];
		        $price = $inv['intranet_invoices']['price'];
		        $quantity = $inv['intranet_invoices']['quantity'];
		        $tax = $inv['intranet_invoices']['tax'];
		        $discount = $inv['intranet_invoices']['discount'];

		        $total = $price*$quantity;
		        $total = ($total*((100-$discount)/100)) * ((100+$tax)/100);
		        $total = number_format($total, 2);
		        $this->Invoice->query("UPDATE intranet_invoices SET total = ".$total." WHERE id = '".$inv_id."'");
		    }*/

		    $user = $this->Session->read('Auth.User');
		    $role = $user['role'];
		    if($role == 'marketing')
		        $types = array(
                    'GMT Extra service' => 'GMT Extra service',
                );
            else
                $types = array(
                    'Personal develop' => 'Personal develop',
                    'GMT Extra service' => 'GMT Extra service',
                    'License' => 'License',
                    'Renew' => 'Renew',
                    'Add domain' => 'Add domain',
                    'Opencart Sales' => 'Opencart Sales',
                    'New GMT Container' => 'New GMT Container'
                );

			$this->set('types',$types);

			$payment_methods = array(
				'Credit Card' => 'Credit Card',
				'Stripe' => 'Stripe',
				'Paypal' => 'Paypal',
				'Bank Transfer' => 'Bank Transfer',
			);

			$this->set('payment_methods',$payment_methods);

			$conditions = array();

			//Filters
				if (!array_key_exists('search', $this->request->data))
				{
					$this->request->data['search'] = $this->Session->read('invoices_filters');
					$filters = $this->Session->read('invoices_filters');
				}
				else
				{
					$this->Session->write('invoices_filters', $this->request->data['search']);
					$filters = $this->request->data['search'];
				}

				if($role == 'marketing') {
				    $filters['type'] = 'GMT Extra service';
                }
				if(!empty($filters))
				{
					foreach ($filters as $key => $value) {
						if(!empty($value))
						{
							if(!in_array($key, array('description', 'total_from', 'total_to', 'unpayed', 'payed', 'bank_transfer_waiting', 'todo', 'date_payed_from', 'date_payed_from', 'date_payed_to', 'customer_vat', 'created_from', 'created_to')))
							{
								$conditions['Invoice.'.$key] = $value;
							}
							else
							{
								switch ($key) {
									case 'description':
										$conditions['Invoice.description LIKE'] = '%'.$value.'%';
									break;

									case 'customer_email':
										$conditions['Invoice.customer_email LIKE'] = '%'.$value.'%';
									break;

									case 'total_from':
										$conditions['Invoice.total >='] = $value;
									break;

									case 'total_to':
										$conditions['Invoice.total <='] = $value;
									break;

									case 'unpayed':
										$conditions['Invoice.solved'] = true;
										$conditions['Invoice.payed_date'] = '0000-00-00 00:00:00';
									break;

									case 'payed':
										$conditions['Invoice.payed_date !='] = '0000-00-00 00:00:00';
									break;

									case 'bank_transfer_waiting':
										$conditions['Invoice.payment_method'] = 'Bank Transfer';
										$conditions['Invoice.pdf_send_date'] = '0000-00-00 00:00:00';
										$conditions['Invoice.payed_date !='] = '0000-00-00 00:00:00';
									break;

									case 'todo':
										$conditions['Invoice.solved'] = false;
										$conditions['Invoice.payed_date !='] = '0000-00-00 00:00:00';
									break;

									case 'date_payed_from':
										$conditions['Invoice.payed_date >='] = $value.' 00:00:00';
									break;

									case 'date_payed_to':
										$conditions['Invoice.payed_date <='] = $value.' 23:59:59';
									break;

									case 'created_from':
										$conditions['Invoice.created >='] = $value.' 00:00:00';
										$filtering_created_from = $value.' 00:00:00';
									break;

									case 'created_to':
										$conditions['Invoice.created <='] = $value.' 23:59:59';
									break;

									default:
										die('Filter '.$key.' not processed');
									break;
								}
							}
						}
					}
				}
			//END

			if(!isset($filtering_created_from) || (isset($filtering_created_from) && strtotime($filtering_created_from) < strtotime('2017-09-04 23:59:59')))
				$conditions['Invoice.created >='] = '2017-09-04 23:59:59';


			//Devman Extensions - info@devmanextensions.com - 2017-09-30 12:00:16 - Calculate total
                $conditions_total = $conditions;
                $conditions_total['Invoice.state'] = 'payed';
				$total = $this->Invoice->find('first', array('fields' => array('ROUND(sum(Invoice.total*Invoice.currency_euro_value),2) as total'), 'conditions' => $conditions_total));
				$currency_eur = Configure::read('eur_currency_value');
				$total = !empty($total[0]['total']) ? $total[0]['total'] : 0;
				$this->set('total', $total);
			//END

			$this->paginate = array(
				'limit' => 100,
				'order' => 'Invoice.created DESC',
				'conditions' => $conditions
			);

			$data = $this->paginate('Invoice');

			$this->set("invoices", $data);

			$buttons = array(
				array(
				'type' => 'create'
				)
			);

			$this->set('buttons', $buttons);
		}
		function edit($id = null)
		{
			if ($id && !$this->request->is(array('post','put')))
				$this->data = $this->Invoice->findById($id);

			$this->request->data['eur_currency_value'] = !empty($this->data) ? $this->data['Invoice']['currency_euro_value'] : Configure::read('eur_currency_value');

			if($this->request->is(array('post','put')) && $this->validate())
    		{
    			//Devman Extensions - info@devmanextensions.com - 2017-09-11 13:14:06 - Round 2 decimals
    			$this->request->data['Invoice']['total'] = round($this->request->data['Invoice']['total'], 2);
    			$this->request->data['Invoice']['price'] = round($this->request->data['Invoice']['price'], 2);
    			$this->request->data['Invoice']['customer_country'] = $this->CountryTools->get_country_name($this->request->data['Invoice']['customer_country_id']);
				$this->request->data['Invoice']['customer_zone'] = $this->CountryTools->get_zone_name($this->request->data['Invoice']['customer_zone_id']);

				$is_eu = $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
				if($is_eu && !empty($this->request->data['Invoice']['customer_vat']) && !$this->CountryTools->validate_vat($this->request->data['Invoice']['customer_country_id'], $this->request->data['Invoice']['customer_vat']))
				{
					$this->Session->setFlash('Error: Insert a valid VAT number', 'default', array('class' => 'error'));
				}
				else
				{
				    if(!empty($this->request->data['Invoice']['customer_email']))
				        $this->request->data['Invoice']['customer_email'] = trim($this->request->data['Invoice']['customer_email']);
					if(!$this->Invoice->save($this->request->data))
					{
						$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
					}
					else
					{
					    $message = 'Invoice edited!';
						if(empty($this->request->data['Invoice']['id'])) {
                            $this->request->data['Invoice']['id'] = $this->Invoice->getLastInsertId();
                            $message = 'Invoice created!';
                        }
						$this->Session->setFlash( $message, 'default', array('class' => 'success'));
					}
				}
    		}

    		//Devman Extensions - info@devmanextensions.com - 2017-09-11 12:46:04 - Send datas to view
    			//Devman Extensions - info@devmanextensions.com - 2017-09-09 17:39:59 - Need apply tax??
					$is_eu = !empty($this->request->data['Invoice']['customer_country_id']) && $this->CountryTools->is_eu($this->request->data['Invoice']['customer_country_id']);
					$apply_tax = $is_eu && empty($this->request->data['Invoice']['customer_vat']);

					$this->set(compact('apply_tax', 'is_eu'));
				//END
	    		$types = array(
	    			'Personal develop' => 'Personal develop',
	    			'GMT Extra service' => 'GMT Extra service',
	    			'Opencart Sales' => 'Opencart Sales',
	    			'License' => 'License',
	    			'Renew' => 'Renew',
	    			'Add domain' => 'Add domain',
	    			'New GMT Container' => 'New GMT Container'
	    		);

	    		$this->set(compact('types'));

	    		$countries = $this->CountryTools->select_format_countries();

				$this->set(compact('countries'));

				$zones = array();

				if(!empty($this->request->data['Invoice']['customer_country_id']))
					$zones = $this->CountryTools->select_format_zones($this->request->data['Invoice']['customer_country_id']);

				$this->set(compact('zones'));

				$statuses = array(
					'Pending' => 'Pending',
					'Payed' => 'Payed',
				);

				$this->set(compact('statuses'));

				$systems = array(
					'Opencart' => 'Opencart',
                    'Prestashop'  => 'Prestashop',
                    'cs-cart'  => 'CS-Cart',
                    'woo'  => 'woo',
				);

				$this->set(compact('systems'));

				$payment_methods = array(
					'Credit Card' => 'Credit Card',
					'Stripe' => 'Stripe',
					'Paypal' => 'Paypal',
					'Bank Transfer' => 'Bank Transfer',
				);

				$this->set(compact('payment_methods'));

				$this->request->data['paypal_fee'] = $this->paypal_fee;
				$this->request->data['stripe_fee'] = $this->stripe_fee;
			//END
		}

		public function clone_invoice($id = null)
		{
			$invoice = $this->Invoice->findById($id);

			//Remove datas
			$datas_to_remove = array('id', 'license_id', 'state', 'number', 'paypal_id_transaction', 'tpv_id_transaction', 'stripe_id_transaction', 'solved', 'payed_date', 'pdf_send_date', 'solved_date', 'created', 'modified');

			foreach ($datas_to_remove as $key => $to_detele) {
				unset($invoice['Invoice'][$to_detele]);
			}

			if($this->Invoice->saveAll($invoice))
				$this->Session->setFlash( 'Invoice cloned!', 'default', array('class' => 'success'));
			else
				$this->Session->setFlash( 'Problem clonning invoice!', 'default', array('class' => 'error'));

			$this->redirect($this->referer());
		}

		function validate()
		{
			return true;
		}

		function payed($id)
		{
			$arra_return = array('error' => false, 'message' => '');
			$invoice_temp = $this->Invoice->findById($id);

			if (empty($invoice_temp['Invoice']) || !isset($invoice_temp['Invoice']))
			{
				$arra_return['error'] = true;
				$arra_return['message'] = 'Invoice not found';
			}
			else
			{
				if(in_array($invoice_temp['Invoice']['system'], array('Opencart','cs-cart','woo')))
				{
					try {
						$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
						$ds->begin($this);
				    	$this->InvoicesOpencart->process_invoice($invoice_temp, true);
				    	$ds->commit();
				    	$arra_return['data']['class'] = 'fa fa-check-square yes';
					} catch (Exception $e) {
						$ds->rollback();
						$arra_return['error'] = true;
						$arra_return['message'] = $e->getMessage();
					}
				}
				else
				{
					$arra_return['error'] = true;
					$arra_return['message'] = 'Invoice system not found';
				}
			}

			echo json_encode($arra_return); die;
		}

		function nosolve($id)
		{
			$arra_return = array('error' => false, 'message' => '');
			$invoice_temp = $this->Invoice->findById($id);

			if (empty($invoice_temp['Invoice']) || !isset($invoice_temp['Invoice']))
			{
				$arra_return['error'] = true;
				$arra_return['message'] = 'Invoice not found';
			}
			else
			{
				try {
					$temp = array(
		        		'Invoice.solved_date' => '"'.date('Y-m-d H:i:s').'"',
		        		'Invoice.solved' => 1
		        	);

		        	if(!$this->Invoice->updateAll($temp, array('Invoice.id' => $invoice_temp['Invoice']['id'])))
						throw new Exception($e->getMessage());

					$arra_return['data']['class'] = 'fa fa-check-square yes';
				} catch (Exception $e) {
					$arra_return['error'] = true;
					$arra_return['message'] = $e->getMessage();
				}
			}

			echo json_encode($arra_return); die;
		}

		function solve($id)
		{
			$arra_return = array('error' => false, 'message' => '');
			$invoice_temp = $this->Invoice->findById($id);

			if (empty($invoice_temp['Invoice']) || !isset($invoice_temp['Invoice']))
			{
				$arra_return['error'] = true;
				$arra_return['message'] = 'Invoice not found';
			}
			else
			{
				try {
					$temp = array(
		        		'Invoice.solved_date' => '"0000-00-00 00:00:00"',
		        		'Invoice.solved' => 0
		        	);

		        	if(!$this->Invoice->updateAll($temp, array('Invoice.id' => $invoice_temp['Invoice']['id'])))
						throw new Exception($e->getMessage());

					$arra_return['data']['class'] = 'fa fa-minus-square no';
				} catch (Exception $e) {
					$arra_return['error'] = true;
					$arra_return['message'] = $e->getMessage();
				}
			}

			echo json_encode($arra_return); die;
		}

		function pdf_download($invoice_id) {
		    $this->InvoicesOpencart->pdf_download($invoice_id);
        }

		function pdf_send($invoice_id)
		{
			$arra_return = array('error' => false, 'message' => '');
			$path_pdf = $this->InvoicesOpencart->pdf_download($invoice_id, true);

			if(!empty($path_pdf))
			{
				try {
					$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
					$ds->begin($this);
					$invoice = $this->Invoice->findById($invoice_id);
			    	$this->InvoicesOpencart->send_invoice_pdf($invoice, $path_pdf);
			    	$ds->commit();
			    	$arra_return['data']['class'] = 'fa fa-paper-plane yes';
				} catch (Exception $e) {
					$ds->rollback();
					$arra_return['error'] = true;
					$arra_return['message'] = $e->getMessage();
				}
			}
			else
			{
				$arra_return['error'] = true;
				$arra_return['message'] = 'Error saving pdf';
			}

			echo json_encode($arra_return); die;
		}

		function pdf_resend($invoice_id)
		{
			$this->pdf_send($invoice_id);
		}



		function generate_excel()
		{
			$this->layout = 'ajax';

			$date_start = date('Y-m-01 00:00:00', strtotime($this->request->data['date'].'-01'));
			$date_end = date('Y-m-31 23:59:59', strtotime($this->request->data['date'].'-01'));
			$conditions = array(
				'Invoice.state' => 'Payed',
				'Invoice.payed_date >=' => $date_start,
				'Invoice.payed_date <=' => $date_end,
				'Invoice.created >=' => '2017-09-04 23:59:59'
			);

			$invoices = $this->Invoice->find('all', array('conditions' => $conditions, 'order' => array('Invoice.number ASC')));
			$final_facturas = array();

			foreach ($invoices as $key => $inv) {

			    //Fee calc
                    $pay_method = $inv['Invoice']['payment_method'];
                    $fee = $pay_method == 'Paypal' ? 5.5 : ($pay_method == 'Stripe' ? 3 : false);
                    if($fee && $inv['Invoice']['type'] != 'Opencart Sales')
                        $inv['Invoice']['total'] = ((100 - $fee) * $inv['Invoice']['total']) / 100;
			    //END Fee calc
				$final_facturas[] = array(
					sprintf('%05d', $inv['Invoice']['number']),
					date('d/m/Y H:i:s', strtotime($inv['Invoice']['payed_date'])),
					$inv['Invoice']['description'],
					$inv['Invoice']['customer_name'],
					$inv['Invoice']['customer_vat'],
					$inv['Invoice']['customer_post_code'],
					$inv['Invoice']['customer_country'],
					$inv['Invoice']['payment_method'],
					$inv['Invoice']['payment_method'] == 'Paypal' ? $inv['Invoice']['paypal_id_transaction'] : ($inv['Invoice']['payment_method'] == 'Credit Card' ?  $inv['Invoice']['tpv_id_transaction'] : ($inv['Invoice']['payment_method'] == 'Stripe' ?  $inv['Invoice']['stripe_id_transaction'] : '')),
					$inv['Invoice']['tax'],
					(round($inv['Invoice']['total']*$inv['Invoice']['currency_euro_value'],2))
				);
			}

			$writer = WriterFactory::create(Type::XLSX);

            $filename = $this->request->data['date'].' - Facturas';

            $filePath = APP.WEBROOT_DIR.DS.'files'.DS.'facturacion'.DS.$filename.'.xlsx';
            $downloadPath = Router::url('/', true).'files/facturacion/'.$filename.'.xlsx';
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToFile($filePath);

            $firstSheet = $writer->getCurrentSheet();
            $firstSheet->setName('Facturación');

            //Devman Extensions - info@devmanextensions.com - 2017-01-20 15:49:04 - Insert columns
                $border = (new BorderBuilder())
                ->setBorderTop('000000', Border::WIDTH_THIN)
                ->setBorderBottom('000000', Border::WIDTH_THIN)
                ->setBorderLeft('000000', Border::WIDTH_THIN)
                ->setBorderRight('000000', Border::WIDTH_THIN)
                ->build();
                $style = (new StyleBuilder())
                        ->setBorder($border)
                        ->setFontBold()
                        ->setFontSize(11)
                        ->setFontColor('ffffff')
                        ->setShouldWrapText(false)
                        ->setBackgroundColor('55acee')
                        ->build();

                $columns_names = array(
                	'Num Factura',
                	'Fecha Factura',
                	'Descripción breve',
                	'Nombre',
                	'NIF/CIF',
                	'Cod. Postal',
                	'País',
                	'Forma pago',
                	'Transacción ID',
                	'IVA',
                	'Total'
                );

                $writer->addRowWithStyle($columns_names, $style);
            //END

            //Devman Extensions - info@devmanextensions.com - 2017-09-16 17:14:56 -  Insert datas
                $border = (new BorderBuilder())->build();

                $style = (new StyleBuilder())
                    ->setBorder($border)
                    ->setShouldWrapText(false)
                    ->build();

                $writer->addRowsWithStyle($final_facturas, $style);
            //END

            $writer->close();
            $this->redirect($downloadPath);
		}
		function pay_invoice($id)
		{
		    $this->Session->write('invoice_id', $id);

			$this->layout = 'frontend';
			$invoice = $this->Invoice->findById($id);

			if(empty($invoice))
			{
				$this->Session->setFlash('Invoice not found', 'default', array('class' => 'error'));
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
			}
			elseif($invoice['Invoice']['state'] == 'Payed')
			{
				$message = 'Invoiced payed in '.date('d/m/Y H:m:i', strtotime($invoice['Invoice']['payed_date']));
				$this->Session->setFlash($message, 'default', array('class' => 'error'));
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
			}

			//Devman Extensions - info@devmanextensions.com - 2017-10-10 10:13:41 - Paypal
				if ($invoice['Invoice']['payment_method'] == 'Paypal') {
					$this->set("sandbox_mode", $this->sandbox_mode);
					$this->set("paypal_url",$this->paypal_url);
					$this->set("business_email",$this->business_email);
				}
			//END
			//Devman Extensions - info@devmanextensions.com - 2017-10-10 09:48:49 - TPV
				elseif ($invoice['Invoice']['payment_method'] == 'Credit Card') {
					$action = $this->tpv_test ? "https://sis-t.redsys.es:25443/sis/realizarPago" : "https://sis.redsys.es/sis/realizarPago";
					$this->set('action', $action);
			        $clave = $this->tpv_test ? 'sq7HjrUOBfKmC576ILgskD5srU870gJ7' : 'swlc3cD4nVupK7M6OcgOl5BfYVM2xesj';

			       //Check signature
			       if (!class_exists("RedsysAPI")) {
			           die("RedsysAPI class doesn't exists");
			       }

			       $redsys = new RedsysAPI;
			       $total = ($invoice['Invoice']['total'] * $invoice['Invoice']['currency_euro_value']);
			       $total = number_format(round($total, 2),2);
			       $total = str_replace('.', '', $total);
			       $total = str_replace(',', '', $total);

			       $redsys->setParameter("DS_MERCHANT_AMOUNT", $total);
			       $redsys->setParameter("DS_MERCHANT_ORDER", sprintf("%012s", time()));
			       $redsys->setParameter("DS_MERCHANT_MERCHANTCODE", 164191298);
			       $redsys->setParameter("DS_MERCHANT_CURRENCY", 978);
			       $redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", 0);
			       $redsys->setParameter("DS_MERCHANT_TERMINAL", '100');
			       $redsys->setParameter("Ds_Merchant_ConsumerLanguage", '002');
			       $redsys->setParameter("Ds_Merchant_ProductDescription", $invoice['Invoice']['description']);
			       $redsys->setParameter("Ds_Merchant_Titular", $invoice['Invoice']['id'] . ' - ' . $invoice['Invoice']['customer_name'].(!empty($invoice['Invoice']['customer_vat']) ? ' - '.$invoice['Invoice']['customer_vat'] : ''));
			       $redsys->setParameter("Ds_Merchant_MerchantData", $invoice['Invoice']['id']);
			       $redsys->setParameter("Ds_Merchant_PayMethods", 'T');
			       $redsys->setParameter("Ds_Merchant_Module", 'Devman Extensions');
			       $redsys->setParameter("DS_MERCHANT_MERCHANTURL", "https://devmanextensions.com/invoices/tpv/callback");
			       $redsys->setParameter("DS_MERCHANT_URLOK", "https://devmanextensions.com/invoices/invoices/pay_success");

			       //Datos de configuración
			       $this->set('version', "HMAC_SHA256_V1");
			       $this->set('paramsBase64', $redsys->createMerchantParameters());
			       $this->set('signatureMac', $redsys->createMerchantSignature($clave));
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-11-15 13:57:08 - Stripe
				elseif ($invoice['Invoice']['payment_method'] == 'Stripe') {
			        //die("MAINTENANCE MODE IN STRIPE PAYMENT METHOD");
					$stripe_api_public = Configure::read('stripe_api_public');
					$stripe_api_private = Configure::read('stripe_api_private');
					$stripe_callback = Router::url("/", true).Configure::read('stripe_callback');

					/* OLD VERSION
					$stripe = array(
					  "secret_key"      => $stripe_api_private,
					  "publishable_key" => $stripe_api_public
					);

					\Stripe\Stripe::setApiKey($stripe['secret_key']);
					$this->set('publishable_key', $stripe['publishable_key']);
					$this->set('stripe_callback', $stripe_callback);
					*/

					$data_to_view = array();
					$data_to_view['callback_url'] = $stripe_callback;
					$data_to_view['text_credit_card'] = 'text_credit_card';
                    $data_to_view['button_confirm'] = 'button_confirm';
                    $data_to_view['button_back'] = 'button_back';
                    $data_to_view['text_enter_card_detail'] = 'Enter card details';
                    $data_to_view['text_or_enter_card_detail'] = 'Or enter card details';
                    $data_to_view['text_submit_payment'] = 'Submit payment';

                    $data_to_view['payment_stripepro_public_key'] = $stripe_api_public;
                    $country = $this->Country->findById($invoice['Invoice']['customer_country_id']);

                    $data_to_view['billing'] = array(
                        'email'             => $invoice['Invoice']['customer_email'],
                        'name'              => $invoice['Invoice']['customer_name'],
                        'address' => array(
                            'line1'	        => $invoice['Invoice']['customer_address'],
                            'line2'	        => '',
                            'city'	        => $invoice['Invoice']['customer_city'],
                            'state'	        => $invoice['Invoice']['customer_zone'],
                            'postal_code'   => $invoice['Invoice']['customer_post_code'],
                            'country'       => $country['Country']['iso_code_2']
                        )
                    );
                    $amount = (int)($invoice['Invoice']['total']*$invoice['Invoice']['currency_euro_value'] * 100);
                    $data_to_view['amount']     = $amount;
                    $data_to_view['currency']   = 'EUR';

                    $data_to_view['order_id']   = $invoice['Invoice']['id'];
                    $data_to_view['firstname']  = $invoice['Invoice']['customer_name'];
                    $data_to_view['email']      = $invoice['Invoice']['customer_email'];


                    \Stripe\Stripe::setApiKey($stripe_api_private);


                    $intent = \Stripe\PaymentIntent::create([
                      "amount"        => $amount,
                      "currency"      => 'EUR',
                      "payment_method_types" => ["card"],
                      'description' => $invoice['Invoice']['description'],
                      "metadata" => [
                          "order_id"  => $invoice['Invoice']['id'],
                          "firstname" => $invoice['Invoice']['customer_name'],
                          "email"     => $invoice['Invoice']['customer_email'],
                      ],
                  ]);

                  $data_to_view['intent'] = $intent;

                  foreach ($data_to_view as $key_var => $value) {
                      $this->set($key_var, $value);
                  }

				}
			//END
			$this->set("invoice", $invoice);
		}

		function pay_success()
		{
		    $this->set('cart_count', $this->Cart->count_products());

		    $invoice_id = $this->Session->read('invoice_id');
			//$invoice_id = '5b7fd811-5dec-4658-8347-1125d93d802a';

		    if(!empty($invoice_id)) {
                $this->Invoice->id = $invoice_id;
                $invoice_selected = $this->Invoice->read();

				$countryModel = ClassRegistry::init('Country');
				$zoneModel = ClassRegistry::init('Zone');

				$countryModel->recursive = -1;
				$country = $countryModel->findById($invoice_selected['Invoice']['customer_country_id']);
				$invoice_selected['Invoice']['customer_country_code'] = $country['Country']['iso_code_3'];

				$zoneModel->recursive = -1;
				$zone = $zoneModel->findById($invoice_selected['Invoice']['customer_zone_id']);
				$invoice_selected['Invoice']['customer_zone_code'] = $zone['Zone']['code'];

                if (!empty($invoice_selected)) {
                    $order_data = array(
						"order_id" => $invoice_id,
						"event_id" => $this->generate_uuid(),
                        "type" => $invoice_selected["Invoice"]['type'],
                        "system" => $invoice_selected["Invoice"]['system'],
                        "payment_method" => $invoice_selected["Invoice"]['payment_method'],
                        "total" => $invoice_selected["Invoice"]['type'] == 'License' ? $this->Cart->get_total() : $invoice_selected["Invoice"]['total'],
                        "customer_name" => $invoice_selected["Invoice"]['customer_name'],
                        "customer_email" => $invoice_selected["Invoice"]['customer_email'],
						"customer_address" => $invoice_selected["Invoice"]["customer_address"],
						"customer_country" => $invoice_selected["Invoice"]["customer_country"],
						"customer_country_code" => $invoice_selected["Invoice"]["customer_country_code"],
						"customer_zone" => $invoice_selected["Invoice"]["customer_zone"],
						"customer_zone_code" => $invoice_selected["Invoice"]["customer_zone_code"],
						"customer_city" => $invoice_selected["Invoice"]['customer_city'],
						"customer_post_code" => $invoice_selected["Invoice"]['customer_post_code'],
                    );

                    if ($invoice_selected["Invoice"]['type'] == 'License') {
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
                        $order_data['products'] = $products;
                    }

					if ($invoice_selected["Invoice"]['type'] == 'Renew') {
						$this->Sale->recursive = 2;
						$license_obj = $this->Sale->find('first', array('conditions' => array('Sale.order_id' => $invoice_selected["Invoice"]['license_id'])));
						$this->Extension->recursive = -1;
						$products = array();

						$conditions = array();
						$conditions['OR'] = array(
							array('Extension.id' => $license_obj['Sale']['extension_id']),
							array('Extension.oc_extension_id' => $license_obj['Sale']['extension_id']),
						);
						$extension = $this->Sale->find('first', array('conditions' => $conditions));
						$extension_data = $this->Extension->formatExtensionToDatalayer($extension);
						$extension_data['price'] = $invoice_selected["Invoice"]['price'];
						$extension_data['tax'] = $invoice_selected["Invoice"]['tax'];
						$extension_data['discount'] = $invoice_selected["Invoice"]['discount'];
						$extension_data['total'] = $invoice_selected["Invoice"]['total'];
						$extension_data['quantity'] = $invoice_selected["Invoice"]['quantity'];
						$products[] = $extension_data;
						$order_data['products'] = $products;
					}

                    $datalayer = '
                        <script>
                            dataLayer.push({
                                "event": "purchase",
                                "orderData": ' . json_encode($order_data) . '
                            });
                        </script>
                    ';

                    $this->set("datalayer", $datalayer);

					//Kalviyo Integration
					$this->Klaviyo->register_purchase($order_data);

					//Clear cart and invoice id from session.
					$this->Session->write('invoice_id', '');
					$this->Cart->clear();
                }
            }


			$this->layout = 'frontend';
		}

		function ajax_budget_gmt() {

		    $budget_information = '';

		    $budget_information .= 'Shop URL: '.$this->request->data['questions']['initial']['url']."\n";
		    $budget_information .= 'Products: '.$this->request->data['questions']['initial']['products']."\n";
		    $budget_information .= 'Email: '.$this->request->data['questions']['initial']['email']."\n";
		    $budget_information .= 'Customer name: '.$this->request->data['questions']['initial']['name']."\n";

		    if(!empty($this->request->data['questions']['initial']['license_id']))
		        $budget_information .= 'License ID: '.$this->request->data['questions']['initial']['license_id']."\n";

		    $budget_information .= "\n".'Services: '."\n";
		    $count = 1;
		    foreach ($this->request->data['services'] as $key => $service) {
		        $budget_information .= $count.'.- '.$service."\n";
		        $count++;
		    }

		    $invoice = array(
                'Invoice' => array(
                    'type' => 'GMT Extra service',
                    'license_id' => $this->request->data['license_id'],
                    'state' => 'Pending',
                    'description' => 'GMT Extra services ('.count($this->request->data['services']).') - '.$this->request->data['questions']['initial']['url'],
                    'description_avanced' => $budget_information,
                    'price' => $this->request->data['budget'],
                    'total' => $this->request->data['budget'],
                    'customer_name' => $this->request->data['questions']['initial']['name'],
                    'customer_email' => trim($this->request->data['questions']['initial']['email']),
                    'currency_euro_value' => Configure::read('eur_currency_value'),
                    'currency' => 'USD',
                    'payment_method' => 'Credit Card',
                )
            );

		    $arra_return = array('error' => false, 'message' => __('We received your budget request, we will put in contact with you soon as possible. Thanks!'));
		    try {
		        $this->Invoice->saveAll($invoice);
		        $this->InvoicesOpencart->send_emails_gmt_budget($invoice);
            } catch (Exception $e) {
                $arra_return['error'] = true;
                $arra_return['message'] = __('Error saving your request, try again later.');
                $arra_return['message_technical'] = $e->getMessage();
            }

            echo json_encode($arra_return); die;
        }

		function delete($id) {
			$this->Invoice->id = $id;
			$invoice_selected = $this->Invoice->read();
			if($invoice_selected['Invoice']['payed_date'] != '0000-00-00 00:00:00')
			{
				$this->Session->setFlash(
					    '<i class="fa fa-thumbs-down"></i>Error deleting invoice, is payed',
					    'default',
					    array('class' => 'error')
					);
			}
			else
			{
				if (empty($invoice_selected['Invoice']) || !isset($invoice_selected['Invoice'])) {
					$this->Session->setFlash(
					    '<i class="fa fa-thumbs-down"></i>Invoice not found',
					    'default',
					    array('class' => 'error')
					);
				} else {

					$temp = array(
		        		'Invoice.deleted' => 1,
		        	);

		        	if(!$this->Invoice->updateAll($temp, array('Invoice.id' => $invoice_selected['Invoice']['id'])))
		        		$this->Session->setFlash(
						    '<i class="fa fa-thumbs-down"></i>Error deleting invoice',
						    'default',
						    array('class' => 'error')
						);
		        	else
		        		$this->Session->setFlash(
						    '<i class="fa fa-thumbs-up"></i>Invoice deleted successfully!',
						    'default'
						);
				}
			}
			$this->redirect(array('action'=>'index'));
		}
	}
?>
