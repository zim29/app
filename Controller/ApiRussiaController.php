<?php

    class OpencartForumPurchases
    {
        private $private_key;

        private static $instance = null;
        private $headers = [];

        public static function getInstance($private_key)
        {
            if (self::$instance !== null)
            {
                return self::$instance;
            }

            self::$instance = new self($private_key);

            return self::$instance;
        }

        public function getPurchase()
        {
            return $this->auth() ? $this->getForm() : false;
        }

        private function __construct($private_key)
        {
            $this->private_key = $private_key;
        }

        private function getForm()
        {
            if (empty($this->form)) {
                $this->form = json_decode(file_get_contents('php://input'), true);
            }

            return $this->form;
        }

        private function auth()
        {
            if (empty($form = $this->getForm()) || empty($form['hash']) || empty($form['order']) ) {
                $this->addHeader("{$_SERVER['SERVER_PROTOCOL']} 400 Bad Request");
                return false;

            }

            if ( ! hash_equals( hash_hmac('md5', (\strlen($form['order']['id']) . $form['order']['id']
                . \strlen($form['order']['date']) . $form['order']['date']), $this->private_key), $form['hash']) ) {

                $this->addHeader("{$_SERVER['SERVER_PROTOCOL']} 401 Unauthorized");
                $this->addHeader('Content-Type: application/json; charset=UTF-8');
                $this->addHeader('State: Unauthorized');
                return false;
            }

            $this->addHeader("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
            $this->addHeader('Content-Type: application/json; charset=UTF-8');

            switch ($form['status']) {
                case 'auth':
                    $this->addHeader('State: Authorized');
                    return false;

                case 'success':
                    $this->addHeader('State: Received');
                    return true;
            }
        }

        private function addHeader($header)
        {
            $this->headers[] = $header;
        }

        private function output(array $data = [])
        {
            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            echo json_decode($data);
            die;
        }

        public function __destruct()
        {
            $this->output();
        }
    }


	class ApiRussiaController extends AppController {

	    public $components = array('Mailchimp', 'ApiLicenses');

		public function beforeFilter() {
	        $this->Auth->allow('new_sale');
	    }

	    public  $uses = array(
      		'Sales.Sale',
            'Sales.Coupon'
		);



		function new_sale()
		{

		    try {
                $private_key		= "LDgU0jmhF9nZf2g";
                $opencartforum_api 	= \OpencartForumPurchases::getInstance($private_key);
                if (($purchase_info = $opencartforum_api->getPurchase())) {
                    $result = array('success' => 'Received');
                    //echo json_encode($result);

                    $marketplace = $purchase_info['marketplace']; //Marketplace identificator, in this case always "opencartforum"
                    $order_status = $purchase_info['status']; //Статус заказа
                    $username = $purchase_info['customer']['name']; //Имя покупателя
                    $email = $purchase_info['customer']['email']; // email покупателя
                    $date_added = $date_modified = date('Y-m-d H:i:s'); //Дата покупки
                    $quantity = 1;
                    $order_id = 'of-' . $purchase_info['order']['id'];  //\IPS\Request::i()->LMI_PAYMENT_NO, //Номер счета
                    $extension_id = $purchase_info['file']['id'];  //ID дополнения
                    $member_id = $purchase_info['customer']['id']; //id покупателя
                    $domain = $purchase_info['order']['domain']; //Домен где будет установлено дополнение
                    $test_domain = $purchase_info['order']['test_domain']; //Тестовый домен на этап разработки сайта
                    $extension = $purchase_info['file']['name']; //Название дополнения

                    $domains = '';
                    if (!empty($domain))
                        $domains = $this->get_domain($domain);
                    if (!empty($test_domain))
                        $domains .= (!empty($domains) ? '|' : '') . $this->get_domain($test_domain);

                    $commission = $purchase_info['order']['commission'];

                    $rub_currency_value = Configure::read('rub_currency_value');

                    /*$sub_total = $purchase_info['order']['total']['amount'] * $rub_currency_value;
                    $total = (($purchase_info['order']['total']['amount'] * (100 - $commission)) / 100) * $rub_currency_value;*/

                    $sub_total = $purchase_info['order']['total']['amount'];
                    $total = ($purchase_info['order']['total']['amount'] * (100 - $commission)) / 100;

                    $exist_sale = $this->Sale->findByOrderId($order_id);

                    $create_order = empty($exist_sale);

                    $continue = $order_status != 'Denied';


                    //TODO - Extension IDs translations, for now, only IE PRO

                    $extensions_translate = array(
                        //Import export pro
                        5960 => 16803,
                        //Google marketing tools
                        7517 => 15609,
                        //Options combinations
                        7631 => 14468,
                    );

                    if ($continue) {
                        $OpencartExtension = $this->Components->load('OpencartExtension');
                        $client_component = $this->Components->load('Client');
                        $client_component->update_count_client_image();

                        $send_email = in_array($order_status, array('Complete', 'complete', 'success'));

                        $waiting = $order_status == 'Waiting for Proof of ID';

                        $temp = array(
                            'Sale' => array(
                                'order_id' => $order_id,
                                'system' => 'Opencart',
                                'marketplace' => $marketplace,
                                'extension_id' => $extensions_translate[$extension_id],
                                'extension_id_opencartforum' => $extension_id,
                                //'extension_id' => $extension_id,
                                'extension_name' => $extension,
                                'buyer_id' => $member_id,
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
                                if ($send_email || $waiting) {
                                    $OpencartExtension->send_emails_purchase_administrator($order_id, $waiting);
                                }
                            }

                            if ($send_email) {
                                $OpencartExtension->send_emails_purchase_client($order_id, true);
                                $this->Coupon->create_coupon($order_id, $extension_id);
                            }

                            //NEW API - Register sale.
                            if ($send_email)
                                $this->register_sale_new_api($order_id);

                        } else //TODO - UPDATE ORDER - REFUNDS AND MORE
                        {
                            $this->Sale->query("DELETE FROM intranet_sales where order_id = " . $order_id);

                            if ($temp['Sale']['order_status'] == 'Complete') {
                                $temp['Sale']['download_id'] = $this->generate_uuid();
                            }
                            if ($this->Sale->saveAll($temp)) {
                                if ($temp['Sale']['order_status'] == 'Complete') {
                                    $OpencartExtension->send_emails_purchase_client($order_id, true);
                                    $OpencartExtension->send_emails_purchase_administrator($order_id, false);
                                    //NEW API - Register sale.
                                    $this->register_sale_new_api($order_id);


                                }

                                $subject = 'OC Order changed - ' . $extension;

                                $content = '<b>Total in ' . date('Y/m') . ': ' . $this->Sale->get_sales_by_month(date('Y-m')) . "</b><br><br>";

                                $content .= '<b>Order id</b>: ' . $order_id . "<br>";
                                $content .= '<b>Extension name</b>: ' . $extension . "<br>";
                                $content .= '<b>Extension id</b>: ' . $extension_id . "<br>";
                                $content .= '<b>Buyer id</b>: ' . $member_id . "<br>";
                                $content .= '<b>Buyer username</b>: ' . $username . "<br>";
                                $content .= '<b>Buyer email</b>: ' . $email . "<br>";
                                $content .= '<b>Quantity</b>: ' . $quantity . "<br>";
                                $content .= '<b>Sub total</b>: $' . number_format($sub_total, 2) . "<br>";
                                $content .= '<b>Commision</b>: ' . $commission . "%<br>";
                                $content .= '<b>Total</b>: $' . number_format($total, 2) . "<br>";
                                $content .= '<b>Order status</b>: ' . $order_status . "<br>";
                                $content .= '<b>Date added</b>: ' . $date_added . "<br>";
                                $content .= '<b>Date modified</b>: ' . $date_modified . "<br>";
                            } else {
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
                    }
                }
            } catch (Exception $e) {
                $error_massage = 'Excepción capturada: '.$e->getMessage();
                $Email = new CakeEmail();
                $Email->from(array('info@devmanextensions.com' => 'Devman russian API'));
                $Email->to('info@devmanextensions.com');
                $Email->emailFormat('html');
                $Email->template('ticket_email');
                $Email->subject($subject);
                $Email->send($error_massage);
            }
            die("Finished!");
		}

		public function register_sale_new_api($order_id) {
		    $sale = $this->Sale->findByOrderId($order_id);
		    $this->ApiLicenses->create_sale($sale['Sale']);
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