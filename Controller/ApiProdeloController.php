<?php 
	class ApiProdeloController extends AppController {

	    public $components = array('Mailchimp');
	    private $key = 't_wxhhhmX9Yo';

		public function beforeFilter() {
	        $this->Auth->allow('new_sale');
	    }

	    public  $uses = array(
      		'Sales.Sale',
            'Sales.Coupon'
		);

		function new_sale()
		{
			$result = json_decode(file_get_contents('php://input'));

			if(empty($result))
			    die("Empty data");
            
		    $marketplace = $result->marketplace; // name marketplace - prodelo
		    $user_id = $result->user_id; // user id, may be = 0 
		    $username = $result->username; // username
		    $email = $result->email; // user mail
		    $order_id = $result->order_id; // Number of order user
		    $order_status = $result->order_status; // Status success if payment is done, request - test request
		    $date_added = $result->date_added; // Pay date in ISO 8601 format
		    $date_request = $result->date_request; // Date request from server ProDelo in ISO 8601 format
		    $extension_id = $result->extension_id; // Extension id in ProDelo marketplace
		    $extension = $result->extension; // Extension name
		    $quantity = $result->quantity; // Quantity
		    $option = $result->option; // Data array with optional services extension developer? May be empty
		    $domain = $result->domain; // Main domain. May be empty if extension not need this
		    $dev_domain = $result->dev_domain; // Test domain. May be empty
		    $cost = $result->cost; // Price extension with additional services (without comission marketplace ProDelo), may be 0 for free extensions
		    $balance = $result->balance; // Developer Current Balance (without comission marketplace ProDelo)

		    $response = strlen($order_id) . $order_id . strlen($date_added) . $date_added;
		    $hash = $this->hmac($this->key, $response);

		    echo 'Ok ' . $hash;

		    if ($order_status == "sdfds") {
                if ($hash == $result->hash) {
                    $result = array('success' => 'Ok');
                    echo json_encode($result); die;
                } else {
                    $result = array('error' => 'Bad hash');
                    echo json_encode($result); die;
                }
            } elseif ($order_status == "request" ) {
		        $date_added = $date_modified = date('Y-m-d H:i:s');
		        $order_id = 'pr-'.$order_id;

		        $domains = '';
                if(!empty($domain))
                    $domains = $this->get_domain($domain);
                if(!empty($dev_domain))
                    $domains .= (!empty($domains) ? '|' : '').$this->get_domain($dev_domain);

                $commission = 15;

                $rub_currency_value = Configure::read('rub_currency_value');

                $sub_total = $cost*$rub_currency_value;
                $total = $sub_total*0.85; //Rest 15%

				$exist_sale = $this->Sale->findByOrderId($order_id);

				$create_order = empty($exist_sale);

                $extensions_translate = array(
                    //Import export pro
                    2280 => 16803,
                    12356 => 16803, //Test api
                    //Google marketing tools
                    7517 => 15609,
                    //Options combinations
                    7631 => 14468,
                );

		
                $OpencartExtension = $this->Components->load('OpencartExtension');
                $client_component = $this->Components->load('Client');
                $client_component->update_count_client_image();
                $send_email = in_array($order_status, array('success'));

                $temp = array(
                    'Sale' => array(
                        'order_id' => $order_id,
                        'system' => 'Opencart',
                        'marketplace' => $marketplace,
                        'extension_id' => $extensions_translate[$extension_id],
                        'extension_id_opencartforum' => $extension_id,
                        //'extension_id' => $extension_id,
                        'extension_name' => $extension,
                        'buyer_id' => $user_id,
                        'buyer_username' => $username,
                        'quantity' => $quantity,
                        'domain' => $domains,
                        'sub_total' => $sub_total,
                        'commission' => $commission,
                        'total' => $total,
                        'buyer_email' => $email,
                        'order_status' => $order_status == 'success' ? 'Complete' : $order_status,
                        'date_added' => $date_added,
                        'date_modified' => $date_modified
                    )
                );

                App::uses('CakeEmail', 'Network/Email');

                if ($create_order) {
                    $mailchimp_list_id = 'ddb182e8b6';
                    $mailchimp_data = array(
                        'email' => $email,
                        'merge_fields' => array(
                            'FNAME' => $username,
                            'ENAME' => $extension,
                            'EID' => $extension_id
                        )
                    );

                    $this->Mailchimp->subscribe($mailchimp_list_id, $mailchimp_data);

                    $temp['Sale']['download_id'] = $this->generate_uuid();

                    if ($this->Sale->saveAll($temp)) {

                        if ($send_email) {
                            $OpencartExtension->send_emails_purchase_administrator($order_id, false);
                            $OpencartExtension->send_emails_purchase_client($order_id, true);
                        	$this->Coupon->create_coupon($order_id, $extension_id);
                        }
                    }
                }
            } else {
                $result = array('error' => 'Bad request');
                //echo json_encode($result);
            }

            die();
		}

		public function hmac($key, $data)
	    {
	        $b = 64; // block size according RFC 2104
	        
	        if (strlen($key) > $b) {
	            $key = pack('H*', md5($key));
	        }
	        
	        $key = str_pad($key, $b, chr(0x00));
	        
	        $ipad = str_pad('', $b, chr(0x36));
	        $opad = str_pad('', $b, chr(0x5c));
	        
	        $k_ipad = $key ^ $ipad ;
	        $k_opad = $key ^ $opad;
	        
	        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
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