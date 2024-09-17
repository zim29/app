<?php

//require_once VENDORS . 'klaviyo/vendor/autoload.php';
//use Klaviyo\Client;

class KlaviyoComponent extends Component {
	public $components = array('Session');

	public function curl_klaviyo_api($action, $data = array()) {

		$data = array(
			'action' => $action,
			'post_data' => $data
		);

		$url = 'https://klaviyoapi.devmanextensions.com/';
		$postData = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($postData)
		));

		$response = curl_exec($ch);

		$response = json_decode($response, true);
		curl_close($ch);

		$this->write_log($action, 'POST', $data, $response);

		return $response;
	}

	public function profile($creating, $account) {
		$accountModel = ClassRegistry::init('Accounts.Account');
		$account = $accountModel->findById($account['Account']['id']);
		$account = $accountModel->addCountryAndZone($account['Account']);

		//Client doesn't accept marketing in register
		if($creating && empty($account['newsletter']))
			return array();

		$data = [
			'data' => [
				'type' => 'profile',
				'attributes' => [
					'email' => $account['email'],
					'external_id' => $account['id'],
					'first_name' => $account['name'],
					'location' => [
						'address1' => $account['address'],
						'city' => $account['city'],
						'country' => $account['country'],
						'region' => $account['zone'],
						'zip' => $account['post_code'],
					],
					'properties' => [
						'external_id' => $account['id'],
					]
				]
			]
		];


		$action = 'profiles/';

		$curl_request_type = 'POST';
		$marketing = !empty($account['newsletter']);
		$id_klaviyo = $account['id_klaviyo'];

		$execute_curl = false;
		//CASE 1: Marketing is enabled and an is creating account - create profile
		if($creating && $marketing) {
			//Do nothing, system will create the account
			$execute_curl = true;
		} else {
			//CASE 2: Marketing is enabled and doesn't exist id_klaviyo - create profile
			if($marketing && !$id_klaviyo) {
				$execute_curl = true;
			} else

			//CASE 3: Marketing is enabled and exists id_klaviyo - update profile
			if($marketing && $id_klaviyo) {
				$execute_curl = true;
				$data['data']['id'] = $id_klaviyo;
			}
		}

		if($execute_curl) {
			$response = $this->curl_klaviyo_api('create_profile', $data);
			if (!empty($response['data']['id'])) {
				$data['id_klaviyo'] = $response['data']['id'];
				$accountModel->query("UPDATE intranet_accounts SET id_klaviyo = '" . $response['data']['id'] . "' WHERE id = " . $account['id']);
			}
		}

		//Susbription to list
		if($creating) {
			$this->susbcribe_to_list($data);
		} else {
			//CASE 4: Marketing is disabled and id_klaviyo is filled - Remove profile
			if (!$marketing && $id_klaviyo) {
				$this->supress($account['email']);
			}

			//CASE 5: Only just in case, if client has "newsletter" accepted and is not creating account, unsupress klaviyo account
			if (!$creating && $marketing && $id_klaviyo) {
				$this->unsupress($account['email']);
			}
		}

		return $response;
	}

	public function susbcribe_to_list($client_data) {
		$data = [
			'data' => [
				'type' => 'profile-subscription-bulk-create-job',
				'attributes' => [
					'custom_source' => 'Marketing Event',
					'profiles' => [
						'data' => [
							[
								'type' => 'profile',
								'id' => $client_data['data']['id'],
								'attributes' => [
									'email' => $client_data['data']['attributes']['email'],
									'phone_number' => $client_data['data']['attributes']['phone_number'],
									'subscriptions' => [
										'email' => [
											'marketing' => [
												'consent' => 'SUBSCRIBED'
											]
										]
									]
								]
							]
						]
					]
				],
				'relationships' => [
					'list' => [
						'data' => [
							'type' => 'list',
							'id' => 'TAnsUA'
						]
					]
				]
			]
		];

		$this->curl_klaviyo_api('subscribe_profile', $data);
	}

	public function register_purchase_OLD($order_data) {
		$curl = curl_init();

		$item_names = array();
		$items = array();

		foreach ($order_data['products'] as $prod) {
			$item_names[] = $prod['name'];
			$items[] = array(
				'ProductID' => $prod['id'],
				//'SKU' => '',
				'ProductName' => $prod['name'],
				'Quantity' => $prod['quantity'],
				'ItemPrice' => $prod['price'],
				'RowTotal' => $prod['total'],
				'ProductURL' => $prod['url'],
				'ImageURL' => $prod['image'],
				//'Categories' => $prod['image'],
				//'Brand' => '',
			);

		}


		$data_to_send = json_encode(
			array(
			   'token' => Configure::read('klaviyo_public_api_key'),
			   'event' => 'Placed Order',
			   'customer_properties' => array(
				 '$email' => $order_data['customer_email'],
				 '$first_name' => $order_data['customer_name'],
				 //'$last_name' => '',
				 //'$phone_number' => '',
				 '$address1' => $order_data['customer_address'],
				 //'$address2' => 'Suite 1',
				 '$city' => $order_data['customer_city'],
				 '$zip' => $order_data['customer_post_code'],
				 '$region' => $order_data['customer_zone_code'],
				 '$country' => $order_data['customer_country_code'],
			   ),
			   'properties' => array(
				 '$event_id' => $order_data['event_id'],
				 '$value' => $order_data['total'],
				 'OrderId' => $order_data['order_id'],
				 //'Categories' => array('Fiction', 'Classics', 'Children'),
				 'ItemNames' => $item_names,
				 //'Brands' => array('Kids Books', 'Harcourt Classics'),
				 //'DiscountCode' => 'Free Shipping',
				 //'DiscountValue' => 5,
				 'Items' => $items,
				 'BillingAddress' => array(
				   'FirstName' => $order_data['customer_name'],
				   //'LastName' => 'Smith',
				   //'Company' => '',
				   'Address1' => $order_data['customer_address'],
				   //'Address2' => 'apt 1',
				   'City' => $order_data['customer_city'],
				   'Region' => $order_data['customer_zone'],
				   'RegionCode' => $order_data['customer_zone_code'],
				   'Country' => $order_data['customer_country'],
				   'CountryCode' => $order_data['customer_country_code'],
				   'Zip' => $order_data['customer_post_code'],
				   //'Phone' => '5551234567'
				 ),
				 'ShippingAddress' => array(
					 'FirstName' => $order_data['customer_name'],
					 //'LastName' => 'Smith',
					 //'Company' => '',
					 'Address1' => $order_data['customer_address'],
					 //'Address2' => 'apt 1',
					 'City' => $order_data['customer_city'],
					 'Region' => $order_data['customer_zone'],
					 'RegionCode' => $order_data['customer_zone_code'],
					 'Country' => $order_data['customer_country'],
					 'CountryCode' => $order_data['customer_country_code'],
					 'Zip' => $order_data['customer_post_code'],
					 //'Phone' => '5551234567'
				 )
			   ),
			   'time' => time()
			));

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://a.klaviyo.com/api/track",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			//CURLOPT_POSTFIELDS => json_encode($data_to_send),
			CURLOPT_POSTFIELDS => 'data='.rawurlencode($data_to_send),
			CURLOPT_HTTPHEADER => array(
				"accept: text/html",
				"content-type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		$this->write_log("register_purchase", "POST", $data_to_send, $response);
	}

	public function register_purchase($order_data) {
		$account_id = $this->Session->read("logged");

		if(empty($account_id))
			return false;

		$accountModel = ClassRegistry::init('Accounts.Account');
		$accountModel->recursive = -1;
		$account = $accountModel->findById($account_id);
		$account = $account['Account'];

		$id = $account['id'];
		$id_klaviyo = $account['id_klaviyo'];
		$email = $account['email'];
		$marketing = $account['newsletter'];

		if(empty($id_klaviyo) || empty($marketing))
			return false;

		$eventName = 'Placed Order';

		$item_names = array();
		$items = array();

		foreach ($order_data['products'] as $prod) {
			$item_names[] = $prod['name'];
			$items[] = array(
				'ProductID' => $prod['id'],
				//'SKU' => '',
				'ProductName' => $prod['name'],
				'Quantity' => $prod['quantity'],
				'ItemPrice' => $prod['price'],
				'RowTotal' => $prod['total'],
				'ProductURL' => $prod['url'],
				'ImageURL' => $prod['image'],
				//'Categories' => $prod['image'],
				//'Brand' => '',
			);

		}
		$data_to_send = array(
				'customer_properties' => array(
					'email' => $order_data['customer_email'],
					'first_name' => $order_data['customer_name'],
					//'last_name' => '',
					//'phone_number' => '',
					'address1' => $order_data['customer_address'],
					//'address2' => 'Suite 1',
					'city' => $order_data['customer_city'],
					'zip' => $order_data['customer_post_code'],
					'region' => $order_data['customer_zone_code'],
					'country' => $order_data['customer_country_code'],
				),
				'properties' => array(
					'event_id' => $order_data['event_id'],
					'value' => $order_data['total'],
					'OrderId' => $order_data['order_id'],
					//'Categories' => array('Fiction', 'Classics', 'Children'),
					'ItemNames' => $item_names,
					//'Brands' => array('Kids Books', 'Harcourt Classics'),
					//'DiscountCode' => 'Free Shipping',
					//'DiscountValue' => 5,
					'Items' => $items,
					'BillingAddress' => array(
						'FirstName' => $order_data['customer_name'],
						//'LastName' => 'Smith',
						//'Company' => '',
						'Address1' => $order_data['customer_address'],
						//'Address2' => 'apt 1',
						'City' => $order_data['customer_city'],
						'Region' => $order_data['customer_zone'],
						'RegionCode' => $order_data['customer_zone_code'],
						'Country' => $order_data['customer_country'],
						'CountryCode' => $order_data['customer_country_code'],
						'Zip' => $order_data['customer_post_code'],
						//'Phone' => '5551234567'
					),
					'ShippingAddress' => array(
						'FirstName' => $order_data['customer_name'],
						//'LastName' => 'Smith',
						//'Company' => '',
						'Address1' => $order_data['customer_address'],
						//'Address2' => 'apt 1',
						'City' => $order_data['customer_city'],
						'Region' => $order_data['customer_zone'],
						'RegionCode' => $order_data['customer_zone_code'],
						'Country' => $order_data['customer_country'],
						'CountryCode' => $order_data['customer_country_code'],
						'Zip' => $order_data['customer_post_code'],
						//'Phone' => '5551234567'
					)
				),
				'time' => time()
			);

		$eventData = $this->flattenArray($data_to_send);

		$data = [
			'data' => [
				'type' => 'event',
				'attributes' => [
					'properties' => array_merge([
						'name' => $eventName,
						'$value' => $order_data['total']
					], $eventData),
					'time' => date("Y-m-d\TH:i:s"),
					'unique_id' => time() . '_' . $eventName,
					'metric' => [
						'data' => [
							'type' => 'metric',
							'attributes' => [
								'name' => $eventName
							]
						]
					],
					'profile' => [
						'data' => [
							'type' => 'profile',
							'id' => $id_klaviyo,
							'attributes' => [
								'email' => $email,
								'external_id' => $id,
								'properties' => [
									'consent' => true
								]
							]
						]
					],
				]
			]
		];


		$this->curl_klaviyo_api('event', $data);
	}

	public function supress($email) {
		$data = [
			'data' => [
				'type' => 'profile-suppression-bulk-create-job',
				'attributes' => [
					'profiles' => [
						'data' => [
							[
								'type' => 'profile',
								'attributes' => [
									'email' => $email,
								]
							]
						]
					]
				]
			]
		];

		$this->curl_klaviyo_api('suppress_profile', $data);
	}

	public function unsupress($email) {
		$data = [
			'data' => [
				'type' => 'profile-suppression-bulk-delete-job',
				'attributes' => [
					'profiles' => [
						'data' => [
							[
								'type' => 'profile',
								'attributes' => [
									'email' => $email,
								]
							]
						]
					]
				]
			]
		];

		$this->curl_klaviyo_api('unsuppress_profile', $data);
	}

	public function write_log($action, $type, $data, $response) {
		$message = "\n".'Action: '.$action;
		$message .= "\n".'Type: '.$type;
		$message .= "\n".'Data: '.print_r($data, true);
		$message .= "\n".'Response: '.print_r($response, true);

		CakeLog::write('klaviyo', $message);
	}

	public function flattenArray($array, $prefix = '') {
		$result = array();
		foreach ($array as $key => $value) {
			$newKey = $prefix ? $prefix . '_' . $key : $key;
			if (is_array($value)) {
				$result = array_merge($result, $this->flattenArray($value, $newKey));
			} else {
				$result[$newKey] = $value;
			}
		}
		return $result;
	}

}
