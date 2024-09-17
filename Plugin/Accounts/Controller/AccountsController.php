<?php
	class AccountsController extends AccountsAppController
	{
		public  $uses = array(
		    'Accounts.Account',
			'Sales.Sale'
		);

		public $components = array(
			'CountryTools',
			'Session',
			'Cart'
		);

		// only allow the login controllers only
	    public function beforeFilter() {
	        $this->Auth->allow('index','register','login','logout','licenses', 'create_accounts', 'password_recovery_assign', 'password_recovery');
	    }

		/**
         *
         * Función Index
         *
         */
		function index() {
			$this->set_layout();

			$is_logged = $this->Session->read("logged");
			$this->set(compact('is_logged'));
		}

		function register() {
			$this->set_layout();

			$is_logged = $this->Session->read("logged");
			if(empty($this->request->data) && $is_logged) {
				$this->request->data = $this->Account->find('first', array('conditions' => array('Account.id' => $is_logged)));
				unset($this->request->data['Account']['password']);
			}

			if($this->request && $this->request->is(array('post','put'))) {
				try{
					if(empty($this->request->data))
						throw new Exception('Not allow direct access.');
					//Devman Extensions - info@devmanextensions.com - 2016-10-12 19:42:38 - Captcha
						$userIP = $_SERVER["REMOTE_ADDR"];
						$recaptchaResponse = $this->request->data['g-recaptcha-response'];
						$secretKey = "6LeNxKAUAAAAAPNyzGddMVFpZLZUefwO4E3HXTh7";

						$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

						if(!strstr($request, "true"))
							throw new Exception('Complete the captcha');
					//END
					if(!empty($this->request->data['Account']['id'])) {
						$this->Account->validator()->remove('email_confirm');
						$this->Account->validator()->remove('password');
					}

					if (!$this->Account->saveAll($this->request->data)) {
						$errors = $this->Account->validationErrors;
						$error_message = '';
						foreach ($errors as $field_name => $error) {
							$error_message .= implode('<br>', $error).'<br>';
						}
						$this->Session->setFlash($error_message, 'default',array('class' => 'error'));
						//$this->Session->setFlash(__('Unable to update your user.'));
					}
					else {
						if($is_logged) {
							$this->request->data = $this->Account->find('first', array('conditions' => array('Account.id' => $is_logged)));
							$this->Session->setFlash(
								'<i class="fa fa-check"></i> <b>Account information updated successfully</b>.'
							);
						} else {
							$this->request->data = array();
							$this->Session->setFlash(
								'<i class="fa fa-check"></i> <b>Account created successfully</b>.'
							);
							$account_id = $this->Account->getLastInsertID();
							$this->Session->write('logged', $account_id);

							$cart_count = $this->Cart->count_products();

							if(!$cart_count) {
								$this->redirect(array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'index'));
							} else {
								$this->redirect(array('plugin' => false, 'controller' => 'Pages', 'action' => 'display', 'cart'));
							}

						}
					}
				}catch(Exception $e){
					$this->Session->setFlash($e->getMessage(), 'default',array('class' => 'error'));
				}
			} else if(!$is_logged) {
				$this->request->data['Account']['newsletter'] = 1;
			}

			$this->set("current_zone_id", !empty($this->request->data['Account']['zone_id']) ? $this->request->data['Account']['zone_id'] : '');
			$countries = $this->CountryTools->select_format_countries();

			$this->set(compact('countries'));
		}

		function licenses() {
			$this->set_layout();

			$is_logged = $this->Session->read("logged");
			$account = $this->Account->find('first', array('conditions' => array('Account.id' => $is_logged)));

			if(!empty($account['Sale'])) {
				foreach ($account['Sale'] as $key => $ext) {
					$account['Sale'][$key]['sale_status'] = $this->Sale->sale_status($ext);
				}
				$sales = $this->array_group_by($this->aasort($account['Sale'], "date_added"), 'extension_id');
				$this->set("licenses", $sales);
			}
		}

		function login() {
			$this->set_layout();
			if($this->request && $this->request->is(array('post','put'))) {
				try{
					$logged = $this->Account->login($this->request->data['Account']['email'], $this->request->data['Account']['password']);
					$this->Session->write('logged', $logged);
					$this->redirect(array('plugin' => 'accounts', 'controller' => 'accounts', 'action'=>'index'));
				}catch(Exception $e){
					$this->Session->setFlash($e->getMessage(), 'default',array('class' => 'error'));
				}
			}
		}

		function logout() {
			$this->Session->delete('logged');
			$this->redirect('/');
		}

		function set_layout() {
			$this->set('noindex', true);
			$cart_count = $this->Cart->count_products();
			$this->set('cart_count', $cart_count);
			$this->layout = 'frontend';
		}

		function password_recovery() {
			$this->set_layout();
			if($this->request && $this->request->is(array('post','put'))) {
				try{
					$email = !empty($this->request->data['Account']['email']) ? $this->request->data['Account']['email'] : '';
					$this->Account->recursive = -1;
					$account = $this->Account->find("first", array('conditions' => array("Account.email" => $email), 'fields' => array("Account.password_recovery")));
					if(empty($account['Account']['password_recovery']))
						throw new Exception('This email has not an account assigned');
					else {
						App::uses('CakeEmail', 'Network/Email');
						//$email = 'info@devmanextensions.com';
						$subject = 'DevmanExtensions - Password recovery';
						$content = 'Please click on the <a href="'.Router::url('/', true).'account/password-recovery?recovery_code='.$account['Account']['password_recovery'].'">following link</a> to generate a new password. The new password will be sent to the same email address.<br>
			            <b>* If you did not request this password recovery, please ignore this email.</b>';

						$Email = new CakeEmail();
						$Email->from(array('info@devmanextensions.com' => 'DevmanExtensions'));
						$Email->to($email);
						$Email->emailFormat('html');
						$Email->template('password_recovery');
						$Email->subject($subject);
						$Email->send($content);

						$this->Session->setFlash(
							'<i class="fa fa-check"></i> An email was sent to <b>'.$email.'</b> with password recovery link. Check SPAM folder in case that is not in INBOX.'
						);

						$this->request->data = array();
					}
				}catch(Exception $e){
					$this->Session->setFlash($e->getMessage(), 'default',array('class' => 'error'));
				}
			}

			if(!empty($_GET['recovery_code'])) {
				$this->Account->reset_password($_GET['recovery_code']);
				$this->Session->setFlash(
					'<i class="fa fa-check"></i> A new password was generated and sent to your email. Check SPAM folder in case that is not in INBOX.'
				);
			}
		}

		function password_recovery_assign() {
			$this->Account->recursive = -1;
			$accounts = $this->Account->find("all", array('fields' => array('Account.id')));
			foreach ($accounts as $key => $acc) {

				$this->Account->saveAll(array(
					'Account' => array(
						'id' => $acc['Account']['id'],
						'password_recovery' => $this->generate_uuid()
					)
				));

			}

			die("Done");
			//password_recovery
		}

		function create_accounts() {
			die("DISABLED");
			$this->Account->validator()->remove('name');
			$this->Account->validator()->remove('address');
			$this->Account->validator()->remove('country_id');
			$this->Account->validator()->remove('zone_id');
			$this->Account->validator()->remove('city');
			$this->Account->validator()->remove('post_code');
			$this->Account->validator()->remove('password');
			//$this->Account->validator()->remove('email');
			$this->Account->validator()->remove('email_confirm');

			$accounts_created = array();
			$this->Sale->recursice = -1;
			$sales = $this->Sale->find("all", array("fields" => array("order_id", 'buyer_email', 'buyer_username'), "conditions" => array("Sale.id_account" => 0)));

			$final_sales = array();
			foreach ($sales as $sale) {
				$final_sales[] = $sale['Sale'];
			}

			$final_sales = $this->array_group_by($final_sales, "buyer_email");
			foreach ($final_sales as $email => $sale) {
				$password = $this->_generate_random_string();

				$account_data = array(
					"Account" => array(
						'email' => $email,
						'password' => $password,
						'name' => $sale[0]['buyer_username']
					)
				);

				$order_ids = array();
				foreach ($sale as $sal) {
					$order_ids[] = $sal['order_id'];
				}

				if($this->Account->saveAll($account_data)) {
					$accounts_created[] = array(
						'email' => $email,
						'password' => $password
					);

					$account_id = $this->Account->getLastInsertID();

					$this->Account->query('UPDATE intranet_sales SET id_account = '.$account_id." WHERE order_id IN ('".implode("','", $order_ids)."')");
				} else {
					$exist_account = $this->Account->findByEmail($email);
					if(!empty($exist_account['Account']['id'])) {
						$this->Account->query('UPDATE intranet_sales SET id_account = '.$exist_account['Account']['id']." WHERE id_account = 0 AND buyer_email = '".$email."'");
					}
				}

			}
			echo '<table>';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Email</th>';
						echo '<th>Password</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					foreach ($accounts_created as $acc) {
						echo '<tr>';
							echo '<td>'.$acc['email'].'</td>';
							echo '<td>'.$acc['password'].'</td>';
						echo '</tr>';
					}
				echo '</tbody>';

			echo '</table>';
			die;
		}
	}
?>
