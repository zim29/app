<?php 
	class WoocommerceController extends AppController {

		public  $uses = array(
      		'Sales.Sale',
      		'Extensions.Extension',
      		'Tickets.Ticket',
		); 

		public $components = array(
		    'OpencartExtension',
		    'Email',
		    'OpencartFormGenerator',
		    'OpencartTicket',
		);

		public function beforeFilter() {
	        $this->Auth->allow('ajax_get_form', 'add_domain', 'increase_license', 'fix_tickets', 'get_license_from_ticket_system', 'ajax_open_ticket', 'extension_download', 'ajax_get_extension_download_links', 'recover_download_id', 'get_discount');
	        $this->action_renew_license = 'increase_license_confirm';
	        $this->action_add_domain = 'add_domain_confirm';
	        $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';

		}

		function ajax_get_form()
		{
			$array_return = array('error' => false, 'message' => '');

			if(array_key_exists('force_curl_json', $this->request->data))
			{
				$str = file_get_contents($this->request->data['force_curl_json']);
				$this->request->data = json_decode($str, true);
				if(array_key_exists('form_basic_datas', $this->request->data))
					$this->request->data['form_basic_datas'] = json_encode($this->request->data['form_basic_datas']);
				if(array_key_exists('form', $this->request->data))
					$this->request->data['form'] = json_encode($this->request->data['form']);
			}

			$form_basic_datas = !empty($this->request->data['form_basic_datas']) ? json_decode($this->request->data['form_basic_datas'], true) : null;
			if(empty($form_basic_datas))
			{
				$array_return = array('error' => true, 'message' => 'Empty post data form');
				echo json_encode($array_return); die;
			}

			$demo_store = array_key_exists('ts6EpBx2', $form_basic_datas) && !empty($form_basic_datas['ts6EpBx2']);
			$license_id = $demo_store ? '111' : '';
			$domain = $demo_store ? 'https://devmanextensions.com/' : '';
            //echo '<pre>'; print_r($this->request->data);  echo '</pre>'; die;
			if(!$demo_store)
			{
			    //Checking if is registering license
                    $license_id = array_key_exists('license_id', $this->request->data) ? trim($this->request->data['license_id']) : '';
                    if(empty($license_id)) {
                        $error['error'] = 1;
                        $error['message'] = 'Fill license ID';
                        echo json_encode($error); die;
                    }

                    $sale = $this->Sale->findByOrderId($license_id);
                    //echo '<pre>'; print_r($sale);  echo '</pre>'; die;
                    if(empty($sale)) {
                        $error = array('error' => false, 'message' => '');
                        //echo '<pre>'; print_r($this->request->data);  echo '</pre>'; die;
                        $buyer_email = array_key_exists('buyer_email', $this->request->data) ? $this->request->data['buyer_email'] : '';
                        $buyer_username = array_key_exists('buyer_username', $this->request->data) ? $this->request->data['buyer_username'] : '';
                        $domain = array_key_exists('domain', $this->request->data) ? $this->get_domain($this->request->data['domain']) : '';
                        $extension_id = array_key_exists('extension_id', $this->request->data) ? $this->request->data['extension_id'] : '';

                        $extension = $this->Extension->findById($extension_id);

                        if(empty($buyer_username)) {
                            $error['error'] = 1;
                            $error['message'] = 'Fill name';
                            echo json_encode($error); die;
                        }

                        if(empty($buyer_email)) {
                            $error['error'] = 1;
                            $error['message'] = 'Fill email';
                            echo json_encode($error); die;
                        }

                        if(empty($domain)) {
                            $error['error'] = 1;
                            $error['message'] = 'Internal error 1';
                            echo json_encode($error); die;
                        }

                        if(empty($extension_id)) {
                            $error['error'] = 1;
                            $error['message'] = 'Internal error 2';
                            echo json_encode($error); die;
                        }

                        $license = array('Sale' => array(
                            'order_id' => $license_id,
                            'marketplace' => 'Codecanyon',
                            'domain' => $domain,
                            'extension_id' => $extension_id,
                            'extension_name' => $extension['Extension']['name'],
                            'extension_id' => $extension['Extension']['id'],
                            'buyer_email' => $buyer_email,
                            'buyer_username' => $buyer_username,
                            'order_status' => 'pending_validate',
                            'date_added' => date('Y-m-d H:i:s'),
                            'date_modified' => date('Y-m-d H:i:s')
                        ));


                        if(!$this->Sale->saveAll($license)) {
                            $error['error'] = 1;
                            $error['message'] = 'Error creating your license.';
                            echo json_encode($error); die;
                        } else {
                            $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Sales system', '[Woocommerce] New license pending to validate - '.$extension['Extension']['name'], '');

                            $error['success'] = 1;
                            $error['message'] = 'Your license was registered, our team will be validate it soon as possible, you will receive and email to address <b>'.$buyer_email.'</b> with the result.';
                            echo json_encode($error); die;
                        }
                    } else {
                        //Is possible that is pending
                        if($sale['Sale']['order_status'] == 'pending_validate') {
                            $error['error'] = 1;
                            $error['is_pending'] = 1;
                            $error['message'] = 'Your license is pending to validate. Our team will be validate it soon as possible, you will receive and email to address <b>'.$sale['Sale']['buyer_email'].'</b> with the result.';
                            echo json_encode($error);
                            die;
                        }
                    }
                //END

				//Devman Extensions - info@devmanextensions.com - 2017-08-26 15:37:40 - Check license and domain
					$license_id = !empty($this->request->data['license_id']) ? $this->request->data['license_id'] : '';
					$domain = !empty($this->request->data['domain']) ? $this->get_domain($this->request->data['domain']) : '';

					$license_checked = $this->OpencartExtension->check_license($license_id, $domain);

					if($license_checked['error'])
					{
						echo json_encode($license_checked); die;
					}
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-08-30 20:22:07 - Check license expired
					if(array_key_exists('expired', $license_checked) && !empty($license_checked['expired']))
					{
						$array_return['expired'] = true;
						$array_return['message'] = $license_checked['message'];
					}
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-09-21 13:09:57 - Check if license id is for this extension
					$license_checked = $this->OpencartExtension->check_license_vs_extension($license_id, $form_basic_datas['extension_id']);

					if($license_checked['error'])
					{
						echo json_encode($license_checked); die;
					}
				//END
			}

			//Devman Extensions - info@devmanextensions.com - 2017-08-29 18:48:13 - Construct form
				$form = !empty($this->request->data['form']) ? json_decode($this->request->data['form'], true) : null;

				if(!$form || !$form_basic_datas)
				{
					$array_return['error'] = true;
					$array_return['message'] = 'Form or form basic datas didn\'t send';
					echo json_encode($array_return); die;
				}
				else
				{
					$form_basic_datas['license_id'] = $license_id;
					$form_basic_datas['domain'] = $domain;
					//To replace $ by jQuery
					$form_basic_datas['jquery_compatibility'] = true;
					$form = $this->OpencartFormGenerator->generateForm($form, $form_basic_datas);
					$array_return['form'] = $form;					
				}
			//END

			echo json_encode($array_return); die;
		}		

		public function get_license_from_ticket_system()
		{
			$array_return = array('error' => false, 'message' => '');
			$license_id = !empty($this->request->data['id_order']) ? $this->request->data['id_order'] : '';

			try {
			    $license_info = $this->OpencartExtension->license_get_license($license_id);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			if(!empty($license_info))
			{
				$array_return['buyer_email'] = $license_info['Sale']['buyer_email'];
				$array_return['domain'] = $license_info['Sale']['domain'];

			}

			echo json_encode($array_return); die;
		}

		public function ajax_open_ticket()
		{
			$array_return = array('error' => false, 'message' => '<b>Ticket sent successfully</b>, check your email inbox or SPAM folder, you will get an auto response with ticket details.');

			$this->request->data['type'] = 'Support';

			try {
			    $this->OpencartTicket->open_ticket($this->request->data);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			echo json_encode($array_return); die;
		}

		public function extension_download()
		{
			$download_id = array_key_exists('download_id', $_GET) ? $_GET['download_id'] : '';
			$changelog_id = array_key_exists('changelog_id', $_GET) ? $_GET['changelog_id'] : '';

			if(empty($download_id) || empty($changelog_id))
				$this->error_manual('GET params not found');

			try {
			    $filename = $this->OpencartExtension->check_license_time_download($download_id, $changelog_id);
			} catch (Exception $e) {
				$this->error_manual($e->getMessage());
			}

			$file_path = APP.WEBROOT_DIR.DS.'extensions_UdtAtIU8'.DS.$filename['folder'].DS.$filename['zip_name'];
			$this->response->file(
			    $file_path,
			    array('download' => true, 'name' => $filename['zip_name'])
			);
			return $this->response;	
		}

		public function ajax_get_extension_download_links()
		{
			$this->layout = 'ajax';
			$array_return = array('error' => false, 'html' => '');
			$download_id = array_key_exists('download_id', $this->request->data) && !empty($this->request->data['download_id']) ? $this->request->data['download_id'] : '';

			if(!empty($download_id))
			{
				$sale = $this->Sale->find('first', array('recursive' => -1, 'fields' => array('Sale.order_id'), 'conditions' => array('Sale.download_id' => $download_id)));

				if(empty($sale))
				{
					$array_return['error'] = true;
					$array_return['message'] = 'Download identifier not found';
					$array_return['html'] = '<b style="font-size:16px; color:#ff0000;">Download identifier not found</b>';
				}
				else
					$array_return['html'] = $this->OpencartExtension->get_download_view($sale['Sale']['order_id']);
			}
			else
			{
				$array_return['error'] = true;
				$array_return['message'] = 'Download identifier not found';
				$array_return['html'] = '<b style="font-size:16px; color:#ff0000;">Download identifier not found</b>';
			}

			echo json_encode($array_return); die;
		}

		public function recover_download_id()
		{
			$license_id = !empty($this->request->data['license_id']) ? $this->request->data['license_id'] : '';

			if(empty($license_id))
				$this->error_manual(__('License not sent'), false);
			else
			{
				try {
			    	$message = $this->OpencartExtension->recover_download_id($license_id);
			    	$this->Session->setFlash($message, 'default', array('class' => 'success'));
				} catch (Exception $e) {
					$this->error_manual($e->getMessage(), false);
				}
			}
			$this->redirect($this->referer());
		}

		public function error_manual($message, $redirect = true)
		{
			$this->Session->setFlash($message, 'default', array('class' => 'error'));
			if($redirect)
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
		}

		public function get_discount() {
		    $array_return = array('error' => false, 'message' => __('Discount code sent successfully, check your email inbox or SPAM folder.'));

			$extension_id = array_key_exists('extension_id', $this->request->data) ? $this->request->data['extension_id'] : '';
			$email = array_key_exists('email', $this->request->data) ? $this->request->data['email'] : '';
			
			try {
				$extension = $this->OpencartExtension->send_email_discount($email, $extension_id);
			} catch (Exception $e) {
			    $array_return['message'] = $e->getMessage();
			    $array_return['error'] = true;
			}

			echo json_encode($array_return); die;
		}
	}
?>