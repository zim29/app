<?php
	class OpencartController extends AppController {

		public $uses = array(
      		'Sales.Sale',
      		'Sales.Coupon',
      		'Tickets.Ticket',
            'Extensions.Extension',
            'TrialLicense'
		);

		public $components = array(
		    'OpencartExtension',
		    'Email',
		    'OpencartFormGenerator',
		    'OpencartTicket',
            'Session',
            'ApiLicenses'
		);

		public function beforeFilter() {
	        $this->Auth->allow('ajax_get_form', 'validate_license', 'add_domain', 'increase_license', 'fix_tickets', 'get_license_from_ticket_system', 'ajax_open_ticket', 'extension_download', 'ajax_get_extension_download_links', 'recover_download_id', 'get_discount', 'ajax_curl_test', 'get_trial', 'validate_trial');
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

			if(array_key_exists('form_basic_datas', $this->request->data) && is_array($this->request->data['form_basic_datas']))
			    $form_basic_datas = $this->request->data['form_basic_datas'];
			else
			    $form_basic_datas = !empty($this->request->data['form_basic_datas']) ? json_decode($this->request->data['form_basic_datas'], true) : null;

			if(empty($form_basic_datas))
			{
				$array_return = array('error' => true, 'message' => 'Empty post data form');
				echo json_encode($array_return); die;
			}

			$demo_store = array_key_exists('ts6EpBx2', $form_basic_datas) && !empty($form_basic_datas['ts6EpBx2']);
			$license_id = $demo_store ? '111' : '';
			$domain = $demo_store ? 'https://devmanextensions.com/' : '';
			$demo_url = strpos($domain, 'devmanextensions.com') === true;
            $is_trial = false;
            $this->demo_store = $demo_store;
			if(!$demo_store)
			{
			    $is_options_combinations = array_key_exists('extension_id', $form_basic_datas) && $form_basic_datas['extension_id'] == '5b0e85b6-c9b0-4f0c-a0ce-0905d93d802a';
			    $license_id = !empty($this->request->data['license_id']) ? trim($this->request->data['license_id']) : '';
			    $domain = !empty($this->request->data['domain']) ? $this->request->data['domain'] : '';

				$is_trial = strpos($license_id, 'trial-') !== false;
			    if($is_trial) {
                    try {
                        $extension_id = array_key_exists('extension_id', $form_basic_datas) ? $form_basic_datas['extension_id'] : '';
                        $days_to_expire = $this->TrialLicense->check($license_id, $extension_id, $domain);
                        $trial_info = $this->TrialLicense->findByLicenseId($license_id);

                        $this->TrialLicense->update_version($license_id, $form_basic_datas['version']);

                        $array_return['expired'] = true;

                        //$array_return['message'] = sprintf(_('Trial period will end on <b>%s</b>. Use coupon code <b>%s</b> in <a target="_blank" href="%s"><b><u>our shop</u></b></a> <b>BEFORE</b> your trial ends and get <b><u>10%% DISCOUNT IN ALL OUR PRODUCTS!</u></b>'), $days_to_expire, 'J8N64V9M7H', 'https://devmanextensions.com/shop-opencart');
                        $end_date = date('M d, Y', strtotime(date('Y-m-d'). ' + '.$days_to_expire.' days'));
                        $array_return['message'] = sprintf(_('<b style="font-size: 18px; color: #f00;">IMPORTANT: Your trial expires in less than %s days!!</b><br>Your trial version of Import/Export Pro will stop working <b>%s</b>.<br><b style="font-size: 16px; color: #f00;">SAVE 10%% NOW</b> before your trial ends with coupon code <b>%s</b>, <a href="https://devmanextensions.com/cart?automatic_add_product=542068d4-ed24-47e4-8165-0994fa641b0a&automatic_add_discount_general=J8N64V9M7H" target="_blank"><b><u>CLICK HERE</u></b></a> to apply discount.'), $days_to_expire, $end_date, 'J8N64V9M7H');

                    } catch (Exception $e) {
                        $array_return['error'] = true;
                        $array_return['message'] = $e->getMessage();
                        echo json_encode($array_return); die;
                    }

                    if(array_key_exists('extension_version', $form_basic_datas) && !empty($form_basic_datas['extension_version'])) {
                        $current_version = $this->OpencartExtension->get_last_version($form_basic_datas['extension_id']);
                        $using_version = $form_basic_datas['extension_version'];
                        if(version_compare($using_version, $current_version, "<")) {
                            $array_return['new_version'] = true;
                            $jquery_click_tab = "$('a.tab_changelog---downloads, a.tab_История-изменений---downloads').click()";
                            $array_return['message_new_version'] = sprintf('New available version <b>%s</b>. Recommeded download and install it. All information about changelog in tab <a href="javascript:{}" onclick="%s">"Changelog - Downloads"</a>', $current_version, $jquery_click_tab);
                        }
                    }

                } else {
                    //Devman Extensions - info@devmanextensions.com - 2017-08-26 15:37:40 - Check license and domain
                    $license_checked = $this->OpencartExtension->check_license($license_id, $domain);

                    if ($license_checked['error']) {
                        echo json_encode($license_checked);
                        die;
                    }
                    //END

                    //Devman Extensions - info@devmanextensions.com - 2017-08-30 20:22:07 - Check license expired
                    if (array_key_exists('expired', $license_checked) && !empty($license_checked['expired'])) {
                        /*$array_return['expired'] = true;
                        $array_return['message'] = $license_checked['message'];*/
                    }
                    //END

                    //Devman Extensions - info@devmanextensions.com - 2017-09-21 13:09:57 - Check if license id is for this extension
                    $license_checked = $this->OpencartExtension->check_license_vs_extension($license_id, $form_basic_datas['extension_id']);

                    if ($license_checked['error']) {
                        echo json_encode($license_checked);
                        die;
                    }
                    //END

                     if($is_options_combinations) {
						 $array_return['new_version'] = true;
						 $array_return['message_new_version'] = sprintf('Did you know that with <b><a href="%s" target="_new">Import export PRO</a></b>, you can bulk import/export product options combinations?', "https://devmanextensions.com/opencart-import-export-pro-module");
					 }

                    if(array_key_exists('extension_version', $form_basic_datas) && !empty($form_basic_datas['extension_version'])) {
                        $current_version = $this->OpencartExtension->get_last_version($form_basic_datas['extension_id']);
                        $using_version = $form_basic_datas['extension_version'];

                        if(version_compare($using_version, $current_version, "<") && !$demo_url) {
                            $sale = $this->Sale->find('first', array('recursive' => -1, 'fields' => array('Sale.download_id'), 'conditions' => array('Sale.order_id' => $license_id)));
                            $link_download = 'https://devmanextensions.com/download-center?download_id='.$sale['Sale']['download_id'];
                            $array_return['new_version'] = true;
                            $jquery_click_tab = "$('a.tab_changelog---downloads, a.tab_История-изменений---downloads').click()";
                            $array_return['message_new_version'] = sprintf('New available version <b>%s</b>, <a target="_blank" href="%s">download here</a>. Get more details in tab "<a href="javascript:{}" onclick="%s">Changelog - downloads</a>"', $current_version, $link_download, $jquery_click_tab);
                        }
                    }


                    if (array_key_exists('version', $form_basic_datas) && !empty($form_basic_datas['version'])) {
                        $this->Sale->update_version($license_id, $form_basic_datas['version']);
                    }
                    $this->Sale->increase_get_form($license_id);
                }
			} else {
			    if(array_key_exists('extension_id', $form_basic_datas) && $form_basic_datas['extension_id'] == '542068d4-ed24-47e4-8165-0994fa641b0a') {
                    $array_return['expired'] = true;

                    $array_return['message'] = sprintf('<b>IMPORTANT:</b> this demo store was created with a few export profiles. You can check profile details in "<b>Export - Import</b>" tab or simulate profile creations. While import profiles are not allowed in this demo, in your real store, you will be able to <b>create as many import/export profiles as you wish</b> with <b>custom column names</b>, <b>filters</b>, <b>columns orders</b>, <b>export/import</b> formats and much more!'); //Put us to the test with your store! <a style="color: #f00;" href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=33123&filter_member=DevmanExtensions" target="_blank"><b>CLAIM YOUR FREE TRIAL NOW!</b></a>
                    //$array_return['message'] .= '<br><br><h3>Your opinion is very important for us</h3> <p>Please, <a href="https://docs.google.com/forms/d/e/1FAIpQLSekGsCDFwfCrrmIk2X7D9Rmtvg1eKjtVAqDvb42qN3FvawBIQ/viewform?usp=sf_link" target="_blank"><b>click here</b></a> to open a very short form to know your opinion. This information is very useful for us, thanks a lot by your apportation!</p>';
			    }
            }

			//Devman Extensions - info@devmanextensions.com - 2017-08-29 18:48:13 - Construct form
                if(array_key_exists('form', $this->request->data) && is_array($this->request->data['form']))
                    $form = $this->request->data['form'];
                else
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

					$form = $this->OpencartFormGenerator->generateForm($form, $form_basic_datas, $is_trial);
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

		public function get_trial() {
		    $this->layout = 'frontend';
		    $customer_email = $customer_name = '';
		    try {
                if(!$this->request->is(array('post','put'))) {
                    $domain = array_key_exists('domain', $_GET) ? $_GET['domain'] : '';
                    $extension_id = array_key_exists('extension_id', $_GET) ? $_GET['extension_id'] : '';

                    $this->Extension->recursive = -1;
                    $extension = $this->Extension->find('first', array('conditions' => array('Extension.id' => $extension_id), 'fields' => array('Extension.name')));
                    if (empty($extension)) {
                        throw new Exception('Extension not found');
                    }

                    if (empty($domain) || empty($extension_id)) {
                        throw new Exception('Post data lost');
                    }
                }
                if($this->request->is(array('post','put'))) {
                    $customer_email = array_key_exists('customer_email', $this->request->data) ? $this->request->data['customer_email'] : '';
                    $customer_name = array_key_exists('customer_name', $this->request->data) ? $this->request->data['customer_name'] : '';
                    $domain = array_key_exists('domain', $this->request->data) ? $this->request->data['domain'] : '';
                    $extension_id = array_key_exists('extension_id', $this->request->data) ? $this->request->data['extension_id'] : '';

                    $extension = $this->Extension->find('first', array('conditions' => array('Extension.id' => $extension_id), 'fields' => array('Extension.name')));
                    if (empty($extension)) {
                        throw new Exception('Extension not found');
                    }

                    if(empty($customer_email) || empty($customer_name)) {
                        throw new Exception('Fill email and name');
                    }
                    if(empty($domain) || empty($extension_id)) {
                        throw new Exception('Post data lost');
                    }

                    $this->TrialLicense->create_trial($extension_id, $domain, $customer_name, $customer_email);
                    $message = 'Trial created, an email with link activation was sent to email <b>'.$customer_email.'</b>, check <b>INBOX</b> and <b>SPAM</b> folder.';
                    $this->Session->setFlash($message, 'default', array('class' => 'success'));
                    $this->redirect($this->referer());
                }
            } catch (Exception $e) {
		        $this->set('domain', $domain);
                $this->set('extension_id', $extension_id);
                $this->set('customer_email', $customer_email);
                $this->set('customer_name', $customer_name);
                $this->set('extension_name', $extension['Extension']['name']);

                $message = $e->getMessage();
                $this->Session->setFlash($message, 'default', array('class' => 'error'));
                if($this->referer() == '/')
                    $this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
                else
                    $this->redirect($this->referer());
            }

            $this->set('domain', $domain);
            $this->set('extension_id', $extension_id);
            $this->set('customer_email', $customer_email);
            $this->set('customer_name', $customer_name);
            $this->set('extension_name', $extension['Extension']['name']);
        }

        public function validate_trial($trial_id = '') {
		    try {
		        if(empty($trial_id))
		            throw new Exception('Trial not found');

		        $this->TrialLicense->validate($trial_id);
		        $this->TrialLicense->recursive = -1;
		        $trial = $this->TrialLicense->find('first', array('conditions' => array('TrialLicense.id' => $trial_id), 'fields' => array('TrialLicense.license_id')));
		        $message = sprintf( __('Trial validated sucessfuly! Your trial license is <b>%s</b>'), $trial['TrialLicense']['license_id']);
                $this->Session->setFlash($message, 'default', array('class' => 'success'));
                $this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));

            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->Session->setFlash($message, 'default', array('class' => 'error'));
                $this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
            }
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
					$array_return['html'] = 'Download identifier not found';
				}
				else
					$array_return['html'] = $this->OpencartExtension->get_download_view($sale['Sale']['order_id']);
			}
			else
			{
				$array_return['error'] = true;
				$array_return['message'] = 'Download identifier not found';
				$array_return['html'] = 'Download identifier not found';
			}

			echo json_encode($array_return); die;
		}

		public function validate_license()
		{
			$this->layout = 'ajax';
			$array_return = array('error' => false, 'html' => '');
			$license_id = array_key_exists('license_id', $this->request->data) && !empty($this->request->data['license_id']) ? $this->request->data['license_id'] : '';
			$email = array_key_exists('email', $this->request->data) && !empty($this->request->data['email']) ? $this->request->data['email'] : '';

			if(empty($license_id)) {
			    $array_return['error'] = true;
				$array_return['message'] = 'Fill Order ID';
				$array_return['html'] = 'Fill Order ID';
            }

            if(!$array_return['error'] && empty($email)) {
			    $array_return['error'] = true;
				$array_return['message'] = 'Fill Email';
				$array_return['html'] = 'Fill Email';
            }

            if(!$array_return['error'] && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    $array_return['error'] = true;
				$array_return['message'] = sprintf('Email <b>%s</b> is not a valid email.', $email);
				$array_return['html'] = sprintf('Email <b>%s</b> is not a valid email.', $email);
            }

			if(!$array_return['error']) {
			    try {
			    	$sale = $this->Sale->find('first', array('recursive' => -1, 'fields' => array('Sale.order_id','Sale.buyer_email','Sale.extension_id','Sale.download_id'), 'conditions' => array('Sale.order_id' => $license_id)));
				} catch (Exception $e) {
			        $array_return['error'] = true;
                    $array_return['message'] = $e->getMessage();
                    $array_return['html'] = $e->getMessage();
				}

				if(empty($sale)) {
					$array_return['error'] = true;
					$array_return['message'] = 'Order ID not found. Put in <a href="https://devmanextensions.com/open_ticket" target="_blank">contact with us</a>.';
					$array_return['html'] = 'Order ID not found. Put in <a href="https://devmanextensions.com/open_ticket" target="_blank">contact with us</a>.';
				}
				else {
				    $download_link = Router::url('/', true).'download-center?download_id='.$sale['Sale']['download_id'];
				    if(empty($sale['Sale']['buyer_email'])) {
				        //Save email
                            $data_to_save = array('Sale.buyer_email' => '"'.$email.'"');
                            $this->Sale->updateAll($data_to_save, array('Sale.order_id' => $license_id));
                        //Send email to client
                            $this->OpencartExtension->send_emails_purchase_client($license_id);
                        //Create coupon
                            $this->Coupon->create_coupon($license_id, $sale['Sale']['extension_id']);
                        //Create sale in new invoice system
                            $sale = $this->Sale->findByOrderId($license_id);
                            $this->ApiLicenses->create_sale($sale['Sale']);

                        $array_return['html'] = sprintf('<div class="alert alert-info">You received an email to <b>%s</b> with more information about your purchase and <a href="%s" target="_blank"><b>Direct download link</b></a>. Check your <b>SPAM folder</b> if you didn\'t received it.</div>', $email, $download_link);
                    } else {
				        $array_return['html'] = sprintf('<div class="alert alert-info">Remember that this is your <b><a href="%s" target="_blank">Direct Download link</a></b> where you can download extension without <b>Download center</b>', $download_link).'</div>';
                    }
                    try {
			    	    $array_return['html'] .= $this->OpencartExtension->get_download_view($license_id);
                    } catch (Exception $e) {
                        $array_return['error'] = true;
                        $array_return['message'] = $e->getMessage();
                        $array_return['html'] = $e->getMessage();
                    }
                }
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
