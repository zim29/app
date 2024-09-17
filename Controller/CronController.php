<?php
	class CronController extends AppController {

		public  $uses = array(
      		'Sales.Sale',
      		'Competitions.Competition',
      		'Competitions.CompetitionExtension',
      		'Competitions.CompetitionSale',
      		'Extensions.Extension',
      		'Testimonial',
            'Invoices.Invoice',
            'TrialLicense'
		);

		public function beforeFilter() {
		    $holidays_from = Configure::read('holidays_from').' 00:00:00';
		    $holidays_to = Configure::read('holidays_to').' 23:59:59';
            $today = date('Y-m-d H:i:s');
            $this->vacation_time = ($today > $holidays_from) && ($today < $holidays_to);

	        $this->Auth->allow('online','offline','restore_db_quick_create','exam_competition','refresh_customers', 'exam_opencart_comments', 'insert_dates_in_holidays_banner', 'generate_purchase_bar');
	    }

	    public $components = array('Client', 'Email');

		function online()
		{
		    if($this->vacation_time)
		        $this->insert_dates_in_holidays_banner();
		    else {
                $file_name = !$this->vacation_time ? 'online.jpg' : 'offline_holidays.jpg';
                unlink(WWW_ROOT . 'images/extensions/support_status.jpg');
                copy(WWW_ROOT . 'images/extensions/' . $file_name, WWW_ROOT . 'images/extensions/support_status.jpg');

                $file_name = !$this->vacation_time ? 'online_ru.jpg' : 'offline_holidays_ru.jpg';
                unlink(WWW_ROOT . 'images/extensions/support_status_ru.jpg');
                copy(WWW_ROOT . 'images/extensions/' . $file_name, WWW_ROOT . 'images/extensions/support_status_ru.jpg');
            }

			die('finish');
		}
		function offline()
		{
		     if($this->vacation_time)
		        $this->insert_dates_in_holidays_banner();
		     else {
                 $file_name = !$this->vacation_time ? 'offline.jpg' : 'offline_holidays.jpg';
                 unlink(WWW_ROOT . 'images/extensions/support_status.jpg');
                 copy(WWW_ROOT . 'images/extensions/' . $file_name, WWW_ROOT . 'images/extensions/support_status.jpg');

                 $file_name = !$this->vacation_time ? 'offline_ru.jpg' : 'offline_holidays_ru.jpg';
                 unlink(WWW_ROOT . 'images/extensions/support_status_ru.jpg');
                 copy(WWW_ROOT . 'images/extensions/' . $file_name, WWW_ROOT . 'images/extensions/support_status_ru.jpg');
             }

			die('finish');
		}
		function  insert_dates_in_holidays_banner() {
		    $image_path = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'offline_holidays.jpg';
		    $image_path_final = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'support_status.jpg';
		    $image_path_final_ru = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'support_status_ru.jpg';
            $font_path = APP.WEBROOT_DIR.DS.'v2'.DS.'fonts'.DS.'WorkSans'.DS.'WorkSans-Bold.ttf';

            $holidays_from = date('d/m/Y', strtotime(Configure::read('holidays_from').' 00:00:00'));
		    $holidays_to = date('d/m/Y', strtotime(Configure::read('holidays_to').' 23:59:59'));

            $image_path = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'offline_holidays.jpg';
            $jpg_image = imagecreatefromjpeg($image_path);

            $color = imagecolorallocate($jpg_image, 221, 221, 221);
            imagettftext($jpg_image, 18, 0, 318, 37, $color, $font_path, $holidays_from);
            imagejpeg($jpg_image, $image_path_final, 100);

            $jpg_image = imagecreatefromjpeg($image_path_final);
            $color = imagecolorallocate($jpg_image, 221, 221, 221);
            imagettftext($jpg_image, 18, 0, 495, 37, $color, $font_path, $holidays_to);
            imagejpeg($jpg_image, $image_path_final, 100);
            copy($image_path_final, $image_path_final_ru);
        }
		function refresh_customers() {
			//INVOICES PAYED
			    $conditions = array(
			        'Invoice.state' => 'Pending',
	                'Invoice.type' => array('Renew','Add domain','New GMT Container','License'),
	                'Invoice.created >=' => date('Y-m-d H:i:s', strtotime("-1 hours")),
	            );
			    $invoices = $this->Invoice->find('all', array('conditions' => $conditions, 'group' => array('Invoice.customer_email')));
			    $conditions['Invoice.state'] = 'Payed';
			    foreach ($invoices as $key => $inv) {
			        $conditions['Invoice.customer_email'] = $inv['Invoice']['customer_email'];
	                $invoices = $this->Invoice->find('all', array('conditions' => $conditions));
	                if(empty($invoices)) {
	                    $subject = 'Problems paying your invoice?';
	                    $content = sprintf('<p>Hi %s!</p><p>We detected that you tried pay an invoice, did you have some problem?</p>', $inv['Invoice']['customer_name']);
	                    $link_invoice = Router::url('/', true).'invoices/opencart/validate_invoice/'.$inv['Invoice']['id'];
	                    if($inv['Invoice']['customer_email'] == 'Credit Card') {
	                        $content .= sprintf('<p>Our TPV is refusing some credit card types, in your case, you can use second payment method Stripe which is compatible with all credit cards. Also can use Paypal (you can pay across paypal with your credit card).</p>
	                                    <p>To change payment method and finish your purchase click in <a href="%s">next link</a>.</p>', $link_invoice);
	                    } else {
	                        $content .= sprintf('<p>Remember that you can finish your purchase in <a href="%s">next link</a>.</p>', $link_invoice);
	                    }

	                    $content .= '<p>Thanks a lot!</p>';

	                    $this->Email->send_email($inv['Invoice']['customer_email'], 'info@devmanextensions.com', 'Devman Extensions', $subject, $content);
	                }
			    }
			//END INVOICES PAYED

			//TRIALS
			    $conditions = array(
			    	'TrialLicense.modified <= ' => date('Y-m-d H:59:59', strtotime("-36 hours")),
			    	'TrialLicense.modified >= ' => date('Y-m-d H:00:00', strtotime("-36 hours")),
                    'TrialLicense.created >= ' => '2019-01-03 21:09:31',
			    );

			    $trials = $this->TrialLicense->find('all', array('conditions' => $conditions, 'group' => array('TrialLicense.customer_email')));

			 	foreach ($trials as $key => $trial) {
			 		$subject = sprintf('%s, we need to know your opinion about Import Export', $trial['TrialLicense']['customer_name']);

					$content = sprintf('<p>Hi, %s!</p>
					<p>First of all, thank you for the trust in our products.</p>
					<p>We are gathering opinions from our clients to achieve a more complete and compatible extension with all possible needs. If you are so kind, we appreciate you answering the following questions, you can skip the ones you want:<p>
					<ol>
					<li>Have you been able to prove it well in the time we offer?</li>
					<li>Have you had any problems during the test?</li>
					<li>Have you met the expected expectations?</li>
					<li>Have you tried another importer before? What do you miss from other importers?</li>
					<li>Have you followed a video tutorial of the ones we offer?</li>
					<li>What do you think would be a fair price for this module?</li>
					<li>Will you finally buy it? If not, what led you to discard our module with respect to the competition?</li>
					</ol>
					<p>Thanks a lot by your apportation!</p>
					<br><br><img src="cid:image_0">', $trial['TrialLicense']['customer_name']);

					$images = array(
                        array(
                            'name' => 'devman-signature.jpg',
                            'path' => WWW_ROOT.'images/devman_ceo_david_signature.png'
                        ),
                    );

                    $this->Email->send_email($trial['TrialLicense']['customer_email'], 'info@devmanextensions.com', 'Devman Extensions', $subject, $content, '', array(), $images);
			 	}
			//END TRIAL
			$this->Client->update_count_client_image();
			die("Finish");
		}
		function restore_db_quick_create()
		{
			$this->exam_competition();
			/*
			// Name of the file
			$filename = 'http://devmanextensions.com/opencart_demos/quick_create/asdf892738947ds897f89esw.sql';
			// MySQL host
			$mysql_host = 'localhost';
			// MySQL username
			$mysql_username = 'devmandb';
			// MySQL password
			$mysql_password = 'Cmyk36~3';
			// Database name
			$mysql_database = 'devmandb';

			// Connect to MySQL server
			mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
			// Select database
			mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());

			// Temporary variable, used to store current query
			$templine = '';
			// Read in entire file
			$lines = file($filename);
			// Loop through each line

			foreach ($lines as $line)
			{
				// Skip it if it's a comment
				if (substr($line, 0, 2) == '--' || $line == '')
				    continue;

				// Add this line to the current segment
				$templine .= $line;
				// If it has a semicolon at the end, it's the end of the query
				if (substr(trim($line), -1, 1) == ';')
				{
				    mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
				    // Reset temp variable to empty
				    $templine = '';
				}
			}*/
			echo "Tables imported successfully"; die;
		}

		function check_licenses()
		{
			$workspaces = scandir('gmt_workspace/exported/');
			$final_licenses = array();
			foreach ($workspaces as $key => $workspace) {
				if(!in_array($workspace, array('..', '.')))
				{
					$exploded = explode('-', $workspace);
					if(count($exploded) == 3)
					{
						if(!isset($final_licenses[$exploded[0]]))
							$final_licenses[$exploded[0]] = array();

						$final_licenses[$exploded[0]][] = str_replace('.json', '', $exploded[1].'-'.$exploded[2]);
					}
				}
			}

			/*
			$more_than_1 = array();
			foreach ($final_licenses as $key => $value) {
				if(count($value) > 1)
					$more_than_1[] = $value;
			}
			echo '<pre>'; print_r($more_than_1);  echo '</pre>'; die;*/

			foreach ($final_licenses as $license_id => $gtm_names) {
				if(count($gtm_names) > 1)
					$this->send_warning_license_email($license_id, $gtm_names);
			}
		}

		public function send_warning_license_email($license_id, $gtm_names)
		{
			$licenses_skip = array('867553');
			if(in_array($license_id, $licenses_skip))
				return true;

			$conditions = array(
				'extension_id' => 15609,
				'order_id' => $license_id
			);

			$license = $this->Sale->find('first', array('conditions' => $conditions));

			if(!empty($license))
			{
				$subject = 'Google Marketing Tools - License '.$license_id.' - WARNING: multiples uses with one license';
				$content = '';

				App::uses('CakeEmail', 'Network/Email');

				$from_email = 'info@devmanextensions.com';

				$Email = new CakeEmail();
				$Email->from(array($from_email => 'DevmanExtensions'));

				$Email->viewVars(compact('license', 'gtm_names'));
				$email_to = $license['Sale']['buyer_email'];
				$Email->to($email_to);
				//$Email->to('info@devmanextensions.com');
				$Email->emailFormat('html');
				$Email->attachments();
				$Email->template('license_gmt');
				$Email->subject($subject);

				return $Email->send($content);
			}
		}

		public function exam_opencart_comments()
		{
			$extension_ids = array();
			$extensions = $this->Extension->find('all', array('conditions' => array('Extension.oc_extension_id !=' => '')));
			$testimonials = array();
			foreach ($extensions as $key => $ext) {
				$id_extension = $ext['Extension']['oc_extension_id'];
				if($id_extension != '11111') {
					$url = "https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=".$id_extension;
					$html = file_get_contents($url);
					$dom = new DOMDocument('1.0', 'utf-8');
					$dom->loadHTML($html);
					$content_node=$dom->getElementById("marketplace-extension-info");
					if(!empty($content_node)) {
						$div_a_class_nodes=$this->getElementsByClass($content_node, 'div', 'media');
						foreach ($div_a_class_nodes as $key => $value) {
							$temp = preg_replace( "/\r|\n/", "", $value->nodeValue);

							if($temp != '') {
								$split_comment = explode('~', $value->nodeValue);
								$testimonial = preg_replace( "/\r|\n/", "", $split_comment[0]);
								$user = preg_replace( "/\r|\n/", "", $split_comment[1]);
								if(!empty($user) && !empty($testimonial))
								$testimonials[$user] = array(
									'testimonial' => $testimonial,
									'user' => $user,
									'extension_id' => $ext['Extension']['id'],
								);
							}
						}
					}
				}
			}
			if(!empty($testimonials)) {
				foreach ($testimonials as $key => $testimonial) {
					$conditions = array(
						'Testimonial.extension_id' => $testimonial['extension_id'],
						'Testimonial.name' => $testimonial['user']
					);
					$test_exist = $this->Testimonial->find('first', array('conditions' => $conditions));
					if(empty($test_exist)) {
						$to_save = array(
							'Testimonial' => array(
								'extension_id' => $testimonial['extension_id'],
								'name' => $testimonial['user'],
								'testimonial' => $testimonial['testimonial'],
								'position' => 'Opencart Marketplace',
								'opencart_marketplace' => 1,
								'rate' => 5,
								'image' => 'opencart_marketplace.jpg',
								'order' => 500,
								'published' => 1,
								'url' => 'https://www.opencart.com/',
                                'country_id' => 258
							)
						);
						$this->Testimonial->saveAll($to_save);
					}
					# code...
				}
			}
			die('finished');
		}

		function getElementsByClass(&$parentNode, $tagName, $className) {
			    $nodes=array();

			    $childNodeList = $parentNode->getElementsByTagName($tagName);
			    for ($i = 0; $i < $childNodeList->length; $i++) {
			        $temp = $childNodeList->item($i);
			        if (stripos($temp->getAttribute('class'), $className) !== false) {
			            $nodes[]=$temp;
			        }
			    }

			    return $nodes;
			}

		public function exam_competition()
		{
			$competitions = $this->Competition->find('all');

			foreach ($competitions as $key => $comp) {
				$id_competition = $comp['Competition']['id'];

				foreach ($comp['CompetitionExtension'] as $key2 => $ext) {
					$id_extension = $ext['id_extension'];

					$url = "https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=".$id_extension;
					$page = file_get_contents($url);

					//Devman Extensions - info@devmanextensions.com - 2017-11-11 12:41:56 - Check name
						if(empty($ext['name']))
						{
							$extension_name = '';
							preg_match_all('/<h3(.*?)<\/h3>/s',$page,$extension_name);
							if(isset($extension_name[0][0]))
							{
								$extension_name = isset($extension_name[0][0]) ? $extension_name[0][0] : '';

								if(!empty($extension_name))
								{
									$extension_name = str_replace(array('<h3>','</h3>'), '', $extension_name);

									$temp = array(
						        		'CompetitionExtension.name' => '"'.$extension_name.'"'
						        	);

						        	if(!$this->CompetitionExtension->updateAll($temp, array('CompetitionExtension.id_extension' => $ext['id_extension'])))
						        		die("Error actualizando nombre extensión");
								}
							}
						}
					//END

					//Devman Extensions - info@devmanextensions.com - 2017-11-11 12:53:46 - Get price
						$price = 0;
						preg_match_all('/<div class=\"col-xs-7 text-right\"(.*?)<\/div>/s',$page,$price);
						if(isset($price[0][0]))
						{
							$price = isset($price[0][0]) ? $price[0][0] : '';

							if(!empty($price))
								$price =  (float)str_replace(array('<div class="col-xs-7 text-right">', '</div>', '$'), '',  $price);
						}
					//END

					//Devman Extensions - info@devmanextensions.com - 2017-11-11 12:45:24 - Add new sale
						$sales = '';
						preg_match_all('/<div id=\"sales\" class=\"well\"(.*?)<\/div>/s',$page,$sales);

						if(isset($sales[0][0]))
						{
							preg_match_all('!\d+!', $sales[0][0], $sales);
							$sales = isset($sales[0][0]) ? $sales[0][0] : 0;

							if(!empty($sales))
							{
								//Get the last sales to compare
									$conditions = array(
										'CompetitionSale.id_competition' => $id_competition,
										'CompetitionSale.id_extension' => $id_extension
									);

									$competition_sales = $this->CompetitionSale->find('first', array('conditions' => $conditions, 'order' => array('CompetitionSale.created' => 'DESC')));

									$num_sales_today = !empty($competition_sales) ? (int)$sales - $competition_sales['CompetitionSale']['current_sales'] : 0;

									$temp = array(
										'CompetitionSale' => array(
											'id_competition' => $id_competition,
											'id_extension' => $id_extension,
											'num_sales_today' => $num_sales_today,
											'current_sales' => (int)$sales,
											'current_price' => (float)$price
										)
									);

									if(!$this->CompetitionSale->saveAll($temp))
										die("Error creando venta competidor");
								//END
							}
						}
					//END
				}
			}
			$subject = 'Competition checked';
			$content = '';
			//$this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Devman Extension Competition', $subject, $content);
			die("Finish!");
		}

		public function generate_purchase_bar() {
		    $link = !empty($_GET['link']) ? $_GET['link'] : '';
		    $this->layout = false;
		    $this->set('link', $link);
		    $this->set('admin', !empty($_GET['admin']));
        }
	}
?>
