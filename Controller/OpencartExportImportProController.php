<?php
	class OpencartExportImportProController extends AppController {
		public $uses = array(
            'Extensions.Extension',
            'Sales.Sale'
		);

		public $components = array(
            'Session',
            'OpencartFormGenerator'
		);

		public function beforeFilter() {
		    $this->cron_id = '5bcae3af-5d90-4d24-be47-05d3fa641b0a';
		    $this->custom_fields_id = '5bcae3af-5d90-4d24-be47-05jm7yh4fvi0';
	        $this->Auth->allow('cron_get_purchase_message', 'cron_purchase', 'cron_get_form', 'cron_validate_license', 'custom_fields_get_purchase_message', 'custom_fields_purchase', 'custom_fields_get_form', 'custom_fields_validate_license');
	        $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
		}

		public function custom_fields_get_purchase_message($return_render = false) {
		    $extension = $this->Extension->findById($this->custom_fields_id);
		    $this->layout = 'ajax';
		    $this->set('ext', $extension);
		    $this->set('lang', $this->request->data['lang']);

		    if($return_render)
		        return $this->render('custom_fields_get_purchase_message');
        }

        public function custom_fields_purchase($units = 1) {
		    $this->cron_purchase($units, $this->custom_fields_id);
        }

        public function custom_fields_get_form() {
		    $this->cron_get_form($this->custom_fields_id);
        }

        public function custom_fields_validate_license() {
            $this->cron_validate_license(true);
        }

		public function cron_get_purchase_message($return_render = false, $force_license = false) {
		    $extension = $this->Extension->findById($force_license ? $force_license : $this->cron_id);
		    $this->layout = 'ajax';
		    $this->set('ext', $extension);
		    $this->set('lang', $this->request->data['lang']);

		    if(($return_render && !$force_license) || ($return_render && $force_license == '5bcae3af-5d90-4d24-be47-05d3fa641b0a'))
		        return $this->render('cron_get_purchase_message');
		    if($return_render && $force_license == '5bcae3af-5d90-4d24-be47-05jm7yh4fvi0')
		        return $this->render('custom_fields_get_purchase_message');
        }

        public function cron_purchase($units = 1, $force_license = false) {
		    $this->layout = 'frontend';
		    $get_params = array(
		        'auto_add_extension_id' => !$force_license ? $this->cron_id : $force_license,
		        'auto_add_units' => $units
            );
		    $this->Session->write('auto_product_add', $get_params);
            $this->redirect(array('plugin' => false, 'controller' => 'pages', 'action' => 'display', 'cart', '?' => $get_params));
        }

        public function cron_get_form($force_license = false) {
		    $this->layout = 'ajax';
		    $domain = $this->get_domain($this->request->data['domain']);
            $is_demo = strpos($this->request->data['domain'], 'devmanextensions.com') !== false;

		    $conditions = array(
		        'extension_id' => !$force_license ? $this->cron_id : $force_license,
                'domain LIKE "%'.$domain.'%"'
            );


		    $sale = $this->Sale->find('first', array('conditions' => $conditions));

		    $domain_found = !empty($sale['Sale']['domain']) && in_array($domain, explode('|', $sale['Sale']['domain']));

            if(!$domain_found) {
                $this->set('lang', $this->request->data['lang']);
                $this->set('domain', $domain);
                $this->set('purchase_form', $this->cron_get_purchase_message(true, $force_license));
                $this->set('key_word', !$force_license ? 'cron' : ($force_license == '5bcae3af-5d90-4d24-be47-05d3fa641b0a' ? 'cron' : 'custom_fields'));
                $this->render('form_validate');
            } else {
                $this->Extension->recursive = 1;
                $extension = $this->Extension->find('first', array('conditions' => array('Extension.id' => !$force_license ? $this->cron_id : $force_license)));
                $last_version = $extension['Changelog'][0]['version'];
                $current_version = $this->request->data['current_version'];

                $form_html = '';
                if(version_compare($last_version, $current_version, '>')) {
                    $link_download = Router::url('/', true).'download-center?download_id='.$sale['Sale']['download_id'];
                    if(!$is_demo) {
                        $message = sprintf('New available version <b>%s</b>, <a target="_blank" href="%s">download here</a>.', $last_version, $link_download);
                        $form_html .= '<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i>' . $message . '<button type="button" class="close" data-dismiss="alert">×</button></div>';
                    }
                }

                $form_decoded = json_decode($this->request->data['config_table'], true);
                $this->oc_2 = true;

                if($force_license && in_array($domain, array('opencart.devmanextensions.com/import_export_pro/', 'opencart.devmanextensions.com/ru-ie-pro/'))) {
                    $field_array = array(
                        'type' => 'html_hard',
                        'html_code' => '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <b>IMPORTANT MESSAGE:</b> This is an <b>optional extra complement</b> to clients that need this functionality, can be purchased by <b>'.(!$force_license ? '$20' : '$30').'</b> from this tab, after install "Import/Export PRO" in your store.<button type="button" class="close" data-dismiss="alert">×</button></div>'
                    );
                    $form_html .= $this->OpencartFormGenerator->generateField($field_array, null, true);
                }
                foreach ($form_decoded as $key => $field_array) {
                    $form_html .= $this->OpencartFormGenerator->generateField($field_array, null, true);
                }

                echo $form_html; die;
            }
        }

        public function cron_validate_license($is_custom_fields = false) {
            $domain = array_key_exists('domain', $this->request->data) ? $this->request->data['domain'] : '';
            $license_id = array_key_exists('license_id', $this->request->data) ? trim($this->request->data['license_id']) : '';
            $lang = array_key_exists('lang', $this->request->data) ? $this->request->data['lang'] : '';

            $array_return = array('error' => false, 'message' => 'License validated! refreshing page...');

            try {
                $conditions = array(
                    'Sale.order_id' => $license_id,
                    'Sale.extension_id' => !$is_custom_fields ? $this->cron_id : $this->custom_fields_id
                );
                $sale = $this->Sale->find('first', array('conditions' => $conditions));

                if(empty($sale))
                    throw new Exception('License not found.');

                $domains = !empty($sale['Sale']['domain']) ? $sale['Sale']['domain'].'|'.$domain : $domain;
                $domain_exploded = explode('|', $sale['Sale']['domain']);
                if(!empty($sale['Sale']['domain']) && !in_array($domain, $domain_exploded))
                    throw new Exception('License has already a domain assigned.');

                $this->Sale->query('UPDATE intranet_sales SET domain = "'.$domains.'" WHERE order_id = "'.$license_id.'"');
            } catch (Exception $e) {
                $array_return['error'] = true;
                $array_return['message'] = $e->getMessage();
            }
            echo json_encode($array_return); die;
        }
	}
?>