<?php
	class GmtController extends AppController {

		public  $uses = array(
      		'Sales.Sale',
            'Extensions.Extension',
            'Changelogs.Changelog'
		);

		public function beforeFilter() {
	        $this->Auth->allow('get_workspace', 'exam_wokspace', 'check_new_version');
	        $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
	    }

	    public $components = array(
		    'OpencartExtension',
		    'Email'
		);

        /**
         * @throws Exception
         */
        function get_workspace()
		{
			$array_return = array(
				'error' => false,
				'message' => ''
			);

			if(!empty($this->request->data))
			{

				$pe_licenses = array();
                $from_prestashop = array_key_exists('submitgooglemarketingtools', $this->request->data);
                $from_woocommerce = array_key_exists('wc-gmt_tag_manager_status', $this->request->data);
                $from_magento = array_key_exists('ordernumber', $this->request->data);
                $from_opencart = !$from_prestashop && !$from_woocommerce && !$from_magento;

                $domain = array_key_exists('domain_workspace', $this->request->data) ? $this->request->data['domain_workspace'] : '';

                $workspace_name = 'workspace_gHxJ8DSX.json';
                $container_id_name = 'GTM-MBXLVT9';

                $ga4_measurement_id = '';

                if($from_opencart) {

					if(!empty($this->request->data['google_all_google_version']) && version_compare($this->request->data['google_all_google_version'], '12.5.15', '>=')) {
						/*
						<pre>Array
						(
							[google_version] => 12.5.15
							[license_id] => ML000488
							[container_id_workspace] => GTM-WJ6QP576
							[include_shipping_cost_in_total] => 1
							[ga4_status] => 1
							[ga4_measurement_id] =>
							[enhanced_ecommerce_status] => 1
							[google_ads_conversion_status] => 1
							[google_ads_conversion_id] =>
							[google_ads_conversion_label] =>
							[google_ads_cs_conversion_status] => 1
							[google_ads_cs_conversion_id] =>
							[google_ads_cs_conversion_label] =>
							[google_ads_cs_conversion_route] =>
							[google_ads_cs_conversion_value] =>
							[google_ads_ct_conversion_status] => 1
							[google_ads_ct_conversion_id] =>
							[google_ads_ct_conversion_label] =>
							[google_ads_ct_conversion_route] =>
							[google_ads_ct_conversion_value] =>
							[google_reviews_status] => 1
							[google_reviews_merchant_id] =>
							[google_reviews_style] => CENTER_DIALOG
							[google_reviews_delivery_days] => 3
							[dynamic_remarketing_status] => 1
							[dynamic_remarketing_id] =>
							[dynamic_remarketing_label] =>
							[dynamic_remarketing_id_prefix] =>
							[dynamic_remarketing_id_sufix] =>
							[google_reviews_badge_code_status] => 1
							[google_reviews_badge_id] =>
							[rich_snippets_status] => 1
							[facebook_pixel_status] => 1
							[facebook_pixel_id] => 23452343
							[facebook_pixel_token] => asdfasdfasdfadsf
							[tiktok_pixel_status] => 1
							[tiktok_pixel_id] =>
							[hotjar_status] => 1
							[hotjar_site_id] =>
							[pinterest_status] => 1
							[pinterest_id] =>
							[crazyegg_status] => 1
							[crazyegg_id] =>
							[criteo_status] => 1
							[criteo_id] =>
							[bing_ads_status] => 1
							[bing_ads_tag_id] =>
							[yahoo_status] => 1
							[yahoo_project_id] =>
							[yahoo_pixel_id] =>
							[domodi_pixel_status] => 1
							[domodi_pixel_id] =>
							[stileo_pixel_status] => 1
							[stileo_pixel_id] =>
							[skroutz_analytics_status] => 1
							[skroutz_analytics_shop_id] =>
							[klaviyo_status] => 1
							[klaviyo_api_key] =>
							[linkedin_status] => 1
							[linkedin_partner_id] => 23452345
							[linkedin_purcharse_conversion_id] => 234523
							[linkedin_add_to_cart_conversion_id] => dsfgwdsfg
							[linkedin_wishlist_conversion_id] =>
							[linkedin_promotion_click_conversion_id] =>
							[linkedin_search_conversion_id] =>
							[linkedin_start_checkout_conversion_id] =>
							[linkedin_conversion_id_placeholder] =>
							[twitter_status] => 1
							[twitter_add_to_cart_event_id] =>
							[twitter_wishlist_event_id] =>
							[twitter_checkout_initiated_event_id] =>
							[twitter_content_view_event_id] =>
							[twitter_purcharse_event_id] =>
							[twitter_search_event_id] =>
							[twitter_pixel_id] =>
							[mailchimp_status] => 1
							[mailchimp_dc_or_server] =>
							[mailchimp_api_key] =>
							[mailchimp_list_id] =>
							[domain_workspace] => phpstorm
						)
						</pre>
						 * */

						//Cleaning settings variables
							$config = array();
							foreach ($this->request->data as $key => $value) {
								preg_match('/\d+$/', $key, $matches);
								$shop_id = isset($matches[0]) ? intval($matches[0]) : '';

								if($shop_id !== '')
									$key_name = str_replace('_' . $shop_id, '', $key);
								else $key_name = $key;

								$key_name = str_replace('google_all_', '', $key_name);

								$config[$key_name] = trim($value);
							}

							if(!array_key_exists("google_reviews_badge_code_style", $config))
								$config['google_reviews_badge_code_style'] = '';

						//Control licencia
							$license_id = !empty($config['license_id']) ? $config['license_id'] : '';
							if(empty($license_id))
								$this->die_error('Fill License Order ID');

						//Control workspace
							$container_id = !empty($config['container_id_workspace']) ? $config['container_id_workspace'] : '';
							if(empty($container_id))
								$this->die_error('Fill Google Tag Manager container ID');

						//Control licencia
							$license_result = $this->check_license($license_id);

							if(empty($license_result['Sale']))
								$this->die_error($license_result);

							$license_uses_result = $this->check_license_uses($license_result, $container_id);
							if($license_uses_result)
								$this->die_error($license_uses_result);

						//Control última versión
							$last_version = $this->Changelog->find('first', array('conditions' => array('Changelog.id_extension' => "5420686f-9450-4afa-a9c1-0994fa641b0a"), 'order' => array('Changelog.created' => 'DESC')));
							$last_version = $last_version['Changelog']['version'];

							if(version_compare($config['google_version'], $last_version, "<"))
								$this->die_error(sprintf('Google Marketing Tools version deprecated, install latest available version: <b>%s</b>', $last_version));

						//Obtenemos variables de flowytracking
							// Initialize cURL session
							$ch = curl_init();

							// Set the URL
							$url = "https://dash.flowytracking.com/api/tags";

							// Set cURL options
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

							// Execute the GET request
							$response = curl_exec($ch);

							// Check for errors
							if(curl_errno($ch)) {
								echo 'Error getting workspace FT';
							}

							// Close cURL session
							curl_close($ch);

							$ft_variables = json_decode($response, true);




						//Macheamos información
							$tag_statuses = array(
								'ga4_status' => array(
									'ft_key' => 'ga4',
									'config_keys' => array(
										'ga4_measurement_id' => array(
											'ft_key' => 'GA4MEASUREMENTID',
											'error_empty' => 'Error: Tag "<b>GA4</b>" - "Measurement ID" required'
										)
									)
								),

								'enhanced_ecommerce_status' => array(
									'ft_key' => 'ga4ee',
								),

								'google_ads_conversion_status' => array(
									'ft_key' => 'ads',
									'config_keys' => array(
										'google_ads_conversion_id' => array(
											'ft_key' => 'IDCONVERSION',
											'error_empty' => 'Error: Tag "<b>Google Ads conversion</b>" - "Ads conversion ID*" required',
											'only_numbers' => 'Error: Tag "<b>Google Ads conversion</b>" - "Ads conversion ID*" only numbers'
										),
										'google_ads_conversion_label' => array(
											'ft_key' => 'LABELCONVERSION',
											'error_empty' => 'Error: Tag "<b>Google Ads conversion</b>" - "Ads conversion label*" required'
										),
									)
								),

								'google_ads_cs_conversion_status' => array(
									'ft_key' => 'ads2',
									'config_keys' => array(
										'google_ads_cs_conversion_id' => array(
											'ft_key' => 'IDCONVERSION2',
											'error_empty' => 'Error: Tag "<b>Google Ads custom secondary conversion</b>" - "Ads conversion ID*" required',
											'only_numbers' => 'Error: Tag "<b>Google Ads custom secondary conversion</b>" - "Ads conversion ID*" only numbers'
										),

										'google_ads_cs_conversion_label' => array(
											'ft_key' => 'LABELCONVERSION2',
											'error_empty' => 'Error: Tag "<b>Google Ads custom secondary conversion</b>" - "Ads conversion label*" required'
										),

										'google_ads_cs_conversion_route' => array(
											'ft_key' => 'ROUTECONVERSION2',
											'error_empty' => 'Error: Tag "<b>Google Ads custom secondary conversion</b>" - "Route to fire secondary conversion" required'
										),

										'google_ads_cs_conversion_value' => array(
											'ft_key' => 'VALUECONVERSION2',
											'error_empty' => 'Error: Tag "<b>Google Ads custom secondary conversion</b>" - "Value" required'
										),
									)
								),

								'google_ads_ct_conversion_status' => array(
									'ft_key' => 'ads3',
									'config_keys' => array(
										'google_ads_ct_conversion_id' => array(
											'ft_key' => 'IDCONVERSION3',
											'error_empty' => 'Error: Tag "<b>Google Ads custom tertiary conversion</b>" - "Ads conversion ID*" required',
											'only_numbers' => 'Error: Tag "<b>Google Ads custom tertiary conversion</b>" - "Ads conversion ID*" only numbers'
										),

										'google_ads_ct_conversion_label' => array(
											'ft_key' => 'LABELCONVERSION3',
											'error_empty' => 'Error: Tag "<b>Google Ads custom tertiary conversion</b>" - "Ads conversion label*" required'
										),

										'google_ads_ct_conversion_route' => array(
											'ft_key' => 'ROUTECONVERSION3',
											'error_empty' => 'Error: Tag "<b>Google Ads custom tertiary conversion</b>" - "Route to fire tertiary conversion" required'
										),

										'google_ads_ct_conversion_value' => array(
											'ft_key' => 'VALUECONVERSION3',
											'error_empty' => 'Error: Tag "<b>Google Ads custom tertiary conversion</b>" - "Value" required'
										),
									)
								),

								'google_reviews_status' => array(
									'ft_key' => 'reviews',
									'config_keys' => array(
										'google_reviews_merchant_id' => array(
											'ft_key' => 'GOOGLE_REVIEW_MERCHANT_ID',
											'error_empty' => 'Error: Tag "<b>Google Reviews</b>" - "Merchant ID" required'
										),
										'google_reviews_style' => array(
											'ft_key' => 'GOOGLE_REVIEW_STYLE',
											'error_empty' => 'Error: Tag "<b>Google Reviews</b>" - "Style" required'
										),
										'google_reviews_delivery_days' => array(
											'ft_key' => 'GOOGLE_REVIEW_DELIVERY_DATE',
											'error_empty' => 'Error: Tag "<b>Google Reviews</b>" - "Estimated days" required'
										),
									)
								),

								'dynamic_remarketing_status' => array(
									'ft_key' => 'adsr',
									'config_keys' => array(
										'dynamic_remarketing_id' => array(
											'ft_key' => 'DYNAMICREMARKETINGCONID',
											'error_empty' => 'Error: Tag "<b>Standard/Dynamic Remarketing</b>" - "Conversion ID" required'
										),
										'dynamic_remarketing_label' => array(
											'ft_key' => 'DYNAMICREMARKETINGCONLABEL',
										),
										'dynamic_remarketing_id_prefix' => 'PREFIXPRODUCTID',
										'dynamic_remarketing_id_sufix' => 'SUFIXPRODUCTID',
										'dynamic_remarketing_dynx' => 'DYNXSTATUS',
									)
								),

								'google_reviews_badge_code_status' => array(
									'ft_key' => 'reviews_badge',
									'config_keys' => array(
										'google_reviews_badge_id' => array(
											'ft_key' => 'GOOGLE_BADGE_MERCHANT_ID',
											'error_empty' => 'Error: Tag "<b>Google Reviews badge</b>" - "Merchant ID" required'
										),
										'google_reviews_badge_code_style' => array(
											'ft_key' => 'GOOGLE_BADGE_STYLE',
											'default_value' => 'BOTTOM_RIGHT',
											'error_empty' => 'Error: Tag "<b>Google Reviews badge</b>" - "Style" required'
										),
									)
								),

								'rich_snippets_status' => array(
									'ft_key' => 'rich_snippets',
								),

								'facebook_pixel_status' => array(
									'ft_key' => 'fbapic',
									'config_keys' => array(
										'facebook_pixel_id' => array(
											'ft_key' => 'FB_PIXEL_ID',
											'error_empty' => 'Error: Tag "<b>Facebook API Conversions</b>" - "Pixel ID" required'
										),
										'facebook_pixel_token' => array(
											'ft_key' => 'FB_API_TOKEN',
											'error_empty' => 'Error: Tag "<b>Facebook API Conversions</b>" - "Access token" required'
										),
									)
								),

								'tiktok_pixel_status' => array(
									'ft_key' => 'ttpixel',
									'config_keys' => array(
										'tiktok_pixel_id' => 'TIKTOK_PIXEL_ID'
									)
								),

								'hotjar_status' => array(
									'ft_key' => 'hotjar',
									'config_keys' => array(
										'hotjar_site_id' => 'HOTJARSITEID'
									)
								),

								'pinterest_status' => array(
									'ft_key' => 'pinterest',
									'config_keys' => array(
										'pinterest_id' => 'PINTERESTPIXELID'
									)
								),

								'crazyegg_status' => array(
									'ft_key' => 'crazyegg',
									'config_keys' => array(
										'crazyegg_id' => 'CRAZYEGGID'
									)
								),

								'criteo_status' => array(
									'ft_key' => 'criteo',
									'config_keys' => array(
										'criteo_id' => 'CRITEOPARTNERID'
									)
								),

								'bing_ads_status' => array(
									'ft_key' => 'bing',
									'config_keys' => array(
										'bing_ads_tag_id' => 'BING_ADS_UET_TAG_ID'
									)
								),

								'yahoo_status' => array(
									'ft_key' => 'yahoo',
									'config_keys' => array(
										'yahoo_project_id' => 'YAHOO-PROJECT-ID',
										'yahoo_pixel_id' => 'YAHOO-PIXEL-ID'
									)
								),

								'domodi_pixel_status' => array(
									'ft_key' => 'domodi',
									'config_keys' => array(
										'domodi_pixel_id' => 'DOMODI_SHOP_KEY',
									)
								),

								'stileo_pixel_status' => array(
									'ft_key' => 'stileo',
									'config_keys' => array(
										'stileo_pixel_id' => 'STILEO_API_KEY',
									)
								),

								'skroutz_analytics_status' => array(
									'ft_key' => 'skroutz',
									'config_keys' => array(
										'skroutz_analytics_shop_id' => 'SKROUTZ_ACCOUNT_ID',
									)
								),

								'klaviyo_status' => array(
									'ft_key' => 'klaviyo',
									'config_keys' => array(
										'klaviyo_api_key' => 'KCOMPANYID',
									)
								),

								'linkedin_status' => array(
									'ft_key' => 'linkedin',
									'config_keys' => array(
										'linkedin_partner_id' => '5375108',
										'linkedin_purcharse_conversion_id' => '14147132',
										'linkedin_add_to_cart_conversion_id' => '14147124',
										'linkedin_wishlist_conversion_id' => '14147156',
										'linkedin_promotion_click_conversion_id' => '14147148',
										'linkedin_search_conversion_id' => '14147172',
										'linkedin_start_checkout_conversion_id' => '14147140',
									)
								),

								'twitter_status' => array(
									'ft_key' => 'twitter',
									'config_keys' => array(
										'twitter_add_to_cart_event_id' => 'tw-ogo25-ogom4',
										'twitter_wishlist_event_id' => 'tw-ogo25-ogo2f',
										'twitter_checkout_initiated_event_id' => 'tw-ogo25-ogo2l',
										'twitter_content_view_event_id' => 'tw-ogo25-ogo2m',
										'twitter_purcharse_event_id' => 'tw-ogo25-ogo2o',
										'twitter_search_event_id' => 'tw-ogo25-ogo2q',
										'twitter_pixel_id' => 'TPXID',
									)
								),

								'mailchimp_status' => array(
									'ft_key' => 'mailchimp',
									'config_keys' => array(
										'mailchimp_dc_or_server' => 'MSERVER',
										'mailchimp_api_key' => 'MAILCAPIKEY',
										'mailchimp_list_id' => 'MLISTID',
									)
								),

								'kelkoo_status' => array(
									'ft_key' => 'kelkoo',
									'config_keys' => array(
										'kelkoo_country_code' => 'KELKOO_COUNTRY_CODE',
										'kelkoo_merchant_id_value' => 'KELKOO_MARCHANT_ID_VALUE',
									)
								),

							);

							$ft_workspace = array(
								'api_key' => '3a655768-281f-3694-a108-ea4fdb5c9d2f',
								'ids' => array(
									'tags' => array(),
									'variables' => array(),
									'triggers' => array(),
									'other_params' => array(),
									'other_variables' => array(),

								),
								'tags' => array(
									'include_shipping_costs' => !empty($config['include_shipping_cost_in_total']) ? 'true' : 'false'
								),
							);


							foreach ($tag_statuses as $key => $ft_key) {
								$tag_info = $tag_statuses[$key];

								if(empty($config[$key])) {
									$ft_key = $tag_info['ft_key'];
									$ft_info = $ft_variables[$ft_key];

									if(!empty($ft_info['variables']))
										$ft_workspace['ids']['variables'] = array_merge($ft_workspace['ids']['variables'], $ft_info['variables']);

									if(!empty($ft_info['triggers'])) {
										$ft_workspace['ids']['triggers'] = array_merge($ft_workspace['ids']['triggers'], $ft_info['triggers']);
									}

									if(!empty($ft_info['tags']))
										$ft_workspace['ids']['tags'] = array_merge($ft_workspace['ids']['tags'], $ft_info['tags']);

									if(!empty($ft_info['other_variables']))
										$ft_workspace['ids']['other_variables'] = array_merge($ft_workspace['ids']['other_variables'], $ft_info['other_variables']);

									if(!empty($ft_info['other_params']))
										$ft_workspace['ids']['other_params'] = array_merge($ft_workspace['ids']['other_params'], $ft_info['other_variables']);
								} else {
									if(!empty($tag_info['config_keys'])) {
										foreach ($tag_info['config_keys'] as $oc_key => $ft_key) {
											$allow_empty = false;

											if(is_array($ft_key)) {

												if($config[$oc_key] === '' && !empty($ft_key['error_empty']) && empty($ft_key['default_value']))
													$this->die_error($ft_key['error_empty']);
												else if($config[$oc_key] === '' && !empty($ft_key['default_value'])) {
													$config[$oc_key] = $ft_key['default_value'];
												}

												if(!empty($ft_key['only_numbers']) && !is_numeric($config[$oc_key]))
													$this->die_error($ft_key['only_numbers']);

												$ft_key = $ft_key['ft_key'];
											}

											if($oc_key == 'dynamic_remarketing_dynx') {
												if(empty($config['dynamic_remarketing_dynx']))
													$ft_workspace['tags'][$ft_key] = 'FALSE';
												else
													$ft_workspace['tags'][$ft_key] = 'TRUE';
											} else if($oc_key == 'dynamic_remarketing_id_prefix' && empty($config['dynamic_remarketing_id_prefix'])) {
													$ft_workspace['tags'][$ft_key] = 'EMPTY';
											} else if($oc_key == 'dynamic_remarketing_id_sufix' && empty($config['dynamic_remarketing_id_sufix'])) {
												$ft_workspace['tags'][$ft_key] = 'EMPTY';
											} else
												$ft_workspace['tags'][$ft_key] = !empty($config[$oc_key]) ? $config[$oc_key] : '';
										}
									}
								}
							}
						//Obtenemos workspace
							$url = 'https://dash.flowytracking.com/api/generate-workspace';

							$jsonData = json_encode($ft_workspace);

							$ch = curl_init($url);
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
							curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								'Content-Type: application/json',
								'Content-Length: ' . strlen($jsonData)
							));
							$workspace_json = curl_exec($ch);
							curl_close($ch);

							json_decode($workspace_json);
							$is_json = json_last_error() == JSON_ERROR_NONE;

							if(!$is_json)
								$this->die_error('Error obtaining json. Put in contact with support.');


						//Generate workspace file
							$file_path = 'gmt_workspace/exported/'.$license_id.'-'.$container_id.'.json';
							$file = fopen($file_path,'w+');
							$writed = fwrite($file,$workspace_json);
							fclose($file);
							if($this->send_gtm_workspace($license_result, $file_path, $domain))
							{
								$array_return['message'] = sprintf('<b>IMPORTANT: MAKE SURE THAT YOU HAVE THE LAST GMT VERSION ELSE MAYBE YOUR WORKSPACE GENERATE WON\'T BE COMPATIBLE.</b><br>Google Tag Manager Workspace was sent to <b>%s</b>, wait 1 minute, also check your SPAM folder. Press save/apply now to don\'t change your configuration.', $license_result['Sale']['buyer_email']);
								echo json_encode($array_return); die;
							}
							else
								$this->die_error('Error sending Workspace file');
					}


                    $last_version = $this->Changelog->find('first', array('conditions' => array('Changelog.id_extension' => "5420686f-9450-4afa-a9c1-0994fa641b0a"), 'order' => array('Changelog.created' => 'DESC')));
                    $last_version = $last_version['Changelog']['version'];


                    if(version_compare($this->request->data['google_all_google_version'], $last_version, "<"))
                        $this->die_error(sprintf('Google Marketing Tools version deprecated, install latest available version: <b>%s</b>', $last_version));

                    $config = array();
                    foreach ($this->request->data as $key => $value) {
                        $shop_id = preg_replace('/\D+/', '', $key);
                        if($shop_id !== '')
                            $key_name = str_replace('_' . $shop_id, '', $key);
                        else $key_name = $key;

                        $config[$key_name] = trim($value);
                    }

                    $all_prefix = array_key_exists('google_all_license_id', $config) ? '_all' : '';

                    $license_id = !empty($config['google'.$all_prefix.'_license_id']) ? $config['google'.$all_prefix.'_license_id'] : '';

                    $container_id = !empty($config['google'.$all_prefix.'_container_id']) ? $config['google'.$all_prefix.'_container_id'] : '';

                    if(empty($container_id))
                        $container_id = !empty($config['google'.$all_prefix.'_container_id_workspace']) ? $config['google'.$all_prefix.'_container_id_workspace'] : '';

                    $container_optimize_id = !empty($config['google'.$all_prefix.'_container_optimize_id']) ? $config['google'.$all_prefix.'_container_optimize_id'] : '';

                    //$ga_ua = !empty($config['google'.$all_prefix.'_analytics_ua']) ? $config['google'.$all_prefix.'_analytics_ua'] : '';
                    $ga4_measurement_id = !empty($config['google'.$all_prefix.'_measurement_id']) ? $config['google'.$all_prefix.'_measurement_id'] : '';

                    $conversion_status = !empty($config['google'.$all_prefix.'_conversion_status']) ? $config['google'.$all_prefix.'_conversion_status'] : '';
                    $conversion_id = !empty($config['google'.$all_prefix.'_conversion_id']) ? $config['google'.$all_prefix.'_conversion_id'] : '';
                    $conversion_label = !empty($config['google'.$all_prefix.'_conversion_label']) ? $config['google'.$all_prefix.'_conversion_label'] : '';

                    $dynamic_remarketing_status = !empty($config['google'.$all_prefix.'_dynamic_remarketing_status']) ? $config['google'.$all_prefix.'_dynamic_remarketing_status'] : '';
                    $dynamic_remarketing_id = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id'] : '';
                    $dynamic_remarketing_label = !empty($config['google'.$all_prefix.'_dynamic_remarketing_label']) ? $config['google'.$all_prefix.'_dynamic_remarketing_label'] : '';
                    $dynamic_remarketing_prefix = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id_prefix']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id_prefix'] : 'EMPTY';
                    $dynamic_remarketing_sufix = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id_sufix']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id_sufix'] : 'EMPTY';
                    $dynamic_remarketing_dynx = !empty($config['google'.$all_prefix.'_dynamic_remarketing_dynx']) ? true : false;

                    //$enhanced_status = $ga_ua && !empty($config['google'.$all_prefix.'_enhanced_ecommerce_status']);

                    //$enhanced_4_status = $ga4_measurement_id && !empty($config['google'.$all_prefix.'_enhanced_ecommerce_status']);

                    $rich_nippets_status = !empty($config['google'.$all_prefix.'_rich_snippets']) ? $config['google'.$all_prefix.'_rich_snippets'] : '';

                    $hotjar_status = !empty($config['google'.$all_prefix.'_hotjar_status']) ? $config['google'.$all_prefix.'_hotjar_status'] : '';
                    $hotjar_site_id = !empty($config['google'.$all_prefix.'_hotjar_site_id']) ? $config['google'.$all_prefix.'_hotjar_site_id'] : '';

                    $pinterest_status = !empty($config['google'.$all_prefix.'_pinterest_status']) ? $config['google'.$all_prefix.'_pinterest_status'] : '';
                    $pinterest_id = !empty($config['google'.$all_prefix.'_pinterest_id']) ? $config['google'.$all_prefix.'_pinterest_id'] : '';

                    $crazyegg_status = !empty($config['google'.$all_prefix.'_crazyegg_status']) ? $config['google'.$all_prefix.'_crazyegg_status'] : '';
                    $crazyegg_id = !empty($config['google'.$all_prefix.'_crazyegg_id']) ? $config['google'.$all_prefix.'_crazyegg_id'] : '';

                    $facebook_pixel_status = !empty($config['google'.$all_prefix.'_facebook_pixel_status']) ? $config['google'.$all_prefix.'_facebook_pixel_status'] : '';
                    $facebook_pixel_id = !empty($config['google'.$all_prefix.'_facebook_pixel_id']) ? $config['google'.$all_prefix.'_facebook_pixel_id'] : '';
                    $facebook_pixel_token = !empty($config['google'.$all_prefix.'_facebook_pixel_token']) ? $this->string_encrypt($config['google'.$all_prefix.'_facebook_pixel_token']) : '';

                    $tiktok_pixel_status = !empty($config['google'.$all_prefix.'_tiktok_pixel_status']) ? $config['google'.$all_prefix.'_tiktok_pixel_status'] : '';
                    $tiktok_pixel_id = !empty($config['google'.$all_prefix.'_tiktok_pixel_id']) ? $config['google'.$all_prefix.'_tiktok_pixel_id'] : '';

                    $bing_ads_status = !empty($config['google'.$all_prefix.'_bing_ads_status']) ? $config['google'.$all_prefix.'_bing_ads_status'] : '';
                    $bing_ads_tag_id = !empty($config['google'.$all_prefix.'_bing_ads_tag_id']) ? $config['google'.$all_prefix.'_bing_ads_tag_id'] : '';

                    $criteo_status = !empty($config['google'.$all_prefix.'_criteo_status']) ? $config['google'.$all_prefix.'_criteo_status'] : '';
                    $criteo_id = !empty($config['google'.$all_prefix.'_criteo_id']) ? $config['google'.$all_prefix.'_criteo_id'] : '';

                    $yahoo_status = !empty($config['google'.$all_prefix.'_yahoo_status']) ? $config['google'.$all_prefix.'_yahoo_status'] : '';
                    $yahoo_project_id = !empty($config['google'.$all_prefix.'_yahoo_project_id']) ? $config['google'.$all_prefix.'_yahoo_project_id'] : '';
                    $yahoo_pixel_id = !empty($config['google'.$all_prefix.'_yahoo_pixel_id']) ? $config['google'.$all_prefix.'_yahoo_pixel_id'] : '';

                    $domodi_status = !empty($config['google'.$all_prefix.'_domodi_pixel_status']) ? $config['google'.$all_prefix.'_domodi_pixel_status'] : '';
                    $domodi_pixel_id = !empty($config['google'.$all_prefix.'_domodi_pixel_id']) ? $config['google'.$all_prefix.'_domodi_pixel_id'] : '';

                    $stileo_status = !empty($config['google'.$all_prefix.'_stileo_pixel_status']) ? $config['google'.$all_prefix.'_stileo_pixel_status'] : '';
                    $stileo_pixel_id = !empty($config['google'.$all_prefix.'_stileo_pixel_id']) ? $config['google'.$all_prefix.'_stileo_pixel_id'] : '';

                    $skroutz_status = !empty($config['google'.$all_prefix.'_skroutz_analytics_status']) ? $config['google'.$all_prefix.'_skroutz_analytics_status'] : '';
                    $skroutz_account_id = !empty($config['google'.$all_prefix.'_skroutz_analytics_shop_id']) ? $config['google'.$all_prefix.'_skroutz_analytics_shop_id'] : '';
                }
                elseif($from_prestashop) {
                    $config = $this->request->data;
                    $config['gmt_gtm_generator_license_id'] = preg_replace('/[^0-9]/', '', $config['gmt_gtm_generator_license_id']);
                    $lang = array_key_exists('lang', $config) ? $config['lang'] : 'en';

                    if($config['domain'] == 'prestashop.devmanextensions.com') {
                        $this->die_error($lang == 'es' ? 'Acción no permitida en demo' : 'Action does not allowed in demo');
                    }

                    $license_id = 'ps-'.$config['gmt_gtm_generator_license_id'];

                    $ga_status = !empty($config['gmt_gtm_generator_ga_status']);
                    $ga_ua = $config['gmt_gtm_generator_ga_ua'];

                    $container_id = $config['gmt_gtm_generator_gtm_container_id'];

                    $conversion_status = !empty($config['gmt_gtm_generator_ac_status']);
                    $conversion_id = $config['gmt_gtm_generator_ac_conversion_id'];
                    $conversion_label = $config['gmt_gtm_generator_ac_conversion_label'];

                    $dynamic_remarketing_status = !empty($config['gmt_gtm_generator_gdr_status']);
                    $dynamic_remarketing_id = $config['gmt_gtm_generator_gdr_conversion_id'];
                    $dynamic_remarketing_label = $config['gmt_gtm_generator_gdr_conversion_label'];
                    $dynamic_remarketing_prefix = !empty($config['gmt_gtm_generator_gdr_conversion_id_prefix']) ? $config['gmt_gtm_generator_gdr_conversion_id_prefix'] : 'EMPTY';
                    $dynamic_remarketing_sufix = !empty($config['gmt_gtm_generator_gdr_conversion_id_sufix']) ? $config['gmt_gtm_generator_gdr_conversion_id_sufix'] : 'EMPTY';
                    $dynamic_remarketing_dynx = $config['gmt_gtm_generator_gdr_conversion_dynx_status'];

                    $enhanced_status = !empty($config['gmt_gtm_generator_ee_status']);
                    $rich_nippets_status = !empty($config['gmt_gtm_generator_rs_status']);

                    $facebook_pixel_status = !empty($config['gmt_gtm_generator_fbp_status']);
                    $facebook_pixel_id = $config['gmt_gtm_generator_fbp_pixel_id'];

                    $bing_ads_status = !empty($config['gmt_gtm_generator_ba_status']);
                    $bing_ads_tag_id =$config['gmt_gtm_generator_ba_tag_id'];

                    $criteo_status = !empty($config['gmt_gtm_generator_cot_status']);
                    $criteo_id = $config['gmt_gtm_generator_cot_partner_id'];

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = $stileo_pixel_id = false;
                }
				elseif($from_woocommerce) {
                    $config = $this->request->data;

                    $license_id = !empty($config['wc-gmt_license_id_gtm']) ? $config['wc-gmt_license_id_gtm'] : '';
                    $container_id = !empty($config['wc-gmt_container_id']) ? $config['wc-gmt_container_id'] : '';
                    $container_optimize_id = !empty($config['wc-gmt_container_optimize_id']) ? $config['wc-gmt_container_optimize_id'] : '';

                    $ga_ua = !empty($config['wc-gmt_google_analytics_ua']) ? $config['wc-gmt_google_analytics_ua'] : '';
                    $ga_status = !empty($ga_ua);

                    $conversion_status = !empty($config['wc-gmt_conversion_status']) ? $config['wc-gmt_conversion_status'] : '';
                    $conversion_id = !empty($config['wc-gmt_conversion_id']) ? $config['wc-gmt_conversion_id'] : '';
                    $conversion_label = !empty($config['wc-gmt_conversion_label']) ? $config['wc-gmt_conversion_label'] : '';

                    $dynamic_remarketing_status = !empty($config['wc-gmt_dynamic_remarketing_status']) ? $config['wc-gmt_dynamic_remarketing_status'] : '';
                    $dynamic_remarketing_id = !empty($config['wc-gmt_dynamic_remarketing_id']) ? $config['wc-gmt_dynamic_remarketing_id'] : '';
                    $dynamic_remarketing_label = !empty($config['wc-gmt_dynamic_remarketing_label']) ? $config['wc-gmt_dynamic_remarketing_label'] : '';
                    $dynamic_remarketing_prefix = !empty($config['wc-gmt_dynamic_remarketing_id_prefix']) ? $config['wc-gmt_dynamic_remarketing_id_prefix'] : 'EMPTY';
                    $dynamic_remarketing_sufix = !empty($config['wc-gmt_dynamic_remarketing_id_sufix']) ? $config['wc-gmt_dynamic_remarketing_id_sufix'] : 'EMPTY';
                    $dynamic_remarketing_dynx = !empty($config['wc-gmt_dynamic_remarketing_dynx']) ? true : false;

                    $enhanced_status = !empty($config['wc-gmt_enhanced_ecommerce_status']) ? $config['wc-gmt_enhanced_ecommerce_status'] : '';
                    $rich_nippets_status = !empty($config['wc-gmt_rich_snippets']) ? $config['wc-gmt_rich_snippets'] : '';

                    $hotjar_status = !empty($config['wc-gmt_hotjar_status']) ? $config['wc-gmt_hotjar_status'] : '';
                    $hotjar_site_id = !empty($config['wc-gmt_hotjar_site_id']) ? $config['wc-gmt_hotjar_site_id'] : '';

                    $pinterest_status = !empty($config['wc-gmt_pinterest_status']) ? $config['wc-gmt_pinterest_status'] : '';
                    $pinterest_id = !empty($config['wc-gmt_pinterest_id']) ? $config['wc-gmt_pinterest_id'] : '';

                    $crazyegg_status = !empty($config['wc-gmt_crazyegg_status']) ? $config['wc-gmt_crazyegg_status'] : '';
                    $crazyegg_id = !empty($config['wc-gmt_crazyegg_id']) ? $config['wc-gmt_crazyegg_id'] : '';

                    $facebook_pixel_status = !empty($config['wc-gmt_facebook_pixel_status']) ? $config['wc-gmt_facebook_pixel_status'] : '';
                    $facebook_pixel_id = !empty($config['wc-gmt_facebook_pixel_id']) ? $config['wc-gmt_facebook_pixel_id'] : '';

                    $bing_ads_status = !empty($config['wc-gmt_bing_ads_status']) ? $config['wc-gmt_bing_ads_status'] : '';
                    $bing_ads_tag_id = !empty($config['wc-gmt_bing_ads_tag_id']) ? $config['wc-gmt_bing_ads_tag_id'] : '';

                    $criteo_status = !empty($config['wc-gmt_criteo_status']) ? $config['wc-gmt_criteo_status'] : '';
                    $criteo_id = !empty($config['wc-gmt_criteo_id']) ? $config['wc-gmt_criteo_id'] : '';

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = $stileo_pixel_id = false;
                }
				elseif($from_magento) {
                    $config = $this->request->data;

                    $license_id = !empty($config['ordernumber']) ? $config['ordernumber'] : '';
                    $container_id = !empty($config['gtmcontainerid']) ? $config['gtmcontainerid'] : '';
                    $container_optimize_id = !empty($config['goptimize']) ? $config['goptimize'] : '';

                    $ga_ua = !empty($config['gaua']) ? $config['gaua'] : '';
                    $ga_status = !empty($ga_ua);

                    $conversion_status = !empty($config['adwords']) ? $config['adwords'] : '';
                    $conversion_id = !empty($config['adwordsconvid']) ? $config['adwordsconvid'] : '';
                    $conversion_label = !empty($config['adwordsconvlabel']) ? $config['adwordsconvlabel'] : '';

                    $dynamic_remarketing_status = !empty($config['standard']) ? $config['standard'] : '';
                    $dynamic_remarketing_id = !empty($config['standardconvid']) ? $config['standardconvid'] : '';
                    $dynamic_remarketing_label = !empty($config['standardconvlabel']) ? $config['standardconvlabel'] : '';
                    $dynamic_remarketing_prefix = !empty($config['standardprefixid']) ? $config['standardprefixid'] : 'EMPTY';
                    $dynamic_remarketing_sufix = !empty($config['standardsufixid']) ? $config['standardsufixid'] : 'EMPTY';
                    $dynamic_remarketing_dynx = !empty($config['dynxcomp']) ? true : false;

                    $enhanced_status = !empty($config['gaenhancedcommerce']) ? $config['gaenhancedcommerce'] : '';
                    $rich_nippets_status = !empty($config['richsnippets']) ? $config['richsnippets'] : '';

                    $hotjar_status = !empty($config['hotjar']) ? $config['hotjar'] : '';
                    $hotjar_site_id = !empty($config['hotjarid']) ? $config['hotjarid'] : '';

                    $pinterest_status = !empty($config['pinterestpixel']) ? $config['pinterestpixel'] : '';
                    $pinterest_id = !empty($config['pinterestpixelid']) ? $config['pinterestpixelid'] : '';

                    $crazyegg_status = !empty($config['crazyegg']) ? $config['crazyegg'] : '';
                    $crazyegg_id = !empty($config['crazyeggid']) ? $config['crazyeggid'] : '';

                    $facebook_pixel_status = !empty($config['facebookpixel']) ? $config['facebookpixel'] : '';
                    $facebook_pixel_id = !empty($config['facebookpixelid']) ? $config['facebookpixelid'] : '';

                    $bing_ads_status = !empty($config['bingadsconversions']) ? $config['bingadsconversions'] : '';
                    $bing_ads_tag_id = !empty($config['bingadsconversionstagid']) ? $config['bingadsconversionstagid'] : '';

                    $criteo_status = !empty($config['criteoonetag']) ? $config['criteoonetag'] : '';
                    $criteo_id = !empty($config['criteoonetagid']) ? $config['criteoonetagid'] : '';

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = $stileo_pixel_id = false;
                }

                $license_id = trim($license_id);
                $container_id = trim($container_id);

                //echo '<pre>'; print_r($this->request->data);  echo '</pre>'; die;

				if(empty($license_id))
					$this->die_error('Fill License Order ID');
				if(empty($container_id))
					$this->die_error('Fill Google Tag Manager container ID');
				/*if(empty($ga_ua) && !empty($container_optimize_id))
				    $this->die_error('Google Optimize needs that Google Analytics is filled');
				if((empty($ga_ua) && empty($ga4_measurement_id)) && !empty($enhanced_status))
				    $this->die_error('Enhanced Ecommerce needs that GA or GA4 is filled');
				if(empty($ga_ua) && empty($ga4_measurement_id))
				    $this->die_error('System needs GA UA or GA4 measurement ID');*/
				if(!empty($conversion_status) && empty($conversion_id))
					$this->die_error('Fill Adwords conversion ID');
				if(!empty($conversion_status) && !is_numeric($conversion_id))
					$this->die_error('Adwords conversion ID - ONLY NUMBERS');
				if(!empty($conversion_status) && empty($conversion_label))
					$this->die_error('Fill Adwords conversion Label');
				if(!empty($dynamic_remarketing_status) && empty($dynamic_remarketing_id))
					$this->die_error('Fill Google dynamic remarketing conversion ID');
				if(!empty($hotjar_status) && empty($hotjar_site_id))
					$this->die_error('Fill Hotjar site ID');
				if(!empty($pinterest_status) && empty($pinterest_id))
					$this->die_error('Fill Pinterest ID');
				if(!empty($crazyegg_status) && empty($crazyegg_id))
					$this->die_error('Fill Crazyegg ID');
				if(!empty($criteo_status) && empty($criteo_id))
					$this->die_error('Fill Criteo Partner ID');
				if(!empty($facebook_pixel_status) && empty($facebook_pixel_id))
					$this->die_error('Fill Facebook Pixel ID');
				if(!empty($facebook_pixel_status) && empty($facebook_pixel_token))
					$this->die_error('Fill Facebook Access token');
				if(!empty($tiktok_pixel_status) && empty($tiktok_pixel_id))
					$this->die_error('Fill TikTok Pixel ID');
				if(!empty($bing_ads_status) && empty($bing_ads_tag_id))
					$this->die_error('Fill Bing Ads tag ID');
				if(!empty($yahoo_status) && empty($yahoo_project_id))
					$this->die_error('Fill Yahoo - Native & Search Dot > Project ID');
				if(!empty($yahoo_status) && empty($yahoo_pixel_id))
					$this->die_error('Fill Yahoo - Native & Search Dot > Pixel ID');
				if(!empty($domodi_status) && empty($domodi_pixel_id))
					$this->die_error('Fill Domodi Pixel > Shop key');
				if(!empty($stileo_status) && empty($stileo_pixel_id))
					$this->die_error('Fill Stileo Pixel 2.0 > API key');
				if(!empty($skroutz_status) && empty($skroutz_account_id))
					$this->die_error('Fill Skroutz Analytics Account ID');

				if(!empty($container_optimize_id) && $container_optimize_id == $container_id)
				    $this->die_error('Google Optimize ID can\'t be same that Google tag manager ID.');

				if($from_prestashop) {
				    $sale = $this->Sale->findByOrderId($license_id);

                    //echo '<pre>'; print_r($sale);  echo '</pre>'; die;
                    if(empty($sale)) {
                        $error = array('error' => false, 'message' => '');

                        $buyer_email = array_key_exists('email', $config) ? $config['email'] : '';
                        $buyer_username = array_key_exists('email', $config) ? $config['email'] : '';
                        $domain = array_key_exists('domain', $config) ? $this->get_domain($config['domain']) : '';
                        $extension_id = array_key_exists('extension_id', $config) ? $config['extension_id'] : '';

                        $extension = $this->Extension->findById($extension_id);

                        if(empty($buyer_username) || empty($buyer_email))
                            $this->die_error('Internal error: Code 1');

                        if(empty($domain))
                            $this->die_error('Internal error: Code 2');

                        if(empty($extension_id))
                            $this->die_error('Internal error: Code 3');

                        $license = array('Sale' => array(
                            'order_id' => $license_id,
                            'marketplace' => 'Prestashop',
                            'domain' => $domain,
                            'extension_id' => $extension_id,
                            'extension_name' => $extension['Extension']['name'],
                            'extension_id' => $extension['Extension']['id'],
                            'buyer_email' => $buyer_email,
                            'buyer_username' => $buyer_username,
                            'order_status' => 'pending_validate',
                            'date_added' => date('Y-m-d H:i:s'),
                            'date_modified' => date('Y-m-d H:i:s')
                        ));


                        if(!$this->Sale->saveAll($license)) {
                            $error['error'] = 1;
                            $error['message'] = 'Error creating your license.';
                            echo json_encode($error); die;
                        } else {
                            $text = sprintf('Hay una licencia de Prestashop pendiente de validar:<ul>
                                    <li>Licencia ID: <b>%s</b></li>
                                    <li>Email: <b><a href="mailto:%s">%s</a></b></li>
                                    <li>Link validación: <b><a href="%s">Click para validar esta licencia.</a></b></li>
                            </ul>', $license_id, $buyer_email, $buyer_email, Router::url('/', true).'sales/sales_waiting/autovalidate/'.$license_id);

                            $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Sales system', '[Prestashop] New license pending to validate - '.$extension['Extension']['name'], $text, '', 'info@kijam.com');

                            $error['success'] = 1;
                            if($lang == 'es')
                                $error['message'] = 'Tu licencia fue registrada, nuestro equipo la validará lo antes posible, una vez validada recibirás un email a la dirección <b>'.$buyer_email.'</b> con el resultado de la operación.';
                            else
                                $error['message'] = 'Your license was registered, our team will be validate it soon as possible, you will receive and email to address <b>'.$buyer_email.'</b> with the result.';
                            echo json_encode($error); die;
                        }
                    } else {
                        //Is possible that is pending
                        if($sale['Sale']['order_status'] == 'pending_validate') {
                            $error['error'] = 1;
                            $error['is_pending'] = 1;
                            if($lang == 'es')
                                $error['message'] = 'Tu licencia está pendiente de validar. Nuestro equipo la validará lo antes posible, una vez validada recibirás un email a la dirección <b>'.$sale['Sale']['buyer_email'].'</b> con el resultado de la operación.';
                            else
                                $error['message'] = 'Your license is pending to validate. Our team will be validate it soon as possible, you will receive and email to address <b>'.$sale['Sale']['buyer_email'].'</b> with the result.';
                            echo json_encode($error);
                            die;
                        }
                    }
                }

				//Devman Extensions - info@devmanextensions.com - 2017-04-03 09:56:47 - Check license
					$license_result = $this->check_license($license_id);

					if(empty($license_result['Sale']))
						$this->die_error($license_result);
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-05-02 19:53:34 - Check 1 use by license
					$license_uses_result = $this->check_license_uses($license_result, $container_id);
					if($license_uses_result)
						$this->die_error($license_uses_result);
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 09:39:03 - Get workspace json, replace all values first.
					$workspace_data_json = file_get_contents(WWW_ROOT . DS . 'gmt_workspace/'.$workspace_name);
					//$workspace_data_json = str_replace('UA-94017251-1', $ga_ua, $workspace_data_json);
					$workspace_data_json = str_replace('GA4MEASUREMENTID', $ga4_measurement_id, $workspace_data_json);

					$workspace_data_json = str_replace($container_id_name, $container_id, $workspace_data_json);
					$workspace_data_json = str_replace('GOOGLEOPTIMIZECONTAINERID', $container_optimize_id, $workspace_data_json);
					$workspace_data_json = str_replace('1550553151909443', $facebook_pixel_id, $workspace_data_json);
					$workspace_data_json = str_replace('FB_API_TOKEN', $facebook_pixel_token, $workspace_data_json);

					$workspace_data_json = str_replace('TIKTOK_PIXEL_ID', $tiktok_pixel_id, $workspace_data_json);
					$workspace_data_json = str_replace('IDCONVERSION', $conversion_id, $workspace_data_json);
					$workspace_data_json = str_replace('LABELCONVERSION', $conversion_label, $workspace_data_json);
					$workspace_data_json = str_replace('1235861442', '1234567890', $workspace_data_json);

					$workspace_data_json = str_replace('HOTJARSITEID', $hotjar_site_id, $workspace_data_json);
					$workspace_data_json = str_replace('PINTERESTPIXELID', $pinterest_id, $workspace_data_json);
					$workspace_data_json = str_replace('CRAZYEGGID', $crazyegg_id, $workspace_data_json);

					$workspace_data_json = str_replace('DYNAMICREMARKETINGCONID', $dynamic_remarketing_id, $workspace_data_json);
					$workspace_data_json = str_replace('DYNAMICREMARKETINGCONLABEL', $dynamic_remarketing_label, $workspace_data_json);
					$workspace_data_json = str_replace('PREFIXPRODUCTID', $dynamic_remarketing_prefix, $workspace_data_json);
					$workspace_data_json = str_replace('SUFIXPRODUCTID', $dynamic_remarketing_sufix, $workspace_data_json);
					$workspace_data_json = str_replace('DYNXSTATUS', $dynamic_remarketing_dynx ? 'TRUE' : 'FALSE', $workspace_data_json);

					$workspace_data_json = str_replace('BING_ADS_UET_TAG_ID', $bing_ads_tag_id, $workspace_data_json);
					$workspace_data_json = str_replace('CRITEOPARTNERID', $criteo_id, $workspace_data_json);

					$workspace_data_json = str_replace('YAHOO-PIXEL-ID', $yahoo_pixel_id, $workspace_data_json);
					$workspace_data_json = str_replace('YAHOO-PROJECT-ID', $yahoo_project_id, $workspace_data_json);

					$workspace_data_json = str_replace('DOMODI_SHOP_KEY', $domodi_pixel_id, $workspace_data_json);
					$workspace_data_json = str_replace('STILEO_API_KEY', $stileo_pixel_id, $workspace_data_json);
					$workspace_data_json = str_replace('SKROUTZ_ACCOUNT_ID', $skroutz_account_id, $workspace_data_json);
				//END

                //Devman Extensions - info@devmanextensions.com - 01/05/2019 17:30 - Fix FORMAT VALUE
                $workspace_data_json = json_decode($workspace_data_json, true);
                $workspace_data_json = json_encode($workspace_data_json);

                $workspace_data_json = str_replace(',"formatValue":[]', '', $workspace_data_json);

				//Transform workspace to array
					$workspace_data = json_decode($workspace_data_json, true);

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 10:45:32 - Remove tags
					/*foreach ($workspace_data['containerVersion']['tag'] as $key => $tag) {
						echo $tag['tagId'].' - '.$tag['name']."\n";
					} die;*/

					//$ga_tags = array(829);
					$ga4_tags = array(807,832,835,824,811,838,816,808,826,843);
                    //$ee_tags = array(815,820,827,834,803,841,817,819,801);
                    //$ee_ga4_tags = array(832,835,824,811,838,816,808,826,843);
                    $fb_tags = array(356,367,337,14,842,809,814);
                    $tiktok_tags = array(810,302);
                    $rich_snippets_tags = array(837);
                    $dynamic_remarketing_tags = array(825);
                    $conversion_tags = array(823,840);
                    $bing_ads_tags = array(818,836,800);
                    $criteo_one_tags = array(844);
                    $hotjar_tags = array(845);
                    $pinterest_tags = array(806,805,862,864);
                    $crazyegg_tags = array(802);
                    $go_tags = array(830);
                    $yahoo_tags = array(812,813);
                    $domodi_tags = array(839,804);
                    $stileo_tags = array(828,831,822);
                    $vk_tags = array();
                    $skroutz_tags = array(821);

					$copy_tags = $workspace_data['containerVersion']['tag'];
					foreach ($copy_tags as $key => $tag) {
					    $delete =
                            //(empty($ga_ua) && in_array($tag['tagId'], $ga_tags)) ||
                            //(empty($ga4_measurement_id) && in_array($tag['tagId'], $ga4_tags)) ||
                            //(in_array($tag['tagId'], $ee_tags) && (empty($enhanced_status) || empty($ga_ua))) ||
                            //(in_array($tag['tagId'], $ee_ga4_tags) && (empty($enhanced_4_status) || empty($ga4_measurement_id))) ||
							(empty($ga4_measurement_id) && in_array($tag['tagId'], $ga4_tags)) ||
                            (empty($conversion_status) && in_array($tag['tagId'], $conversion_tags)) ||
                            (empty($facebook_pixel_status) && in_array($tag['tagId'], $fb_tags)) ||
                            (empty($tiktok_pixel_status) && in_array($tag['tagId'], $tiktok_tags)) ||
                            (empty($criteo_status) && in_array($tag['tagId'], $criteo_one_tags)) ||
                            (empty($rich_nippets_status) && in_array($tag['tagId'], $rich_snippets_tags)) ||
                            (empty($dynamic_remarketing_status) && in_array($tag['tagId'], $dynamic_remarketing_tags)) ||
                            (empty($bing_ads_status) && in_array($tag['tagId'], $bing_ads_tags)) ||
                            (empty($hotjar_status) && in_array($tag['tagId'], $hotjar_tags)) ||
                            (empty($pinterest_status) && in_array($tag['tagId'], $pinterest_tags)) ||
                            (empty($crazyegg_status) && in_array($tag['tagId'], $crazyegg_tags)) ||
                            (in_array($tag['tagId'], $go_tags) && (empty($container_optimize_id) || empty($ga_ua))) ||
                            (empty($yahoo_status) && in_array($tag['tagId'], $yahoo_tags)) ||
                            (empty($vk_status) && in_array($tag['tagId'], $vk_tags)) ||
                            (empty($domodi_status) && in_array($tag['tagId'], $domodi_tags)) ||
                            (empty($stileo_status) && in_array($tag['tagId'], $stileo_tags)) ||
                            (empty($skroutz_status) && in_array($tag['tagId'], $skroutz_tags));

					    if($delete)
						    unset($workspace_data['containerVersion']['tag'][$key]);
					}

					/*if(empty($criteo_status) || (!empty($criteo_status) && !$from_woocommerce)) {
					    //Delete criteo one tag - tag purchase, only is for woocommerce
                        $copy_tags = $workspace_data['containerVersion']['tag'];
                        foreach ($copy_tags as $key => $tag) {
                            $delete = $tag['tagId'] == 37;
                            if($delete)
                                unset($workspace_data['containerVersion']['tag'][$key]);
                        }
                    }*/

					$workspace_data['containerVersion']['tag'] = array_values($workspace_data['containerVersion']['tag']);
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 20:27:44 - Remove triggers
					/*foreach ($workspace_data['containerVersion']['trigger'] as $key => $trigger) {
						echo $trigger['triggerId'].' - '.$trigger['name']."\n";
					} die;*/

                    /*$ee_triggers = array(343);

					$copy_triggers = $workspace_data['containerVersion']['trigger'];
					foreach ($copy_triggers as $key => $trigger) {
						if(empty($enhanced_status) && in_array($trigger['triggerId'], $ee_triggers))
							unset($workspace_data['containerVersion']['trigger'][$key]);
					}
					$workspace_data['containerVersion']['trigger'] = array_values($workspace_data['containerVersion']['trigger']);*/
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 15:09:57 - Remove variables
					/*foreach ($workspace_data['containerVersion']['variable'] as $key => $var) {
						echo $var['variableId'].' - '.$var['name']."\n";
					} die;*/

					//$ga_variables = array(74,281);
					$ga4_variables = array(380,134,129,152,131,135,137,130,141,791,132);
                    //$ee_variables = array(9,13,15,65,770,771,792,19,795,781,21);
                    //$ee_ga4_variables = array(134,129,152,131,135,137,130,141,791,132);
                    $fb_variables = array(799,787,381,22,865,866,867);
                    $tiktok_variables = array(303);
                    $rich_snippets_variables = array(24);
                    $dynamic_remarketing_variables = array(66,777,773,774,778,26,27,67,68);
                    $conversion_variables = array(70,71);
                    $bing_ads_variables = array(62);
                    $criteo_variables = array(64,5);
                    $hotjar_variables = array(72);
                    $pinterest_variables = array(37,73);
                    $crazyegg_variables = array(63);
                    $google_optimize_variables = array(69);
                    $yahoo_variables = array(98,99);
                    $domodi_variables = array(117);
                    $stileo_variables = array(283);
                    $vk_variables = array();
                    $skroutz_variables = array(298);

					$copy_variables = $workspace_data['containerVersion']['variable'];
					foreach ($copy_variables as $key => $var) {
					    $delete =
                            //(empty($ga_ua) && in_array($var['variableId'], $ga_variables)) ||
                            (empty($ga4_measurement_id) && in_array($var['variableId'], $ga4_variables)) ||
                            //(empty($enhanced_status) && in_array($var['variableId'], $ee_variables)) ||
                            //(empty($enhanced_4_status) && in_array($var['variableId'], $ee_ga4_variables)) ||
                            (empty($conversion_status) && in_array($var['variableId'], $conversion_variables)) ||
                            (empty($facebook_pixel_status) && in_array($var['variableId'], $fb_variables)) ||
                            (empty($tiktok_pixel_status) && in_array($var['variableId'], $tiktok_variables)) ||
                            (empty($criteo_status) && in_array($var['variableId'], $criteo_variables)) ||
                            (empty($rich_nippets_status) && in_array($var['variableId'], $rich_snippets_variables)) ||
                            (empty($dynamic_remarketing_status) && in_array($var['variableId'], $dynamic_remarketing_variables)) ||
                            (empty($bing_ads_status) && in_array($var['variableId'], $bing_ads_variables)) ||
                            (empty($hotjar_status) && in_array($var['variableId'], $hotjar_variables)) ||
                            (empty($pinterest_status) && in_array($var['variableId'], $pinterest_variables)) ||
                            (empty($crazyegg_status) && in_array($var['variableId'], $crazyegg_variables)) ||
					        (in_array($var['variableId'], $google_optimize_variables) && (empty($container_optimize_id) || empty($ga_ua))) ||
                            (empty($yahoo_status) && in_array($var['variableId'], $yahoo_variables)) ||
                            (empty($vk_status) && in_array($var['variableId'], $vk_variables)) ||
                            (empty($domodi_status) && in_array($var['variableId'], $domodi_variables)) ||
                            (empty($stileo_status) && in_array($var['variableId'], $stileo_variables)) ||
                            (empty($skroutz_status) && in_array($var['variableId'], $skroutz_variables));

					    if($delete)
						    unset($workspace_data['containerVersion']['variable'][$key]);
					}
					$workspace_data['containerVersion']['variable'] = array_values($workspace_data['containerVersion']['variable']);

					//Conversion label empty - Remove label from tag and tag constant variable
						if(!empty($conversion_status) && empty($conversion_label))
						{
						    $this->remove_tag_parameter($workspace_data, 823, 'conversionLabel');
						    $this->remove_variable($workspace_data, 71);
						}

					//Dynamic remarketing label empty - Remove label from tag and tag constant variable
						if(!empty($dynamic_remarketing_status) && empty($dynamic_remarketing_label))
						{
						    $this->remove_tag_parameter($workspace_data, 825, 'conversionLabel');
						    $this->remove_variable($workspace_data, 27);
						}
				    //END

                //<editor-fold desc="Remove GA custom dimmensions if GDR is disabled">
                    /*if(!$dynamic_remarketing_status && (!isset($ga_status) || $ga_status)) {
						$copy_tags = $workspace_data['containerVersion']['tag'];
                        foreach ($copy_tags as $key => $tag) {
                            $delete = in_array($tag['tagId'], $ga_tags);

                            if ($delete) {
                                //Custom dimensions 2 and 3
                                unset($workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list'][1]);
                                unset($workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list'][2]);
                                $workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list'] = array_values($workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list']);
                            }
                        }
                    }*/
                //</editor-fold>


				$file_path = 'gmt_workspace/exported/'.$license_id.'-'.$container_id.'.json';
				$file = fopen($file_path,'w+');
                $writed = fwrite($file,json_encode($workspace_data, JSON_UNESCAPED_SLASHES));
                fclose($file);
                if($this->send_gtm_workspace($license_result, $file_path, $domain))
                {
	                $array_return['message'] = sprintf('<b>IMPORTANT: MAKE SURE THAT YOU HAVE THE LAST GMT VERSION ELSE MAYBE YOUR WORKSPACE GENERATE WON\'T BE COMPATIBLE.</b><br>Google Tag Manager Workspace was sent to <b>%s</b>, wait 1 minute, also check your SPAM folder. Press save/apply now to don\'t change your configuration.', $license_result['Sale']['buyer_email']);
	                echo json_encode($array_return); die;
	            }
	            else
	            	$this->die_error('Error sending Workspace file');
			} else {
			    $this->die_error('Empty POST data');
            }
		}

		function die_error($message)
		{
			$array_return = array();
			$array_return['error'] = true;
			$array_return['message'] = $message;
			echo json_encode($array_return); die;
		}

		function exam_wokspace()
		{
			$fichero = json_decode(file_get_contents(WWW_ROOT . DS . 'gmt_workspace/new_version.json'), true);
			$tags = array();

            foreach ($fichero['containerVersion']['tag'] as $key => $tag) {
                $tags[$tag['name']] = $tag['tagId'];
            }
            ksort($tags);


            $triggers = array();
            foreach ($fichero['containerVersion']['trigger'] as $key => $tag) {
                $triggers[$tag['name']] = $tag['triggerId'];
            }
            ksort($triggers);

            $vars = array();
            foreach ($fichero['containerVersion']['variable'] as $key => $tag) {
                $vars[$tag['name']] = $tag['variableId'];
            }
            ksort($vars);

            echo '<h1>TAGS</h1>';
            echo '<pre>'; print_r($tags);  echo '</pre>';

            echo '<h1>VARIABLES</h1>';
            echo '<pre>'; print_r($vars);  echo '</pre>';

            echo '<h1>TRIGGERS</h1>';
            echo '<pre>'; print_r($triggers);  echo '</pre>'; die;
		}

		function check_new_version() {
            $main_path = WWW_ROOT . DS . 'gmt_workspace/';

            $fichero = json_decode(file_get_contents($main_path.'workspace_gHxJ8DSX.json', FILE_USE_INCLUDE_PATH), true);
            $fichero2 = json_decode(file_get_contents($main_path.'new_version.json', FILE_USE_INCLUDE_PATH), true);

            $tags = array();
            foreach ($fichero['containerVersion']['tag'] as $key => $tag) {
                $tags[$tag['name']] = $tag['tagId'];
            }

            $triggers = array();
            foreach ($fichero['containerVersion']['trigger'] as $key => $tag) {
                $triggers[$tag['name']] = $tag['triggerId'];
            }

            $vars = array();
            foreach ($fichero['containerVersion']['variable'] as $key => $tag) {
                $vars[$tag['name']] = $tag['variableId'];
            }


            $tags2 = array();
            foreach ($fichero2['containerVersion']['tag'] as $key => $tag) {
                $tags2[$tag['name']] = $tag['tagId'];
            }

            $triggers2 = array();
            foreach ($fichero2['containerVersion']['trigger'] as $key => $tag) {
                $triggers2[$tag['name']] = $tag['triggerId'];
            }

            $vars2 = array();
            foreach ($fichero2['containerVersion']['variable'] as $key => $tag) {
                $vars2[$tag['name']] = $tag['variableId'];
            }

            echo '<h1>Cambios en tags</h1>';
            echo '<ul>';
            foreach ($tags as $tag_name => $tag_id) {
                if(!array_key_exists($tag_name, $tags2)) {
                    echo '<li>La etiqueta <b>'.$tag_name.'</b> no se encuentra en la nueva versión</li>';
                }

                if(array_key_exists($tag_name, $tags2) && $tags2[$tag_name] != $tag_id) {
                    echo '<li>La etiqueta <b>'.$tag_name.'</b> que tenía ID <b>'.$tag_id.'</b> ahora tiene ID <b>'.$tags2[$tag_name].'</b></li>';
                }
            }
            echo '</ul>';

            echo '<h1>Cambios en eventos</h1>';
            echo '<ul>';
            foreach ($triggers as $trigger_name => $trigger_id) {
                if(!array_key_exists($trigger_name, $triggers2)) {
                    echo '<li>El evento <b>'.$trigger_name.'</b> no se encuentra en la nueva versión</li>';
                }

                if(array_key_exists($trigger_name, $triggers2) && $triggers2[$trigger_name] != $trigger_id) {
                    echo '<li>El evento <b>'.$trigger_name.'</b> que tenía ID <b>'.$trigger_id.'</b> ahora tiene ID <b>'.$tags2[$trigger_name].'</b></li>';
                }
            }
            echo '</ul>';

            echo '<h1>Cambios en variables</h1>';
            echo '<ul>';
            foreach ($vars as $var_name => $var_id) {
                if(!array_key_exists($var_name, $vars2)) {
                    echo '<li>La variable <b>'.$var_name.'</b> no se encuentra en la nueva versión</li>';
                }

                if(array_key_exists($var_name, $vars2) && $vars2[$var_name] != $var_id) {
                    echo '<li>La variable <b>'.$var_name.'</b> que tenía ID <b>'.$var_id.'</b> ahora tiene ID <b>'.$tags2[$var_name].'</b></li>';
                }
            }
            echo '</ul>';



            echo '<h1>Nuevos tags</h1>';
            echo '<ul>';
            foreach ($tags2 as $tag_name => $tag_id) {
                if(!array_key_exists($tag_name, $tags)) {
                    echo '<li>Se añadió una nueva etiqueta <b>'.$tag_name.'</b> con ID <b>'.$tag_id.'</b></li>';
                }
            }
            echo '</ul>';

            echo '<h1>Nuevos eventos</h1>';
            echo '<ul>';
            foreach ($triggers2 as $trigger_name => $trigger_id) {
                if(!array_key_exists($trigger_name, $triggers)) {
                    echo '<li>Se añadió un nuevo evento <b>'.$trigger_name.'</b> con ID <b>'.$trigger_id.'</b></li>';
                }
            }
            echo '</ul>';

            echo '<h1>Nuevas variables</h1>';
            echo '<ul>';
            foreach ($vars2 as $var_name => $var_id) {
                if(!array_key_exists($var_name, $vars)) {
                    echo '<li>Se añadió una nueva variable <b>'.$var_name.'</b> con ID <b>'.$var_id.'</b></li>';
                }
            }
            echo '</ul>';



            die;
        }

		function remove_tag_parameter(&$workspace_data, $tagId, $param_key)
		{
			foreach ($workspace_data['containerVersion']['tag'] as $key => $tag) {
				if($tag['tagId'] == $tagId)
				{
					foreach ($tag['parameter'] as $key2 => $param) {
						if($param['key'] == $param_key)
						{
							unset($workspace_data['containerVersion']['tag'][$key]['parameter'][$key2]);
							$workspace_data['containerVersion']['tag'][$key]['parameter'] = array_values($workspace_data['containerVersion']['tag'][$key]['parameter']);
							break 2;
						}
					}
				}
			}
		}

		function remove_variable(&$workspace_data, $variableId)
		{
			foreach ($workspace_data['containerVersion']['variable'] as $key => $var) {
				if($var['variableId'] == $variableId)
				{
					unset($workspace_data['containerVersion']['variable'][$key]);
					$workspace_data['containerVersion']['variable'] = array_values($workspace_data['containerVersion']['variable']);
					break;
				}
			}
		}

		function check_license($license_id)
		{
			$conditions = array(
				'extension_id' => array(15609, 12707, 36731, '36731cec-8b87-4738-b96a-9118e4ee981f', '7f939980-65f2-4b0a-a614-581879e0e0cd', '3e3c4bf0-c8f7-446f-a474-d33315e74f2e'),
				'order_id' => $license_id,
				'order_status' => 'Complete'
			);
			$license = $this->Sale->find('first', array('conditions' => $conditions));

			//Devman Extensions - info@devmanextensions.com - 2017-04-03 15:11:41 - Fake license
				if($license_id == 123)
				{
					$license['Sale'] = array(
						'order_id' => 123,
						'date_added' => date('Y-m-d'),
						'buyer_email' => 'info@devmanextensions.com'
					);
					return $license;
				}
			//END

			if(empty($license['Sale']))
				return 'License Order ID not found';

			//Check expired
				try {
				    $this->OpencartExtension->license_check_expired($license);
				} catch (Exception $e) {
					return $e->getMessage();
				}
			//END

			return $license;
		}

		function send_gtm_workspace($license, $file_path, $domain)
		{
			$subject = 'Google Marketing Tools - Your Workspace'.(!empty($domain) ? ' - '.$domain : '');
			$content = '';

			App::uses('CakeEmail', 'Network/Email');

			$from_email = 'info@devmanextensions.com';

			$Email = new CakeEmail();
			$Email->from(array($from_email => 'DevmanExtensions'));
            $Email->config('gmail');
			$Email->viewVars(compact('license'));
			$email_to = trim($license['Sale']['buyer_email']);
			//$Email->to('info@devmanextensions.com');
			$Email->to($email_to);
			$Email->emailFormat('html');
			$Email->attachments($file_path);
			$Email->template('gtm_workspace');
			$Email->subject($subject);

			return $Email->send($content);
		}

		function check_license_uses($license, $gtm_id)
		{
			$license_id = $license['Sale']['order_id'];
			$num_containers = $license['Sale']['gmt_containers_num'];

			if($license_id == 123)
				return false;

			/*$licenses_unlimited = array('867553');
			if(in_array($license_id, $licenses_unlimited))
				return false;*/

			$workspaces = scandir('gmt_workspace/exported/');
			$licenses_in_use = array();

			foreach ($workspaces as $key => $workspace) {
				if(!in_array($workspace, array('..', '.')))
				{
					$exploded = explode('-', $workspace);
					$json_license_id = $exploded[0];
					$json_gtm_id = str_replace('.json', '', $exploded[1].'-'.$exploded[2]);
					if(count($exploded) == 3 && ($license_id == $json_license_id) && ($gtm_id != $json_gtm_id))
						$licenses_in_use[] = trim($json_gtm_id);
				}
			}

			$limit_over = count($licenses_in_use) > $num_containers || (count($licenses_in_use) == $num_containers && !in_array($gtm_id, $licenses_in_use));

			if($limit_over)
			{
				$string_gmt_id = '';
				foreach ($licenses_in_use as $key => $workspace_id) {
					$string_gmt_id .= '<b>'.$workspace_id.'</b>, ';
				}

				$string_gmt_id = rtrim($string_gmt_id, ', ');

				$link_renew = $this->api_url.'invoices/opencart/new_invoice?type=new_gmt_container&license_id='.$license_id;

				$message = sprintf('Your license <b>%s</b> only can generate on <b>%s</b> containers, currently you are using the containers: <b>%s</b> with your license. As a <b>loyal customer</b> you are entitled to a <b><u>-%s%% discount</u></b>. <a href="%s" target="_blank"><b>Increase your containers number in this link</b></a>.', $license_id, $num_containers, $string_gmt_id, $this->OpencartExtension->get_discount_renew(), $link_renew);

				return $message;
			}

			return false;
		}

		function string_encrypt($string) {
            $encrypt_method = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($encrypt_method);
            $options = 0;
            $vector = '1548556498215783';
            $key = "JDURY3HFYRHF";

            $string = openssl_encrypt($string, $encrypt_method,
                        $key, $options, $vector);

            return $string;
        }

        function string_decrypt($string) {
            $encrypt_method = "AES-128-CTR";
            $iv_length = openssl_cipher_iv_length($encrypt_method);
            $options = 0;
            $vector = '1548556498215783';
            $key = "JDURY3HFYRHF";

            $string = openssl_decrypt($string, $encrypt_method,
                        $key, $options, $vector);

            return $string;
        }

	}
?>
