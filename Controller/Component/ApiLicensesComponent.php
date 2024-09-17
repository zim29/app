<?php
	class ApiLicensesComponent extends Component {

		public function initialize(Controller $controller) {

        }

        function api_call($action, $params = array(), $method = 'GET') {
            $api_licenses_url = Configure::read('api_licenses_url');
            $api_licenses_key = Configure::read('api_licenses_key');

            if($method == 'GET') {
                $url = $api_licenses_url.$action.'?'.(!empty($params) ? http_build_query($params).'&' : '').'secret='.$api_licenses_key;
                $result = json_decode(file_get_contents($url), true);

                if(empty($result)) {
                    throw new Exception ('Error while connecting with API Licenses, try it later please.');
                }
            } else if($method == 'POST') {
                $url = $api_licenses_url.$action;
                $params['secret'] = $api_licenses_key;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new Exception ('curl error: '.curl_errno($ch));
                }
                curl_close($ch);

                if(!empty($result['error'])) {
                    throw new Exception ('Error while connecting with API Licenses, try it later please.');
                }
            } else if ($method == 'GET-SECRET') {
                $url = $api_licenses_url.$action;
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=".$api_licenses_key);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

                $result = (array)json_decode(curl_exec($ch), true);

                if (curl_errno($ch)) {
                    throw new Exception ('curl error: '.curl_errno($ch));
                }
                curl_close($ch);

                if(!empty($result['error'])) {
                    throw new Exception ('Error while connecting with API Licenses, try it later please.');
                }
            } else if ($method == 'POST-BODY') {
                $url = $api_licenses_url.$action;
                $params['secret'] = $api_licenses_key;
                $data_string = json_encode($params);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
                );

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new Exception ('curl error: '.curl_errno($ch));
                }
                curl_close($ch);

                if(!empty($result['error'])) {
                    throw new Exception ('Error while connecting with API Licenses, try it later please.');
                }
            }
            return $result;
        }

        function get_license_info($support_id) {
		    $license_info = $this->api_call('get-support-info', array('support_id' => $support_id));
		    return $license_info;
        }

        function renew_license($support_id) {
		    $result = $this->api_call('extends-support', array('support_id' => $support_id), 'POST');
		    return $result;
        }

        function add_domain($support_id, $domain) {
		    $result = $this->api_call('add-domain', array('support_id' => $support_id, 'domain_url' => $domain), 'POST');
		    return $result;
        }

        function increase_containers_number($support_id, $quantity) {
		    $result = $this->api_call('increase-workspaces', array('support_id' => $support_id, 'quantity' => $quantity), 'POST');
		    return $result;
        }

        function get_extensions() {
		    $extensions = $this->api_call('products', array(), 'GET-SECRET');
		    return $extensions;
        }

        function get_extension($system_slug, $extension_slug) {
		    $extensions = $this->get_extensions();
		    foreach ($extensions as $key => $system) {
		        if($system['slug'] == $system_slug) {
		            foreach ($system['products'] as $key2 => $extension) {
		                if($extension['extension']['slug'] == $extension_slug) {
		                    $extension['platform_name'] = $system['name'];
		                    $extension['platform_slug'] = $system['slug'];
                            return $extension;
                        }

		            }
                }
		    }
		    throw new Exception ('Extension not found. System: '.$system_slug.' Slug: '.$extension_slug);
        }

        function register_license($params) {
            $result = $this->api_call('buy', $params, 'POST-BODY');
        }

        /* Invoices functions */
        function create_invoice($invoice_data) {
            $invoice = $this->api_call('invoice', $invoice_data, 'POST-BODY');
            return $invoice;
        }
        /* Sales functions */
        function create_sale($sale_data) {
            $invoice = $this->api_call('sale', $sale_data, 'POST-BODY');
            return $invoice;
        }
    }