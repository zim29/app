<?php
	class ApiController extends AppController {

	    public $components = array('Mailchimp','Invoices.InvoicesOpencart', 'ApiLicenses');

		public function beforeFilter() {
	        $this->Auth->allow('new_sale', 'invoice_paid', 'valid_extension_version', 'insert_extension_version');
	    }

	    public  $uses = array(
      		'Sales.Sale',
            'Sales.Coupon',
            'Invoices.Invoice',
			'Changelogs.Changelog',
			'Extensions.Extension'
		);

		function invoice_paid() {
		    $array_return = array(
                'error' => false,
                'message' => 'Invoice Processed'
            );

		    if(!empty($_POST)) {
		        $secret = !empty($_POST['secret']) &&  $_POST['secret'] == 'M3Hho9Ep6v';
		        $invoice_id = !empty($_POST['ref']) ? $_POST['ref'] : '';

		        if($secret && !empty($invoice_id)) {
		            $invoice = $this->Invoice->findById($invoice_id);


                    //TODO
		            $country = !empty($_POST['country']) ? $_POST['country'] : '';
                    $region = !empty($_POST['region']) ? $_POST['region'] : '';

                    $invoice['Invoice']['customer_country'] = '';
                    $invoice['Invoice']['customer_country_id'] = '';
                    $invoice['Invoice']['customer_zone'] = '';
                    $invoice['Invoice']['customer_zone_id'] = '';
                    $invoice['Invoice']['customer_phone'] = '';
                    $invoice['Invoice']['price'] = '';
                    $invoice['Invoice']['paypal_id_transaction'] = '';
                    $invoice['Invoice']['stripe_id_transaction'] = '';
                    $invoice['Invoice']['customer_name'] = !empty($_POST['name']) ? $_POST['name'] : '';
                    $invoice['Invoice']['customer_city'] = !empty($_POST['city']) ? $_POST['city'] : '';
                    $invoice['Invoice']['customer_address'] = !empty($_POST['address']) ? $_POST['address'] : '';
                    $invoice['Invoice']['customer_post_code'] = !empty($_POST['postal']) ? $_POST['postal'] : '';
                    $invoice['Invoice']['customer_vat'] = !empty($_POST['vat']) ? $_POST['vat'] : '';
                    $invoice['Invoice']['currency_euro_value'] = !empty($_POST['exchange']) ? $_POST['exchange'] : '';
                    $invoice['Invoice']['tax'] = !empty($_POST['tax']) ? $_POST['tax'] : '';
                    $invoice['Invoice']['total'] = !empty($_POST['total']) ? $_POST['total'] : '';

                    try {
                        $ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
                        $ds->begin($this);
                        $this->Invoice->saveAll($invoice);
                        $this->InvoicesOpencart->process_invoice($invoice);
                        $ds->commit();
                    } catch (Exception $e) {
                        $this->send_email_error('Error during update invoice', array('Error message' => $e->getMessage()));
                        $array_return['error'] = true;
                        $array_return['message'] = __('Error during update invoice.');
                        $ds->rollback();
                    }
                    echo json_encode($array_return); die;
                } else {
		            if(empty($invoice_id))
		                $array_return = array(
                            'error' => true,
                            'message' => 'Empty ref'
                        );
		            else
                        $array_return = array(
                            'error' => true,
                            'message' => 'Empty or wrong security'
                        );
                }
            } else {
		        $array_return = array(
                    'error' => true,
                    'message' => 'Empty POST data'
                );
            }

            echo json_encode($array_return); die;
        }

		function new_sale()
		{

			/*$test = true;
			$_POST['order_id'] = 1;
			$_POST['extension_id'] = 15609;
			$_POST['member_id'] = 3;
			$_POST['username'] = 'David';
			$_POST['email'] = 'davidddd@gmail.com';
			$_POST['extension'] = 'Google marketing tools';
			$_POST['quantity'] = 1;
			$_POST['sub_total'] = 39.99;
			$_POST['commission'] = 25;
			$_POST['total'] = 39.99;
			$_POST['order_status'] = 'Complete';
			$_POST['date_added'] = date('Y-m-d H:i:s');
			$_POST['date_modified'] = date('Y-m-d H:i:s');*/


			/*App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
            $Email->from(array('info@devmanextensions.com' => 'info@devmanextensions.com'));
            $Email->to('info@devmanextensions.com');
            $Email->emailFormat('html');
            $Email->template('ticket_email');
            $Email->subject('PURCHASE TRIED');
            $Email->send(json_encode(!empty($_POST) ? json_encode($_POST) : 'NO POST'));*/
            //IMPORTANTE - DESCOMENTAR LÍNEA 100!!!!!!!!!!!!!!!!!!!!

			if($_SERVER['REQUEST_METHOD']=='POST' || !empty($test)){
				$order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : ''; // the opencart order id
				$extension_id = !empty($_POST['extension_id']) ? $_POST['extension_id'] : ''; // the extension id
				$member_id = !empty($_POST['member_id']) ? $_POST['member_id'] : ''; // buyer's member id
				$username = !empty($_POST['username']) ? $_POST['username'] : ''; // buyer's username
				$email = !empty($_POST['email']) ? $_POST['email'] : ''; // buyer's email address
				$extension = !empty($_POST['extension']) ? $_POST['extension'] : ''; // name of the extension
				$quantity = !empty($_POST['quantity']) ? $_POST['quantity'] : ''; // quantity of extension license purchased
				$sub_total = !empty($_POST['sub_total']) ? $_POST['sub_total'] : ''; // sell price of extension * quantity
				$commission = !empty($_POST['commission']) ? (float)$_POST['commission']*100 : ''; // what percentage rate OpenCart.com take from the sale
				$total = !empty($_POST['total']) ? $_POST['total'] : ''; // what you will be paid
				$order_status = !empty($_POST['order_status']) ? trim($_POST['order_status']) : ''; // status of the order - complete, pending, etc
				//$date_added = !empty($_POST['date_added']) ? $_POST['date_added'] : ''; // date and time
				//$date_modified = !empty($_POST['date_modified']) ? $_POST['date_modified'] : ''; // date and time
				$date_added = $date_modified = date('Y-m-d H:i:s');

				$exist_sale = $this->Sale->findByOrderId($order_id);

				$create_order = empty($exist_sale);

				$continue = $order_status != 'Denied';

                $marketplace = 'Opencart';

				//ISENSELABS
				if($order_status == 'Confirmed') {
				    $order_status = 'Complete';
				    $marketplace = 'Isenselabs';
				    $commission = 100-(float)$_POST['commission'];
				    $extension = !empty($_POST['extension_name']) ? $_POST['extension_name'] : '';

				    //GMT
				    if($extension_id == 474)
				        $extension_id = 15609;

				    $total *= 0.8;
				    $order_id = 'isenselabs-'.$order_id;
				    $date_modified = $date_added;
                }

				if($continue)
				{
					$OpencartExtension = $this->Components->load('OpencartExtension');
					$client_component = $this->Components->load('Client');
					$client_component->update_count_client_image();

					$send_email = !empty($email) && in_array($order_status, array('Complete', 'complete'));
                    $send_email_admin = in_array($order_status, array('Complete', 'complete'));

					$waiting = $order_status == 'Waiting for Proof of ID';

					$temp = array(
						'Sale' => array(
							'order_id' => $order_id,
							'system' => 'Opencart',
							'marketplace' => $marketplace,
							'extension_id' => $extension_id,
							'extension_name' => $extension,
							'buyer_id' => $member_id,
							'buyer_username' => $username,
							'quantity' => $quantity,
							'sub_total' => $sub_total,
							'commission' => $commission,
							'total' => $total,
							'buyer_email' => $email,
							'order_status' => $order_status == 'complete' ? 'Complete' : $order_status,
							'date_added' => $date_added,
							'date_modified' => $date_modified
						)
					);

					App::uses('CakeEmail', 'Network/Email');

	   				if($create_order)
	   				{
	   				    /*$mailchimp_list_id = 'ddb182e8b6';
                        $mailchimp_data = array(
                            'email' => $email,
                            'merge_fields' => array(
                                'FNAME' => $username,
                                'ENAME' => $extension,
                                'EID' => $extension_id
                            )
                        );

                        $this->Mailchimp->subscribe($mailchimp_list_id, $mailchimp_data);*/

	   					$temp['Sale']['download_id'] = $this->generate_uuid();

	   					if($this->Sale->saveAll($temp))
	   					{
	   						if($send_email_admin || $waiting)
	   						{
	   							$OpencartExtension->send_emails_purchase_administrator($order_id, $waiting);
							}
	   					}

	   					if($send_email) {
	   						$OpencartExtension->send_emails_purchase_client($order_id);
	   						$this->Coupon->create_coupon($order_id, $extension_id);
						}


						//NEW API - Register sale.
						if($send_email)
	   					     $this->register_sale_new_api($order_id);
	   				}
	   				else //Update order
	   				{
	   					$this->Sale->query("DELETE FROM intranet_sales where order_id = ".$order_id);

	   					if($temp['Sale']['order_status'] == 'Complete') {
	   					    $temp['Sale']['download_id'] = $this->generate_uuid();
                        }
	   					if($this->Sale->saveAll($temp))
	   					{
	   					    if($temp['Sale']['order_status'] == 'Complete') {
                                $OpencartExtension->send_emails_purchase_client($order_id);
                                $OpencartExtension->send_emails_purchase_administrator($order_id, false);

                                //NEW API - Register sale.
                                $this->register_sale_new_api($order_id);
                            }

							$subject = 'OC Order changed - '.$extension;

							$content = '<b>Total in '.date('Y/m').': '.$this->Sale->get_sales_by_month(date('Y-m'))."</b><br><br>";

							$content .= '<b>Order id</b>: '.$order_id."<br>";
							$content .= '<b>Extension name</b>: '.$extension."<br>";
							$content .= '<b>Extension id</b>: '.$extension_id."<br>";
							$content .= '<b>Buyer id</b>: '.$member_id."<br>";
							$content .= '<b>Buyer username</b>: '.$username."<br>";
							$content .= '<b>Buyer email</b>: '.$email."<br>";
							$content .= '<b>Quantity</b>: '.$quantity."<br>";
							$content .= '<b>Sub total</b>: $'.number_format($sub_total,2)."<br>";
							$content .= '<b>Commision</b>: '.$commission."%<br>";
							$content .= '<b>Total</b>: $'.number_format($total,2)."<br>";
							$content .= '<b>Order status</b>: '.$order_status."<br>";
							$content .= '<b>Date added</b>: '.$date_added."<br>";
							$content .= '<b>Date modified</b>: '.$date_modified."<br>";
	   					}
	   					else
	   					{
	   						$subject = 'OC Error changing sale';
	   						$content = json_encode($_POST);
	   					}

	   					$Email = new CakeEmail();
						$Email->from(array($email => $username));
						$Email->to('info@devmanextensions.com');
						$Email->emailFormat('html');
						$Email->template('ticket_email');
						$Email->subject($subject);
						$Email->send($content);
	   				}
	   			} else if($order_status == 'Denied' || $order_status == 'Failed') {
					$subject = 'Problems in opencart.com? purchase extension in our official website!';
					$content = 'Remember that you can use our <a href="https://devmanextensions.com/extensions-shop">own store</a> to purchase extensions. You can choose between different payment methods and the extensions and conditions are exactly same. Thanks!';

	   				App::uses('CakeEmail', 'Network/Email');
					$Email = new CakeEmail();
					$Email->from(array('info@devmanextensions.com' => 'info@devmanextensions.com'));
					$Email->to($email);
					$Email->emailFormat('html');
					$Email->template('ticket_email');
					$Email->subject($subject);
					$Email->send($content);
	   			}
   			}

   			die("finish");
		}

		public function register_sale_new_api($order_id) {
		    $sale = $this->Sale->findByOrderId($order_id);
		    $this->ApiLicenses->create_sale($sale['Sale']);
        }

		public function valid_extension_version() {

			$token = 'vRKBx8W0BK';
			$return = array("error" => false);

			if(empty($this->request->data['token']) || $this->request->data['token'] != $token) {
				$return['error'] = "Incorrect token";
			} else {
				$extension_id = $this->request->data['extension_id'];
				$version = $this->request->data['version'];


				$this->Extension->recursive = 1;
				$extension = $this->Extension->findById($extension_id);

				if (!empty($extension['Changelog'])) {
					$current_version = $extension['Changelog'][0]['version'];
					if (version_compare($version, $current_version, "<="))
						$return['error'] = "Last available version is: " . $current_version;
				}
			}

			echo json_encode($return); die;

		}

		public function insert_extension_version() {
			$token = 'vRKBx8W0BK';
			$return = array("error" => false);


			if(empty($this->request->data['token']) || $this->request->data['token'] != $token) {
				$return['error'] = "Incorrect token";
			} else {
				$extension_id = $this->request->data['extension_id'];
				$version = $this->request->data['version'];
				$text = $this->request->data['text'];

				$changelog = array(
					'id_extension' => $extension_id,
					'version' => $version,
					'text' => $text,
					'deleted' => 0
				);

				$this->Changelog->saveAll($changelog);
			}

			echo json_encode($return); die;

		}

	}
?>
