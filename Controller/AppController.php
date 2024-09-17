<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    var $layout = 'administration';

    public $components = array(
        'Session',
        'Seguridad',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'tickets', 'action' => 'tickets'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'authError' => 'You must be logged in to view this page.',
            'loginError' => 'Invalid Username or Password entered, please try again.'

        ));

    public $helpers = array(
        'Form' => array('className' => 'Bs3Helpers.Bs3Form'),
        'FormTool'
    );

    // only allow the login controllers only
    public function beforeFilter() {
        $this->Auth->allow('login', 'display');

        //Language config - David Nieves
            if (isset($this->request->data['lang_code']))
                $this->set('lang_code', $this->request->data['lang_code']);
            else
                $this->set('lang_code', 'en');
    }

    public function isAuthorized($user) {
        // Here is where we should verify the role and give access based on role
        return true;
    }

    function beforeRender() {
      if($this->name == 'CakeError') {
        $this->layout = 'frontend';
      }

      if($this->layout == 'administration')
      {
        $OpencartExtension = $this->Components->load('OpencartExtension');
        $waiting_extensions_count = $OpencartExtension->count_extensions_waiting();
        $this->Session->write('waiting_extensions_count', $waiting_extensions_count);
      } else {
		  if($this->Session->read("logged")) {
			  $accountModel = ClassRegistry::init('Accounts.Account');
			  $accountModel->recursive = -1;
			  $account = $accountModel->findById($this->Session->read("logged"));
			  $datalayerCode = $this->Session->read("datalayerCode");

			  $user_data = array(
				  'name' => $account['Account']['name'],
				  'email' => $account['Account']['email'],
			  );

			  $datalayerCode .= '
                    <script>
                    	var dataLayer = [];
                        dataLayer.push({
                            "userData": ' . json_encode($user_data) . '
                        });
                    </script>
                ';
			  $this->Session->write("datalayerCode", $datalayerCode);
		  }

		  $datalayerCode = $this->Session->read("datalayerCode");
		  $this->Session->write("datalayerCode", '');
		  $this->set("datalayerCode", $datalayerCode);


		  $richsnippets = $this->Session->read("richsnippets");
		  $this->Session->write("richsnippets", '');
		  $this->set("richsnippets", $richsnippets);

	  }
    }
    function get_domain($url)
    {
        $url = trim($url);
        $disallowed = array('http://', 'https://', 'www.');
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                $url = str_replace($d, '', $url);
            }
        }
        $url = rtrim($url, '/').'/';
        return $url;
    }

	public function aasort ($array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		return $ret;
	}

	public function array_group_by(array $array, $key)
	{
		if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
			trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
			return null;
		}

		$func = (!is_string($key) && is_callable($key) ? $key : null);
		$_key = $key;

		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value) {
			$key = null;

			if (is_callable($func)) {
				$key = call_user_func($func, $value);
			} elseif (is_object($value) && property_exists($value, $_key)) {
				$key = $value->{$_key};
			} elseif (isset($value[$_key])) {
				$key = $value[$_key];
			}

			if ($key === null) {
				continue;
			}

			$grouped[$key][] = $value;
		}

		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();

			foreach ($grouped as $key => $value) {
				$params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $params);
			}
		}

		return $grouped;
	}
	public function _generate_random_string($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
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
