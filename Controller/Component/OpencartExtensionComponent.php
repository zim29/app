<?php
	class OpencartExtensionComponent extends Component {

		public $components = array('Email', 'ExtensionTool');

		public function initialize(Controller $controller) {
            $this->Sale = ClassRegistry::init('Sales.Sale');
            $this->Extension = ClassRegistry::init('Extensions.Extension');
            $this->Changelog = ClassRegistry::init('Extensions.Changelog');

            $this->discount_add_domain = 20;
            $this->discount_renew = 25;

            $this->months_allow_download = 6;

            $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
        }

		public function get_extension_price($license_id, $discount = false)
		{
			$license = $this->license_get_license($license_id);

			//Devman Extensions - info@devmanextensions.com - 2017-08-26 17:18:14 - Get extension price
				if(empty($license['Extension']['price']))
					throw new Exception ('Extension not found');

				$extension_price = $license['Extension']['price'];

				if(!empty($discount))
				{
					$final_discount = 100-$discount;
					$extension_price = ($extension_price*$final_discount) / 100;
				}

				return round($extension_price, 2);
			//END
		}

		public function get_discount_renew()
		{
			return $this->discount_renew;
		}

		public function get_discount_add_domain()
		{
			return $this->discount_add_domain;
		}

		public function get_extension_price_renew($license_id)
		{
			return $this->get_extension_price($license_id);
		}

		public function get_extension_price_add_domain($license_id)
		{
			return $this->get_extension_price($license_id);
		}

		public function check_license($license_id, $domain)
		{
		    if(strpos($license_id, 'trial-') !== false) {
		        return true;
            }
			$array_return = array('error' => false, 'expired' => false, 'message' => '');
		    $domain_exploded = explode(".", $domain);

			$domain_contain_localhost =
                strpos($domain, 'localhost') !== false ||
                strpos($domain_exploded[0], 'dev') !== false ||
                strpos($domain_exploded[1], 'dev') !== false ||
                strpos($domain_exploded[1], 'test') !== false ||
                strpos($domain_exploded[1], 'local') !== false ||
				strpos($domain, 'phpstorm/opencart') !== false ||
                strpos($domain, '127.0.0.1') !== false;
			//DELETE THIS!!!
			//$domain_contain_localhost = false;
			//Devman Extensions - info@devmanextensions.com - 2017-08-26 19:24:55 - Get license
				try {
				    $license = $this->license_get_license($license_id);
				} catch (Exception $e) {
					$array_return['error'] = true;
					$array_return['message'] = $e->getMessage();
					return $array_return;
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-08-26 15:53:02 - If license still has domain assigned, assign this.
				if(empty($license['Sale']['domain']) && !$domain_contain_localhost)
				{
					try {
					    $this->license_first_insert_domain($license_id, $domain);
					} catch (Exception $e) {
						$array_return['error'] = true;
						$array_return['message'] = $e->getMessage();
						return $array_return;
					}
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-08-26 15:54:54 - Check domain are differents
				if(!empty($license['Sale']['domain']) && !$domain_contain_localhost)
				{
					try {
					    $this->license_check_domains($license, $domain);
					} catch (Exception $e) {
						$array_return['error'] = true;
						$array_return['message'] = $e->getMessage();
						return $array_return;
					}
				}
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-08-26 19:00:01 - Check license expired
				try {
					    $this->license_check_expired($license);
					} catch (Exception $e) {
						$array_return['expired'] = true;
						$array_return['message'] = $e->getMessage();
						return $array_return;
					}
			//END

			return $array_return;
		}

		public function check_license_time_download($download_id, $changelog_id)
		{
			$sale = $this->Sale->find('first', array('conditions' => array('Sale.download_id' => $download_id)));
			$changelog = $this->Changelog->find('first', array('conditions' => array('Changelog.id' => $changelog_id)));
			if(empty($sale))
				throw new Exception ('License not found');
			if(empty($changelog))
				throw new Exception ('Changelog not found');

			$sale_extension_id = $sale['Extension']['id'];
			$changelog_extension_id = $changelog['Changelog']['id_extension'];

			if($sale_extension_id != $changelog_extension_id)
				throw new Exception (__('Your license is not for this extension.'));

			$license_id = $sale['Sale']['order_id'];
			$sale_date = !empty($sale['Sale']['date_increase']) ? $sale['Sale']['date_increase'] : $sale['Sale']['date_added'];
			$version_date = strtotime($changelog['Changelog']['created']);
			$limit_version_date = strtotime(date('Y-m-d H:i:s', strtotime("+".$this->months_allow_download." months", strtotime($sale_date))));

			if($limit_version_date < $version_date)
			{
				$link_renew = $this->api_url.'invoices/opencart/new_invoice?type=renew_license&license_id='.$license_id;

				$message = sprintf(__('Your accesss period to download new versions is outside of this version. As a <b>loyal customer</b> you are entitled to a <b><u>-%s%% discount</u></b>. <a href="%s" target="_blank"><b>Renew your license in this link</b></a>.'), $this->discount_renew, $link_renew);

				throw new Exception ($message);
			}

			$filename = array(
				'zip_name' => $sale['Extension']['zip_name'].' V.'.$changelog['Changelog']['version'].'.zip',
				'folder' => $sale['Extension']['system'].DS.$sale['Extension']['zip_name']
			);

			return $filename;
		}

		public function get_download_id_message($license_id)
		{
			$license = $this->license_get_license($license_id);

			$message = 'Your download identifier: <b>'.$license['Sale']['download_id'].'</b><br>';
			$message .= 'Access to <a target="_new" href="https://devmanextensions.com/download-center?download_id='.$license['Sale']['download_id'].'">Download center</a>.';

			return $message;
		}

		public function check_license_vs_extension($license_id, $extension_id)
		{
			$array_return = array('error' => false, 'message' => '');
			//Devman Extensions - info@devmanextensions.com - 2017-08-26 19:24:55 - Get license
				try {
				    $license = $this->license_get_license($license_id);
				} catch (Exception $e) {
					$array_return['error'] = true;
					$array_return['message'] = $e->getMessage();
					return $array_return;
				}
			//END

			if($license['Extension']['id'] != $extension_id)
			{
				$array_return['error'] = true;
				$array_return['message'] = sprintf('Your license <b>%s</b> is for the extension <b>%s</b>', $license_id, $license['Extension']['name']);
				return $array_return;
			}

			return $array_return;
		}

		public function get_last_version($extension_id) {
		    //$last_version = $this->Changelog->find('first', array('conditions' => array('Changelog.id_extension' => $extension_id), 'order' => array('CAST(Changelog.version AS UNSIGNED INTEGER) DESC')));
            $last_version = $this->Changelog->find('first', array('conditions' => array('Changelog.id_extension' => $extension_id), 'order' => array('Changelog.created DESC')));
		    return $last_version['Changelog']['version'];
        }

		public function get_download_view($license_id)
		{
			$license_info = $this->license_get_license($license_id);

			$html = '';

			//Devman Extensions - info@devmanextensions.com - 2017-11-03 13:44:37 - Load license information

                $renew_link = Router::url('/', true).'invoices/opencart/new_invoice?type=renew_license&license_id='.$license_id;

				$date_purchase = !empty($license_info['Sale']['date_increase']) && $license_info['Sale']['date_increase'] != '0000-00-00 00:00:00' ? $license_info['Sale']['date_increase'] : $license_info['Sale']['date_added'];
				$download_date = date('d/m/Y H:i:s', strtotime("+".$this->months_allow_download." months", strtotime($date_purchase)));
				$support_date = date('d/m/Y H:i:s', strtotime("+".$license_info['Extension']['oc_support_months']." months", strtotime($date_purchase)));
				$support_date_raw = strtotime("+".$license_info['Extension']['oc_support_months']." months", strtotime($date_purchase));
				$support_finished = strtotime(date("Y-m-d H:i:s")) > $support_date_raw;

				$html .= '<b>'.__('License ID').'</b>: '.$license_info['Sale']['order_id'].'<br>';
				$html .= '<b>'.__('Extension').'</b>: '.$license_info['Extension']['name'].'<br>';
				$html .= '<b>'.__('Purchase/renew date').'</b>: '.date('d/m/Y H:i:s', strtotime($date_purchase)).'<br>';
				$html .= '<b>'.__('FREE premium support until').'</b>: '.$support_date.' ('.$license_info['Extension']['oc_support_months'].' '.__('months').')';

				if($support_finished)
				    $html .= ' <a style="color:#ff0000;" target="_new" href="'.$renew_link.'">Click to renew</a>';
                $html .= '<br>';

				if($license_info['Extension']['id'] == '5420686f-9450-4afa-a9c1-0994fa641b0a') {
                    $html .= '<b>' . __('Access to workspace API generator until') . '</b>: ' . $support_date . ' (' . $license_info['Extension']['oc_support_months'] . ' ' . __('months') . ')';
                    if($support_finished)
				    $html .= ' <a style="color:#ff0000;" target="_new" href="'.$renew_link.'">Click to renew</a>';
                    $html .= '<br>';
                }

				$html .= '<b>'.__('Access to download new versions expires in').'</b>: '.$download_date.' ('.$this->months_allow_download.' '.__('months').')<br>';
				$html .= '<b>'.__('Access to new versions').'</b>: All previous versions launched before license purchase/renew date and all new version launched '.$this->months_allow_download.' months after your license purchase/renew date.<br>';
				$html .= '<b>'.__('Domains assigned').'</b>: <ul><li>'.implode('</li><li>',explode('|',$license_info['Sale']['domain'])).'</ul>';

				$html .= '<b style="color:#f00;"><u>INSTALL TUTORIAL INSIDE .ZIP EXTENSION</u></b></br>';
			//END

			//Devman Extensions - info@devmanextensions.com - 2017-11-03 13:44:43 - Get download links
				$limit_version_date = strtotime(date('Y-m-d H:i:s', strtotime("+".$this->months_allow_download." months", strtotime($date_purchase))));

				$extension_id = $license_info['Extension']['id'];
				$extension = $license_info['Extension'];
				$this->Changelog = ClassRegistry::init('Extensions.Changelog');
				$changelogs = $this->Changelog->find('all', array('conditions' => array('Changelog.id_extension' => $extension_id), 'order' => array('Changelog.created' => 'DESC')));

				if(!empty($changelogs))
				{

					$html .= '<br><ul>';
						foreach ($changelogs as $key => $cl) {
							$lines = explode(PHP_EOL, $cl['Changelog']['text']);

                            $version_date_formatted = date('d/m/Y H:i:s', strtotime($cl['Changelog']['created']));
                            $version_date = strtotime($cl['Changelog']['created']);
							//Devman Extensions - info@devmanextensions.com - 2017-10-27 13:03:36 - Insert download link
								$link_download = '';
								if(!empty($extension['zip_name']))
								{
									$filename = $extension['zip_name'].' V.'.$cl['Changelog']['version'].'.zip';

									$file_path = APP.WEBROOT_DIR.DS.'extensions_UdtAtIU8'.DS.$extension['system'].DS.$extension['zip_name'].DS.$filename;

									if(file_exists($file_path))
									{
										if($limit_version_date < $version_date)
										{
											$link = Router::url('/', true).'invoices/opencart/new_invoice?type=renew_license&license_id='.$license_id;
											$text = __('Outside of download period, click here to renew license');
											$color = '#ff0000;';
										}
										else
										{
											$link = Router::url('/', true).'opencart/extension_download/?download_id='.$license_info['Sale']['download_id'].'&changelog_id='.$cl['Changelog']['id'];
											$text = __('Download');
											$color = '#82c95b;';
										}

										$link_download = ' - <a style="color:'.$color.'" target="_new" href="'.$link.'">'.$text.'</a>';

									}
								}
								if(!empty($link_download))
									$html .= '<li style="margin-bottom:5px;"><b><u>Version '.$cl['Changelog']['version'].' - '.$version_date_formatted.'</u></b>'.$link_download;
								//END
						}
					$html .= '</ul>';
				}
			//END

			return $html;
		}

		public function license_first_insert_domain($license_id, $domain)
		{
			if(!$this->Sale->updateAll(array('Sale.domain' => '"'.$this->get_domain($domain).'"'), array('Sale.order_id' => $license_id)))
				throw new Exception ('Extension not found');

			return true;
		}

		public function license_check_domains($license_obj, $domain)
		{
		    $domain = $this->get_domain($domain);
			$domains = explode('|', $license_obj['Sale']['domain']);

			$license_id = $license_obj['Sale']['order_id'];

			$domain_found = in_array($domain, $domains);
			$domains_text = $license_obj['Sale']['domain'];
			//If "quantity" is more than domains added, we will add new domain.
			if(!$domain_found && (int)$license_obj['Sale']['quantity'] > count($domains)) {
				$domains_text = $license_obj['Sale']['domain'].'|'.$domain;
                $data_to_save = array('Sale.domain' => '"'.$license_obj['Sale']['domain'].'|'.$domain.'"');
                if(!$this->Sale->updateAll($data_to_save, array('Sale.order_id' => $license_id)))
                    throw new Exception ("Error during add domain");
            } else if(!$domain_found) {
				$domains_string = '';

				foreach ($domains as $key => $dom) {
					$domains_string .= $dom.', ';
				}

				$link_add_domain = $this->api_url.'invoices/opencart/new_invoice?type=add_domain&license_id='.$license_id.'&domain='.$domain;

				$domains_string = mb_substr($domains_string, 0, -2);

				$message = sprintf('Your license <b>%s</b> was registered in another domains. If you need use this extension in this domain, <a href="%s" target="_blank"><b>increase your license in this link</b></a>. As a <b>loyal customer</b> you are entitled to a <b><u>-%s%% discount</u></b>.', $license_id, $link_add_domain, $this->discount_add_domain);

				throw new Exception ($message);
			}

			/**/

			$ip_Client = $_SERVER['REMOTE_ADDR'];
			$ips = explode('|', $license_obj['Sale']['ips']);
			$domains = explode('|', $domains_text);
			$dom_index = array_search($domain, $domains);

			//Construimos las IPS
			if(empty($ips)) {
				$final_ips = array();
				foreach ($domains as $dom) {
					$final_ips[] = '';
				}

				//Insertamos la nueva IP en la posición del dominio
				$final_ips[$dom_index] = $ip_Client;

			} else {
				//Comprobamos ip para el dominio guardado.
				if(empty($ips[$dom_index]))
					$ips[$dom_index] = $ip_Client;
				else {
					if($ips[$dom_index] != '*' && $ips[$dom_index] != $ip_Client) {
						$message = sprintf('Error validating the domain <b>%s</b>, please contact <a href="%s" target="_blank">technical support</a>.', $domain, "https://devmanextensions.com/open_ticket");
						throw new Exception ($message);
					}

				}

				$final_ips = $ips;
			}

			$data_to_save = array('Sale.ips' => '"'.implode("|", $final_ips).'"');
			$this->Sale->updateAll($data_to_save, array('Sale.order_id' => $license_id));

			return true;
		}

		public function license_check_expired($license_obj)
		{
			$license_id = $license_obj['Sale']['order_id'];

			$this->Sale->recursive = 2;
			$license_obj = $this->Sale->find('first', array('conditions' => array('Sale.order_id' => $license_id)));

			$date_purchase = !empty($license_obj['Sale']['date_increase']) ? $license_obj['Sale']['date_increase'] : $license_obj['Sale']['date_added'];
			$limit_date = date('Y-m-d H:i:s', strtotime("+".$license_obj['Extension']['oc_support_months']." months", strtotime($date_purchase)));
			$today = date('Y-m-d H:i:s');

			if(strtotime($today) > strtotime($limit_date)) {
				$link_renew = $this->api_url.'invoices/opencart/new_invoice?type=renew_license&license_id='.$license_id;

				$message = sprintf('Your license <b>%s</b> expired on <b>%s</b>. As a <b>loyal customer</b> you are entitled to a <b><u>-%s%% discount</u></b>. <a href="%s" target="_blank"><b>Renew your license in this link</b></a>.', $license_id, date('d/m/Y H:i:s', strtotime($limit_date)), $this->discount_renew, $link_renew);

				throw new Exception ($message);
			}

			return true;
		}

		public function license_get_license($license_id, $force_waiting = false)
		{
		    $license_id = trim($license_id);
		    $this->Sale = ClassRegistry::init('Sales.Sale');
			$conditions = array(
				'order_id' => $license_id,
				'order_status' => 'Complete'
			);

			if($force_waiting)
				$conditions['order_status'] = 'Waiting for Proof of ID';

			$license = $this->Sale->find('first', array('conditions' => $conditions));

			$conditions = array(
				'download_id' => $license_id,
				'order_status' => 'Complete'
			);
			$license2 = $this->Sale->find('first', array('conditions' => $conditions));

			if(empty($license)) {
				if(!empty($license2))
					throw new Exception ('The data sent is "Download identifier" your license id is: <b>'.$license2['Sale']['order_id'].'</b>');
				else
					throw new Exception ('License ID not found');
			}

			return $license;
		}

		/*ALWAYS ACROSS INVOICE*/
		public function renew_license($invoice)
		{
			$license_id = !empty($invoice['Invoice']['license_id']) ? $invoice['Invoice']['license_id'] : '';
            if($invoice['Invoice']['total'] > 0) {
                if (empty($license_id))
                    throw new Exception ('License ID empty');

                $license_info = $this->license_get_license($license_id);

                $months = $license_info['Extension']['oc_support_months'];
                $new_date = date('Y-m-d H:i:s');

                if (!$this->Sale->updateAll(array('Sale.date_increase' => '"' . $new_date . '"'), array('Sale.order_id' => $license_id)))
                    throw new Exception ("Error during renew license");

                //Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
                $subject = '[DevmanExtensions] Your license ' . $license_id . ' was renewed';

                $message = '';

                $message .= sprintf('Hi %s!!<br>', $invoice['Invoice']['customer_name']);
                $message .= 'Thanks for trust in my work! =)<br><br>';
                $message .= sprintf('Your license <b>%s</b> of your extension <b>%s</b> was renewed!<br>', $license_id, $license_info['Extension']['name']);
                $message .= sprintf('Enjoy of the best technical assistance of Opencart during <b>%s</b> months more! ;)', $months);

                try {
                    $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'DevmanExtensions Licenses System', $subject, $message);
                } catch (Exception $e) {
                    throw new Exception ($e->getMessage());
                }
                //END
            }
		}

		/*ALWAYS ACROSS INVOICE*/
		public function add_domain($invoice)
		{
			$license_id = !empty($invoice['Invoice']['license_id']) ? $invoice['Invoice']['license_id'] : '';
			if(empty($license_id))
				throw new Exception ('License ID empty');

			$domain = !empty($invoice['Invoice']['new_domain']) ? $invoice['Invoice']['new_domain'] : '';
			if(empty($domain))
				throw new Exception ('Domain empty');

			$license_info = $this->license_get_license($license_id);

			$is_gmt = in_array($license_info['Sale']['extension_id'], array(15609, 12707));

			$current_domains = $license_info['Sale']['domain'];

			$data_to_save = array('Sale.domain' => '"'.$current_domains.'|'.$this->get_domain($domain).'"');

			if($is_gmt)
				$data_to_save['Sale.gmt_containers_num'] = $license_info['Sale']['gmt_containers_num'] + 1;

			if(!$this->Sale->updateAll($data_to_save, array('Sale.order_id' => $license_id)))
				throw new Exception ("Error during add domain");

			//Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
				$subject = '[DevmanExtensions] Your license '.$license_id.' has a new domain';

				$message = '';

				$message .= sprintf('Hi %s!!<br>', $invoice['Invoice']['customer_name']);
				$message .= 'Thanks for trust in my work! =)<br><br>';
				$message .= sprintf('Your license <b>%s</b> of your extension <b>%s</b> has a new domain: <b>%s</b>!<br>', $license_id, $license_info['Extension']['name'], $domain);

				try {
				    $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'DevmanExtensions Licenses System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception ($e->getMessage());
				}
			//END
		}

		/*ALWAYS ACROSS INVOICE*/
		public function increase_containers_number($invoice)
		{
			$license_id = !empty($invoice['Invoice']['license_id']) ? $invoice['Invoice']['license_id'] : '';
			if(empty($license_id))
				throw new Exception ('License ID empty');

			$license_info = $this->license_get_license($license_id);

			$new_gmt_container_num = $license_info['Sale']['gmt_containers_num'] + 1;

			if(!$this->Sale->updateAll(array('Sale.gmt_containers_num' => $new_gmt_container_num), array('Sale.order_id' => $license_id)))
				throw new Exception ("Error during increase container numbers");

			//Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
				$subject = '[DevmanExtensions] Your license '.$license_id.' can generate more containers';

				$message = '';

				$message .= sprintf('Hi %s!!<br>', $invoice['Invoice']['customer_name']);
				$message .= 'Thanks for trust in my work! =)<br><br>';
				$message .= sprintf('Your license <b>%s</b> of your extension <b>%s</b> can generate more containers!<br>', $license_id, $license_info['Extension']['name']);

				try {
				    $this->Email->send_email($invoice['Invoice']['customer_email'], 'info@devmanextensions.com', 'DevmanExtensions Licenses System', $subject, $message);
				} catch (Exception $e) {
					throw new Exception ($e->getMessage());
				}
			//END
		}

		public function recover_download_id($license_id)
		{
			$license_info = $this->license_get_license($license_id);

			$return_message = $this->recover_download_id_send_email($license_info, true);

			return $return_message;
		}

		public function recover_download_id_send_email($license_info, $return_message = false, $russian = false)
		{
			$buyer_email = $license_info['Sale']['buyer_email'];

			$russian = $russian || ($license_info['Sale']['marketplace'] == 'opencartforum');

			if(!$russian) {
                $subject = '[DevmanExtensions] Your Download Identifier';
                $content = '';
                $content .= 'Your download identifier: <b>' . $license_info['Sale']['download_id'] . '</b><br>';
                $content .= 'Access to <a href="https://devmanextensions.com/download-center?download_id=' . $license_info['Sale']['download_id'] . '">this link</a> to download your extension.';
            } else {
			    $subject = '[DevmanExtensions] Ваш идентификатор загрузки';
                $content = '';
                $content .= 'Ваш идентификатор загрузки: <b>' . $license_info['Sale']['download_id'] . '</b><br>';
                $content .= 'Перейдите по <a href="https://devmanextensions.com/download-center?download_id=' . $license_info['Sale']['download_id'] . '">этой ссылке</a> чтобы скачать ваш модуль.';
            }

			try {
			    $this->Email->send_email($buyer_email, 'info@devmanextensions.com', 'DevmanExtensions Download Center', $subject, $content);
			    if($return_message)
			    	return sprintf(__('Download Identifier sent successfully to email <b>%s</b>'), $buyer_email);
			    else
			    	return true;
			} catch (Exception $e) {
				throw new Exception ($e->getMessage());
			}
		}

		public function count_extensions_waiting()
		{
			$this->Sale = ClassRegistry::init('Sales.Sale');
			$conditions = array('Sale.order_status' => array('Waiting for Proof of ID', 'pending_validate'));
			$count_sales = $this->Sale->find('count', array('conditions' => $conditions));
			return $count_sales;
		}

		public function complete_order($license_id)
		{
			$download_id = $this->generate_uuid();
			if(!$this->Sale->updateAll(array('Sale.order_status' => '"Complete"', 'Sale.download_id' => '"'.$download_id.'"'), array('Sale.order_id' => $license_id)))
				throw new Exception ("Error during complete license");

			$this->send_emails_purchase_client($license_id);
			$this->send_emails_purchase_administrator($license_id);
			$this->recover_download_id($license_id);
		}

		public function generate_uuid() {
		    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		        mt_rand( 0, 0xffff ),
		        mt_rand( 0, 0x0fff ) | 0x4000,
		        mt_rand( 0, 0x3fff ) | 0x8000,
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    );
		}

		public function send_email_discount($email, $extension_id) {
			if(empty($email) || empty($extension_id))
				throw new Exception ("Extension or email params lost");
			$extension = $this->ExtensionTool->get_extension($extension_id);

			$discount_code = $extension['Extension']['discount_code'];

			$subject = 'Your discount code';

			$content = 'Hi! here your discount code, <b>hurry up! are limited!!</b><br><br>';

			$content .= '<b>Extension name</b>: '.$extension['Extension']['name']."<br>";
			$content .= '<b>Discount</b>: '.$extension['Extension']['discount']."%<br>";
			$content .= '<b>Apply discount now!:</b> To apply discount directly, do <a href="https://devmanextensions.com/cart?automatic_add_product='.$extension['Extension']['id'].'&automatic_add_discount='.$extension['Extension']['discount_code'].'">click en this link</a><br>';
			$content .= '<b>Discount code</b>: '.$extension['Extension']['discount_code']."<br><br>";

			$this->Email->send_email($email, 'sales@devmanextensions.com', 'DevmanExtension', $subject, $content);

			$subject = 'Discount ordered - '.$extension['Extension']['name'];
			$content .= '<b>Email</b>: <a href="mailto:'.$email.'">'.$email."</a><br>";
			$this->Email->send_email('info@devmanextensions.com', 'sales@devmanextensions.com', 'DevmanExtension', $subject, $content);
		}

		public function send_emails_purchase_client($license_id, $russian = false)
		{
			$this->Sale = ClassRegistry::init('Sales.Sale');

			$license_info = $this->license_get_license($license_id);

			if(!$russian) {
                //Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
                    $subject = 'Important information about your purchase';
                    $content = 'Hi ' . $license_info['Sale']['buyer_username'] . '.<br><br>';

                    $content .= 'Thanks you for purchase of ' . $license_info['Extension']['name'] . '. If you have some problem please use our <a href="https://devmanextensions.com/open_ticket">system tickets</a>, we attend you soon as possible in support hours.<br><br>';

                    $content .= '<b>Order id (license number)</b>: ' . $license_info['Sale']['order_id'] . "<br>";
                    $content .= '<b>Extension name</b>: ' . $license_info['Extension']['name'] . "<br>";
                    $content .= '<b>Quantity</b>: ' . $license_info['Sale']['quantity'] . "<br>";
                    $content .= '<b>Date purchase</b>: ' . (date('d/m/Y H:i:s', strtotime($license_info['Sale']['date_added']))) . "<br><br>";

                    $content .= 'Enjoy of exclusive discounts in <a href="https://devmanextensions.com/extensions-shop">our own shop</a>!!';
                    $content .= '<br><a href="https://devmanextensions.com/extensions-shop"><img src="cid:image_0"></a><br>';

                    $content .= '<br><br><img src="cid:image_1">';

                    $images = array(
                        array(
                            'name' => 'thanks-for-your-purchase.jpg',
                            'path' => WWW_ROOT.'images/extensions/thanks_for_your_purchase.jpg'
                        ),
                        array(
                            'name' => 'devman-signature.jpg',
                            'path' => WWW_ROOT.'images/devman_ceo_david_signature.png'
                        ),
                    );
                //END
            } else {
			    //Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
                    $subject = 'Важная информация относительно вашей покупки';
                    $content = 'Добрый день ' . $license_info['Sale']['buyer_username'] . '!.<br><br>';

                    $content .= 'Спасибо за приобретение ' . $license_info['Extension']['name'] . '. Если у вас возникла какая-либо проблема, пожалуйста, воспользуйтесь нашей <a href="https://devmanextensions.com/open_ticket">системой тикетов</a>, мы отреагируем максимально быстро в рабочее время поддержки.<br><br>';

                    $content .= '<b>Идентификатор заказа (номер лицензии)</b>: ' . $license_info['Sale']['order_id'] . "<br>";
                    $content .= '<b>Название модуля</b>: ' . $license_info['Extension']['name'] . "<br>";
                    $content .= '<b>Количество</b>: ' . $license_info['Sale']['quantity'] . "<br>";
                    $content .= '<b>Дата приобретения</b>: ' . (date('d/m/Y H:i:s', strtotime($license_info['Sale']['date_added']))) . "<br><br>";

                    $content .= '<br>Воспользуйтесь эксклюзивными скидками в <a href="https://devmanextensions.com/extensions-shop">нашем собственном магазине</a>!!';
                    $content .= '<br><a href="https://devmanextensions.com/extensions-shop"><img src="cid:image_0"></a>';

                    $content .= '<br><br><img src="cid:image_1">';

                    $images = array(
                        array(
                            'name' => 'thanks-for-your-purchase.jpg',
                            'path' => WWW_ROOT.'images/extensions/thanks_for_your_purchase_ru.jpg'
                        ),
                        array(
                            'name' => 'devman-signature.jpg',
                            'path' => WWW_ROOT.'images/devman_ceo_david_signature.png'
                        ),
                    );
                //END
            }
            try {
                $this->Email->send_email($license_info['Sale']['buyer_email'], 'sales@devmanextensions.com', 'DevmanExtension', $subject, $content, '', array(), $images);
            } catch (Exception $e) {
                throw new Exception ($e->getMessage());
            }
			$this->recover_download_id_send_email($license_info, false, $russian);
		}

		public function send_emails_purchase_administrator($license_id, $force_waiting = false)
		{
			$this->Sale = ClassRegistry::init('Sales.Sale');
			$license_info = $this->license_get_license($license_id, $force_waiting);

			$waiting = $license_info['Sale']['order_status'] == 'Waiting for Proof of ID';

			//Devman Extensions - info@devmanextensions.com - 2017-08-27 12:33:29 - Send email to customer
				//Get total money this month
					$total_sales = $this->Sale->get_sales_by_month(date('Y-m'));
				//END Get total money this month

				$subject = 'OC Extension purchased'.($waiting ? ' [WAITING]' : '').' - '.$license_info['Sale']['order_id'].' - '.$license_info['Extension']['name'].' - '.$total_sales;

				$content = '<b>Total in '.date('Y/m').': '.$total_sales."</b><br><br>";

				$content .= '<b>Order id</b>: '.$license_info['Sale']['order_id']."<br>";
				$content .= '<b>Extension name</b>: '.$license_info['Extension']['name']."<br>";
				$content .= '<b>Extension id</b>: '.$license_info['Extension']['id']."<br>";
				$content .= '<b>Buyer id</b>: '.$license_info['Sale']['buyer_id']."<br>";
				$content .= '<b>Buyer username</b>: '.$license_info['Sale']['buyer_username']."<br>";
				$content .= '<b>Buyer email</b>: '.$license_info['Sale']['buyer_email']."<br>";
				$content .= '<b>Quantity</b>: '.$license_info['Sale']['quantity']."<br>";
				$content .= '<b>Sub total</b>: $'.number_format($license_info['Sale']['sub_total'],2)."<br>";
				$content .= '<b>Commision</b>: '.$license_info['Sale']['commission']."%<br>";
				$content .= '<b>Total</b>: $'.number_format($license_info['Sale']['total'],2)."<br>";
				$content .= '<b>Order status</b>: '.$license_info['Sale']['order_status']."<br>";
				$content .= '<b>Date added</b>: '.$license_info['Sale']['date_added']."<br>";
				$content .= '<b>Date modified</b>: '.$license_info['Sale']['date_modified']."<br>";

				try {
				    $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Devman Extension Sales System', $subject, $content);
				} catch (Exception $e) {
					throw new Exception ($e->getMessage());
				}
			//END
		}
	}
?>
