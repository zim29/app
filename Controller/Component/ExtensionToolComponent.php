<?php
	class ExtensionToolComponent extends Component {
		public $components = array('Session');

		public function __construct()
		{
			$this->Extension = ClassRegistry::init('Extensions.Extension');
            $this->Sale = ClassRegistry::init('Sales.Sale');
            $this->Coupon = ClassRegistry::init('Sales.Coupon');
			$this->global_discount = Configure::read('discount_extensions_shop');
			$this->discount_calculate = Configure::read('discount_extensions_shop_calculate');
		}

		public function get_extension($id) {
			if(empty($id))
				throw new Exception('Extension not found');

			$this->Extension = ClassRegistry::init('Extensions.Extension');

			$extension = $this->Extension->find('first', array('conditions' => array('Extension.id' => $id), 'recursive' => -1));

			if(empty($extension))
				throw new Exception('Extension not found');

			return $extension;
		}
		public function get_extension_to_cart($extension_id, $quantity = 1)
		{
			$this->Extension = ClassRegistry::init('Extensions.Extension');

			$extension = $this->Extension->find('first', array('conditions' => array('Extension.id' => $extension_id), 'recursive' => -1));
			$price = !empty($extension['Extension']['special']) ? $extension['Extension']['special'] : $extension['Extension']['price'];
			//$discount = !empty($extension['Extension']['discount']) ? (100-$extension['Extension']['discount']) / 100 : $this->discount_calculate;
			//$special = $price * $discount;

			$extension_id = $extension['Extension']['id'];
			App::uses('Session', 'Controller/Component');
			$this->Session = new SessionComponent(new ComponentCollection());
			$discounts = $this->Session->read('discounts') ? $this->Session->read('discounts') : array();

			$special = !empty($extension['Extension']['special']) ? $extension['Extension']['special'] : $price;

			if(array_key_exists($extension_id, $discounts)) {
				$discount = (100-$discounts[$extension_id]['discount']) / 100;
				$special = $price * $discount;
			}
            $discount_applied = $this->Session->read('discounts');
			$discounts = Configure::read('discount_pack');
			$discount_percentage = 0;
            if($quantity > 1) {
                $discount_percentage = $discounts[$quantity];
                $price = $price * ((100-$discount_percentage)/100);
            } else if($discount_applied) {
                $discount_percentage = $discount_applied[$extension_id]['discount'];
                $price = $price * ((100-$discount_percentage)/100);
            }
			$array_return = array(
				'price' => number_format($price, 2),
				'special' => number_format($special, 2),
				'old_price' => number_format($extension['Extension']['old_price'], 2),
				//'discount' => !empty($extension['Extension']['discount']) ? $extension['Extension']['discount'] : $this->global_discount,
				'discount' => array_key_exists($extension_id, $discounts) ? $discounts[$extension_id]['discount'] : '',
				'discount_code' => array_key_exists($extension_id, $discounts) ? $discounts[$extension_id]['code'] : '',
				'discount_percentage' => $discount_percentage,
				'id' => $extension['Extension']['id'],
				'name' => $extension['Extension']['name'].' - '.ucfirst($extension['Extension']['system']),
				'quantity' => $quantity,
				'total' => number_format($price*$quantity, 2),
			);

			return $array_return;
		}

        /**
         * @return array
         */
        public function get_in_shop_extensions()
		{
			$conditions = array(
				'Extension.in_shop' => 1,
                'Extension.system' => "opencart",
			);

			$extensions = $this->Extension->find('all', array('conditions' => $conditions, 'order' => array('Extension.order ASC'), 'recursive' => -1));

			$final_start_products = array();

			foreach ($extensions as $key => $ext) {
				$temp_ext = $ext['Extension'];

				//Devman Extensions - info@devmanextensions.com - 2017-10-19 11:08:08 - Calculate current uses
					$num_sales = $this->Sale->find('count', array('conditions' => array('Sale.extension_id' => $temp_ext['oc_extension_id']), 'recursive' => -1));
					$num_sales += 300;
					$temp_ext['num_clients'] = $num_sales;
				//END

				$prices = $this->get_extension_to_cart($ext['Extension']['id']);
				$temp_ext['price'] = $prices['price'];
				$temp_ext['special'] = !empty($prices['special']) ? $prices['special'] : 0;
				$temp_ext['old_price'] = !empty($prices['old_price']) ? $prices['old_price'] : 0;
				$temp_ext['price_with_discount'] = $prices['special'];
				$temp_ext['discount'] = $prices['discount'];

				//Devman Extensions - info@devmanextensions.com - 2017-10-19 11:08:19 - Put opencart link
					$temp_ext['discount_link'] = 'shop/discount?extension_id='.$temp_ext['id'];
				//END

				$final_start_products[] = $temp_ext;
			}

            /*App::import('Component','ApiLicenses');
            $ApiLicenses = new ApiLicensesComponent(new ComponentCollection);
            $api_extensions = $ApiLicenses->get_extensions();
            foreach ($final_start_products as $key => $ext) {
                if(!empty($ext['api_id'])) {
                    $api_id = $ext['api_id'];
                    $system = $ext['system'];
                    foreach ($api_extensions as $key2 => $api_system) {
                        if($system == strtolower($api_system['name'])) {
                            foreach ($api_system['products'] as $key3 => $api_system_prod) {
                                if($api_system_prod['id'] == $api_id) {
                                    $final_start_products[$key]['price'] = $api_system_prod['price'];
                                    $final_start_products[$key]['name'] = $api_system_prod['extension']['name'];
                                    $final_start_products[$key]['description'] = $api_system_prod['extension']['subtitle'];
                                    $final_start_products[$key]['title_sub'] = $api_system_prod['extension']['description'];
                                }
                            }
                        }
                    }
                }

            }*/
			return $final_start_products;
		}

		public function get_start_products()
		{
			$conditions = array(
				'Extension.star_product' => 1
			);
			$extensions = $this->Extension->find('all', array('conditions' => $conditions, 'order' => array('Extension.order ASC'), 'recursive' => -1));

			$final_start_products = array();

			foreach ($extensions as $key => $ext) {
				$temp_ext = $ext['Extension'];

				//Devman Extensions - info@devmanextensions.com - 2017-10-19 11:06:40 - Format features
					$features = explode("\n", $temp_ext['features']);
					$temp_features = '<ul class="features">';
						foreach ($features as $feature) {
							$temp_features .= '<li>'.$feature.'</li>';
						}
					$temp_features .= '</ul>';
					$temp_ext['features_formatted'] = $temp_features;
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-10-19 11:08:08 - Calculate current uses
					$num_sales = $this->Sale->find('count', array('conditions' => array('Sale.extension_id' => $temp_ext['oc_extension_id']), 'recursive' => -1));
					$num_sales += 300;
					$temp_ext['num_clients'] = $num_sales;
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-10-19 11:08:19 - Put opencart link
					$temp_ext['oc_link'] = 'https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id='.$temp_ext['oc_extension_id'];
				//END

				$final_start_products[] = $temp_ext;
			}

			return $final_start_products;
		}

		function create_license($extension_id, $quantity, $email, $customer_name)
		{
			$license_id = $this->Sale->find('first', array('conditions' => array('Sale.order_id LIKE' => '%ML%'), 'order' => array('Sale.order_id DESC')));
			$license_number = (int)preg_replace('/[^0-9]/', '', $license_id['Sale']['order_id']);
			$license_id = $license_number +1;
			$license_id = 'ML'.sprintf('%06d', $license_id);
			$extension = $this->Extension->findById($extension_id);

			$extension_id = !empty($extension['Extension']['oc_extension_id']) ? $extension['Extension']['oc_extension_id'] : $extension['Extension']['id'];
			$date = date('Y-m-d H:i:s');
			$temp = array(
				'Sale' => array(
					'order_id' => $license_id,
					'marketplace' => 'DevmanExtensions',
					'domain_num' => 1,
					'download_id' => $this->generate_uuid(),
					'extension_id' => $extension_id,
					'extension_name' => $extension['Extension']['name'],
					'buyer_username' => $customer_name,
					'buyer_id' => null,
					'buyer_email' => $email,
					'quantity' => $quantity,
					'subtotal' => 0,
					'commission' => 0,
					'order_status' => 'Complete',
					'total' => 0,
					'gmt_containers_num' => $extension_id == '15609' ? $quantity : 1,
					'date_added' => $date,
					'date_added' => $date
				)
			);

			if(!$this->Sale->saveAll($temp))
				throw new Exception('Error creating license!');

			App::import('Component', 'Mailchimp');
            $this->Mailchimp = new MailchimpComponent(new ComponentCollection());

			/*App::uses('Mailchimp', 'Controller/Component');
			$this->Mailchimp = new MailchimpComponent(new ComponentCollection());*/

			$mailchimp_list_id = 'ddb182e8b6';
            $mailchimp_data = array(
                'email' => $email,
                'merge_fields' => array(
                    'FNAME' => $customer_name,
                    'ENAME' => $extension['Extension']['name'],
                    'EID' => $extension['Extension']['oc_extension_id']
                )
            );

            $this->Mailchimp->subscribe($mailchimp_list_id, $mailchimp_data);

			App::uses('OpencartExtension', 'Controller/Component');
			$this->OpencartExtension = new OpencartExtensionComponent(new ComponentCollection());
			$this->OpencartExtension->send_emails_purchase_client($license_id);
			$this->Coupon->create_coupon($license_id, $extension['Extension']['id']);
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
	}
?>
