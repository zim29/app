<?php
/**
  * Modelo Alumnos
  *
  * @author David Nieves
  * @fecha 22/08/2013 18:45:00
  */
    class Sale extends AppModel
  	{
    	var $name = 'Sale';
    	var $useTable = 'sales';

      /*public $hasOne = array(
        'Extension' => array(
          'className' => 'Extensions.Extension',
          'conditions' => array(
            'Extension.oc_extension_id' => 'Sale.extension_id',
          ),
          'foreignKey' => 'extension_id'
        ),
      );*/

      public $belongsTo = array(
          'Extension' => array(
            'foreignKey' => false,
            'conditions' => array(
                'OR' => array(
                    array('Extension.oc_extension_id = Sale.extension_id'),
                    array('Extension.id = Sale.extension_id')
                )
            )
          )
      );

      public function get_sales_by_month($year_month = '', $force_year = false) {
          $force_year = !$force_year ? date('Y') : $force_year;
          $currency_eur = Configure::read('eur_currency_value');

        $results = $this->query("SELECT
            auxiliar.month_date,
            SUM(auxiliar.num_sales) as num_sales,
            ROUND(SUM(auxiliar.total*".$currency_eur."), 2) as euros
            FROM(
                SELECT
                  CONCAT(YEAR(sales.date_added),'-',LPAD(MONTH(sales.date_added), 2, '0')) as month_date,
                  COUNT(*) as num_sales,
                  SUM(total) as total
                FROM intranet_sales sales
                WHERE
                  sales.order_status = 'Complete'
                  AND
                  sales.date_added >= '".$force_year."-01-01'
                  AND
                  sales.date_added <= '".$force_year."-12-31'
                  AND
                  sales.total > 0
                GROUP BY month_date
                UNION ALL

                SELECT
                    CONCAT(YEAR(sales.payed_date),'-',LPAD(MONTH(sales.payed_date), 2, '0')) as month_date,
                    COUNT(*) as num_sales,
                    SUM(total) as total
                FROM intranet_invoices sales
                WHERE
                    sales.state = 'Payed'
                    AND
                    sales.type IN ('Renew','Add domain','New GMT Container','License')
                    AND
                    sales.deleted = 0
                    AND
                    sales.payed_date >= '".$force_year."-01-01'
                    AND
                    sales.payed_date <= '".$force_year."-12-31'
                GROUP BY month_date
            )
            as auxiliar
        GROUP BY auxiliar.month_date
        ORDER BY auxiliar.month_date DESC");

        if(!empty($year_month)) {
            foreach ($results as $result) {
                if($result['auxiliar']['month_date'] == $year_month)
                    return $result[0]['euros'].'€ - '.$result[0]['num_sales'];
            }
        }
        else {
            return $results;
        }
      }

        public function add_additional_information($results) {

          $temp = $this->query('SELECT oc_extension_id,oc_support_months FROM intranet_extensions');
          $extensions = array();
          foreach ($temp as $ext) {
              $extensions[$ext['intranet_extensions']['oc_extension_id']] = $ext['intranet_extensions']['oc_support_months'];
          }


            $sales_month = array();
            $prev_purchase = $this->query('SELECT * FROM intranet_sales_ext_month');
              foreach ($prev_purchase as $key => $val) {
                  $val = $val['intranet_sales_ext_month'];

                  $extension_id = $val['extension_id'];
                  $month_date = $val['month_date'];
                  $num_sales = $val['num_sales'];
                  if(!array_key_exists($month_date, $sales_month))
                      $sales_month[$month_date] = array();

                  $sales_month[$month_date][$extension_id] = $num_sales;
              }

              $past_month = date("Y-m", strtotime("-1 month", strtotime(date('Y-m'))));
              $result = $this->query('SELECT * FROM intranet_sales_ext_month AS Sale');
              $extension_past_month = array();
                foreach ($result as $key => $sale) {
                    if($sale['Sale']['month_date'] == $past_month)
                        $extension_past_month[$sale['Sale']['extension_id']] = $sale['Sale']['num_sales'];
              }



          foreach ($results as $key => $val) {
                $support_expired = true;
                if(array_key_exists('Sale', $val) && isset($val['Sale']['date_added'])) {
                    $purchase_date = !empty($val['Sale']['date_increase']) ? $val['Sale']['date_increase'] : $val['Sale']['date_added'];
                    $date_support_expire = date('Y-m-d H:i:s', strtotime("+" . (!empty($extensions[$val['Sale']['extension_id']]) ? $extensions[$val['Sale']['extension_id']] : 1) . " months", strtotime($purchase_date)));
                    $support_expired = strtotime(date('Y-m-d H:i:s')) > strtotime($date_support_expire);

                    $date_download_expire = date('Y-m-d H:i:s', strtotime("+6 months", strtotime($purchase_date)));
                    $download_expired = strtotime(date('Y-m-d H:i:s')) > strtotime($date_download_expire);

                    $date_support_expire_formated = date('d/m/Y H:i:s', strtotime($date_support_expire));
                    $date_download_expire_formated = date('d/m/Y H:i:s', strtotime($date_download_expire));

                    $results[$key]['Sale']['support_expired'] = $support_expired;
                    $results[$key]['Sale']['download_expired'] = $download_expired;

                    $results[$key]['Sale']['support_expired_message'] = $support_expired ? 'This client can\'t ask for support from ' . $date_support_expire_formated : 'This client can ask for support until ' . $date_support_expire_formated;
                    $results[$key]['Sale']['download_expired_message'] = $download_expired ? 'This client can\'t download new version launched after ' . $date_download_expire_formated : 'This client can download new versions launched before ' . $date_download_expire_formated;

                    $results[$key]['Sale']['media'] = '';

                    $anio_mes = date('Y-m', strtotime($val['Sale']['date_added']));
                    $ext_id = $val['Sale']['extension_id'];
                    $count_sales = array_key_exists($anio_mes, $sales_month) && array_key_exists($ext_id, $sales_month[$anio_mes]) ? $sales_month[$anio_mes][$ext_id] : 0;
                    $results[$key]['Sale']['media'] .= sprintf('Ventas: <b>%s</b>', $count_sales);

                    if(array_key_exists($val['Sale']['extension_id'], $extension_past_month))
                        $results[$key]['Sale']['media'] .= sprintf(' VS <b>%s</b><br>', $extension_past_month[$val['Sale']['extension_id']]);
                    else
                        $results[$key]['Sale']['media'] .= '<br>';

                    $date_added = $val['Sale']['date_added'];

                    $prev_purchase = $this->query('SELECT date_added,order_id FROM intranet_sales WHERE marketplace = "'.$val['Sale']['marketplace'].'" AND date_added < "' . $date_added . '" ORDER BY date_added DESC LIMIT 1');
                    if (!empty($prev_purchase)) {
                        $prev_purchase = $prev_purchase[0]['intranet_sales'];
                        $diferencia = (int)str_replace(array('of-', 'ML', 'isenselabs-'), '', $val['Sale']['order_id']) - (int)str_replace(array('of-', 'ML', 'isenselabs-'), '', $prev_purchase['order_id']);

                        $t1 = strtotime($val['Sale']['date_added']);
                        $t2 = strtotime($prev_purchase['date_added']);
                        $diff = $t1 - $t2;
                        $hours = number_format($diff / (60 * 60), 2);

                        $media = $hours < 1 ? '-' : number_format($diferencia / $hours, 2);

                        $results[$key]['Sale']['media'] .= sprintf('Última compra hace <b>%s</b> horas.<br>Compras en periodo <b>%s</b>.<br>Media en periodo <b>%s</b>', $hours, $diferencia, $media);
                    }
                }
            }
            return $results;
        }
      public function update_version($sale_id, $version)
      {
          $this->recursive = -1;
          $versions = $this->find('first', array('conditions' => array('Sale.order_id' => $sale_id)));
          $final_version = '';
          $current_version = $versions['Sale']['system_version'];
          if (empty($current_version)) {
              $final_version = $version;
          } else {
              $current_version = explode('|', $current_version);

              if (!in_array($version, $current_version))
                  $current_version[] = $version;

              $final_version = implode('|', $current_version);
          }

          $this->query('UPDATE intranet_sales SET system_version = "'.$final_version.'" WHERE order_id = "'.$sale_id.'"');
      }
      public function increase_get_form($sale_id) {
          $this->query('UPDATE intranet_sales SET recovered_form_last_date = "'.date('Y-m-d H:i:s').'", recovered_form = recovered_form+1 WHERE order_id = "'.$sale_id.'"');
      }

	  public function sale_status($sale) {
		  $status = array();

		  $temp = $this->query('SELECT oc_support_months FROM intranet_extensions WHERE oc_extension_id = "'.$sale['extension_id'].'"');
		  if(!empty($temp[0]['intranet_extensions']['oc_support_months'])) {
			  $support_months = $temp[0]['intranet_extensions']['oc_support_months'];
			  $purchase_date = !empty($sale['date_increase']) ? $sale['date_increase'] : $sale['date_added'];
			  $date_support_expire = date('Y-m-d H:i:s', strtotime("+" . $support_months . " months", strtotime($purchase_date)));
			  $support_expired = strtotime(date('Y-m-d H:i:s')) > strtotime($date_support_expire) ? $date_support_expire : false;
			  $status['expired_date'] = $support_expired;
			  $status['support_end_date'] = $date_support_expire;
			  if(!empty($support_expired))
				  $status['link_renew'] = Router::url('/', true).'invoices/opencart/new_invoice?type=renew_license&license_id='.$sale['order_id'];
		  }
		  return $status;
	  }

		public function beforeSave($options = array()) {
			//parent::beforeSave();

			App::uses('CakeSession', 'Model/Datasource');
			$is_logged = CakeSession::read('logged');

			if($is_logged)
				$this->data['Sale']['id_account'] = $is_logged;
			else if(empty($this->data['Sale']['id_account']) && !empty($this->data['Sale']['buyer_email'])) {
				$accountModel = ClassRegistry::init('Accounts.Account');

				$exist_account = $accountModel->findByEmail($this->data['Sale']['buyer_email']);
				if(!empty($exist_account['Account']['id'])) {
					$this->data['Sale']['id_account'] = $exist_account['Account']['id'];
				} else {

					$accountModel->validator()->remove('name');
					$accountModel->validator()->remove('address');
					$accountModel->validator()->remove('country_id');
					$accountModel->validator()->remove('zone_id');
					$accountModel->validator()->remove('city');
					$accountModel->validator()->remove('post_code');
					$accountModel->validator()->remove('password');
					//$accountModel->validator()->remove('email');
					$accountModel->validator()->remove('email_confirm');

					$temp = array(
						'Account' => array(
							"email" => $this->data['Sale']['buyer_email'],
							"name" => $this->data['Sale']['buyer_username'],
							'password' => $this->_generate_random_string()
						)
					);

					$accountModel->save($temp);

					$errors = $accountModel->validationErrors;
					$account_id = $accountModel->getLastInsertID();
					$this->data['Sale']['id_account'] = $account_id;
				}
			}
			return true;
		}
  	}
?>
