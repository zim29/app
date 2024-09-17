<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
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

	function beforeSave($options = array()) {
        if ($this->alias == 'Invoice' && empty($this->data['Invoice']['id'])) {
            $this->data['Invoice']['currency_euro_value'] = Configure::read('eur_currency_value');
            if (!empty($this->data['Invoice']['customer_country_id']) && $this->data['Invoice']['customer_country_id'] != 195 && !empty($this->data['Invoice']['customer_vat'])) {
	            $this->data['Invoice']['tax'] = 0;
	        }
        }
        return true;
    }

    function get_domain($url)
    {
        $disallowed = array('http://', 'https://', 'www.');
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                $url = str_replace($d, '', $url);
            }
        }
        $url = rtrim($url, '/').'/';
        return $url;
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
