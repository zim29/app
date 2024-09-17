<?php
class KlaviyoController extends AppController {

	public $components = array('Klaviyo');

	public function beforeFilter() {
		$this->Auth->allow('assign_klaviyo_id');
	}

	public  $uses = array(
		'Account.Account',
	);

	function register_purchase() {

	}

	function assign_klaviyo_id() {
		$accounts = $this->Klaviyo->getProfiles(100000);
		echo '<pre>'; print_r($accounts);  echo '</pre>'; die;
		foreach ($accounts as $account) {
		    $email = !empty($account['attributes']['email']) ? $account['attributes']['email'] : '';
			$id = !empty($account['id']) ? $account['id'] : '';

			if(!empty($email) && !empty($id)) {
				$this->Account->query("UPDATE intranet_accounts SET id_klaviyo = '" . $id . "' WHERE email = '" . $email."'");
			}
		}

	}
}
?>
