<?php 
	class GmtController extends AppController {

		public  $uses = array(
      		'Sales.Sale',
            'Extensions.Extension',
		); 

		public function beforeFilter() {
	        $this->Auth->allow('get_workspace', 'exam_worspace_json');
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

                if($from_opencart) {
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

                    $ga_ua = !empty($config['google'.$all_prefix.'_analytics_ua']) ? $config['google'.$all_prefix.'_analytics_ua'] : '';

                    $conversion_status = !empty($config['google'.$all_prefix.'_conversion_status']) ? $config['google'.$all_prefix.'_conversion_status'] : '';
                    $conversion_id = !empty($config['google'.$all_prefix.'_conversion_id']) ? $config['google'.$all_prefix.'_conversion_id'] : '';
                    $conversion_label = !empty($config['google'.$all_prefix.'_conversion_label']) ? $config['google'.$all_prefix.'_conversion_label'] : '';

                    $dynamic_remarketing_status = !empty($config['google'.$all_prefix.'_dynamic_remarketing_status']) ? $config['google'.$all_prefix.'_dynamic_remarketing_status'] : '';
                    $dynamic_remarketing_id = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id'] : '';
                    $dynamic_remarketing_label = !empty($config['google'.$all_prefix.'_dynamic_remarketing_label']) ? $config['google'.$all_prefix.'_dynamic_remarketing_label'] : '';
                    $dynamic_remarketing_prefix = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id_prefix']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id_prefix'] : 'EMPTY';
                    $dynamic_remarketing_sufix = !empty($config['google'.$all_prefix.'_dynamic_remarketing_id_sufix']) ? $config['google'.$all_prefix.'_dynamic_remarketing_id_sufix'] : 'EMPTY';
                    $dynamic_remarketing_dynx = !empty($config['google'.$all_prefix.'_dynamic_remarketing_dynx']) ? true : false;

                    $enhanced_status = !empty($config['google'.$all_prefix.'_enhanced_ecommerce_status']) ? $config['google'.$all_prefix.'_enhanced_ecommerce_status'] : '';
                    $rich_nippets_status = !empty($config['google'.$all_prefix.'_rich_snippets']) ? $config['google'.$all_prefix.'_rich_snippets'] : '';

                    $hotjar_status = !empty($config['google'.$all_prefix.'_hotjar_status']) ? $config['google'.$all_prefix.'_hotjar_status'] : '';
                    $hotjar_site_id = !empty($config['google'.$all_prefix.'_hotjar_site_id']) ? $config['google'.$all_prefix.'_hotjar_site_id'] : '';

                    $pinterest_status = !empty($config['google'.$all_prefix.'_pinterest_status']) ? $config['google'.$all_prefix.'_pinterest_status'] : '';
                    $pinterest_id = !empty($config['google'.$all_prefix.'_pinterest_id']) ? $config['google'.$all_prefix.'_pinterest_id'] : '';

                    $crazyegg_status = !empty($config['google'.$all_prefix.'_crazyegg_status']) ? $config['google'.$all_prefix.'_crazyegg_status'] : '';
                    $crazyegg_id = !empty($config['google'.$all_prefix.'_crazyegg_id']) ? $config['google'.$all_prefix.'_crazyegg_id'] : '';
                    
                    $facebook_pixel_status = !empty($config['google'.$all_prefix.'_facebook_pixel_status']) ? $config['google'.$all_prefix.'_facebook_pixel_status'] : '';
                    $facebook_pixel_id = !empty($config['google'.$all_prefix.'_facebook_pixel_id']) ? $config['google'.$all_prefix.'_facebook_pixel_id'] : '';

                    $bing_ads_status = !empty($config['google'.$all_prefix.'_bing_ads_status']) ? $config['google'.$all_prefix.'_bing_ads_status'] : '';
                    $bing_ads_tag_id = !empty($config['google'.$all_prefix.'_bing_ads_tag_id']) ? $config['google'.$all_prefix.'_bing_ads_tag_id'] : '';

                    $criteo_status = !empty($config['google'.$all_prefix.'_criteo_status']) ? $config['google'.$all_prefix.'_criteo_status'] : '';
                    $criteo_id = !empty($config['google'.$all_prefix.'_criteo_id']) ? $config['google'.$all_prefix.'_criteo_id'] : '';

                    $yahoo_status = !empty($config['google'.$all_prefix.'_yahoo_status']) ? $config['google'.$all_prefix.'_yahoo_status'] : '';
                    $yahoo_project_id = !empty($config['google'.$all_prefix.'_yahoo_project_id']) ? $config['google'.$all_prefix.'_yahoo_project_id'] : '';
                    $yahoo_pixel_id = !empty($config['google'.$all_prefix.'_yahoo_pixel_id']) ? $config['google'.$all_prefix.'_yahoo_pixel_id'] : '';

                    $domodi_status = !empty($config['google'.$all_prefix.'_domodi_pixel_status']) ? $config['google'.$all_prefix.'_domodi_pixel_status'] : '';
                    $domodi_pixel_id = !empty($config['google'.$all_prefix.'_domodi_pixel_id']) ? $config['google'.$all_prefix.'_domodi_pixel_id'] : '';
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

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = false;
                } elseif($from_woocommerce) {
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

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = false;
                } elseif($from_magento) {
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

                    $yahoo_status = $yahoo_project_id = $yahoo_pixel_id = $domodi_pixel_id = false;
                }

                $license_id = trim($license_id);
                $container_id = trim($container_id);

                //echo '<pre>'; print_r($this->request->data);  echo '</pre>'; die;

				if(empty($license_id))
					$this->die_error('Fill License Order ID');
				if(empty($container_id))
					$this->die_error('Fill Google Tag Manager container ID');
				if( (empty($ga_ua) && $from_opencart) || (!$from_opencart && $ga_status && empty($ga_ua)))
					$this->die_error('Universal GA or GA4 property is requied.');
				if(empty($ga_ua) && !empty($container_optimize_id))
				    $this->die_error('Google Optimize needs that Google Analytics is filled');
				if(empty($ga_ua) && !empty($enhanced_status))
				    $this->die_error('Enhanced Ecommerce needs that Google Analytics is filled');
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
				if(!empty($bing_ads_status) && empty($bing_ads_tag_id))
					$this->die_error('Fill Bing Ads tag ID');
				if(!empty($yahoo_status) && empty($yahoo_project_id))
					$this->die_error('Fill Yahoo - Native & Search Dot > Project ID');
				if(!empty($yahoo_status) && empty($yahoo_pixel_id))
					$this->die_error('Fill Yahoo - Native & Search Dot > Pixel ID');
				if(!empty($domodi_status) && empty($domodi_pixel_id))
					$this->die_error('Fill Domodi Pixel > Shop key');

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

                $using_ga4 = !empty($ga_ua) && strpos($ga_ua, 'UA-') === false;
                $delete_ga4 = !empty($ga_ua) && !$using_ga4;
                $delete_ga = !empty($ga_ua) && $using_ga4;

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 09:39:03 - Get workspace json, replace all values first.
					$workspace_data_json = file_get_contents(WWW_ROOT . DS . 'gmt_workspace/'.$workspace_name);
					$workspace_data_json = str_replace('UA-94017251-1', $ga_ua, $workspace_data_json);
					$workspace_data_json = str_replace($container_id_name, $container_id, $workspace_data_json);
					$workspace_data_json = str_replace('GOOGLEOPTIMIZECONTAINERID', $container_optimize_id, $workspace_data_json);
					$workspace_data_json = str_replace('1550553151909443', $facebook_pixel_id, $workspace_data_json);
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

					$ga_tags = array(32);
					$ga_tags = array(32);
					$ga4_tags = array(126);
                    $ee_tags = array(7,8,11,30,31,33,34,35,36);
                    $ee_ga4_tags = array(128,133,136,138,139,140,142,151,154);
                    $fb_tags = array(14,15,16,17);
                    $rich_snippets_tags = array(19);
                    $dynamic_remarketing_tags = array(20);
                    $conversion_tags = array(21);
                    $bing_ads_tags = array(1,2,3);
                    $criteo_one_tags = array(29);
                    $hotjar_tags = array(22);
                    $pinterest_tags = array(24,23,25,26,27);
                    $crazyegg_tags = array(4);
                    $go_tags = array(28);
                    $yahoo_tags = array(110,111);
                    $domodi_tags = array(113,114,115,116);

					$copy_tags = $workspace_data['containerVersion']['tag'];
					foreach ($copy_tags as $key => $tag) {
					    $delete =
                            (($delete_ga && in_array($tag['tagId'], $ga_tags)) || (empty($ga_ua) && in_array($tag['tagId'], $ga_tags))) ||
                            (($delete_ga4 && in_array($tag['tagId'], $ga4_tags)) || (empty($ga_ua) && in_array($tag['tagId'], $ga4_tags))) ||
                            ((in_array($tag['tagId'], $ee_tags) || in_array($tag['tagId'], $ee_ga4_tags)) && (empty($enhanced_status) || (!empty($enhanced_status) && in_array($tag['tagId'], $ee_ga4_tags) && $delete_ga4) || (!empty($enhanced_status) && in_array($tag['tagId'], $ee_tags) && $delete_ga))) ||
                            (empty($conversion_status) && in_array($tag['tagId'], $conversion_tags)) ||
                            (empty($facebook_pixel_status) && in_array($tag['tagId'], $fb_tags)) ||
                            (empty($criteo_status) && in_array($tag['tagId'], $criteo_one_tags)) ||
                            (empty($rich_nippets_status) && in_array($tag['tagId'], $rich_snippets_tags)) ||
                            (empty($dynamic_remarketing_status) && in_array($tag['tagId'], $dynamic_remarketing_tags)) ||
                            (empty($bing_ads_status) && in_array($tag['tagId'], $bing_ads_tags)) ||
                            (empty($hotjar_status) && in_array($tag['tagId'], $hotjar_tags)) ||
                            (empty($pinterest_status) && in_array($tag['tagId'], $pinterest_tags)) ||
                            (empty($crazyegg_status) && in_array($tag['tagId'], $crazyegg_tags)) ||
					        ((empty($container_optimize_id) && in_array($tag['tagId'], $go_tags)) || (in_array($tag['tagId'], $go_tags) && $using_ga4)) ||
                            (empty($yahoo_status) && in_array($tag['tagId'], $yahoo_tags)) ||
                            (empty($domodi_status) && in_array($tag['tagId'], $domodi_tags));

					    if($delete)
						    unset($workspace_data['containerVersion']['tag'][$key]);
					}

					if(empty($criteo_status) || (!empty($criteo_status) && !$from_woocommerce)) {
					    //Delete criteo one tag - tag purchase, only is for woocommerce
                        $copy_tags = $workspace_data['containerVersion']['tag'];
                        foreach ($copy_tags as $key => $tag) {
                            $delete = $tag['tagId'] == 37;
                            if($delete)
                                unset($workspace_data['containerVersion']['tag'][$key]);
                        }
                    }

					$workspace_data['containerVersion']['tag'] = array_values($workspace_data['containerVersion']['tag']);
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 20:27:44 - Remove triggers
					/*foreach ($workspace_data['containerVersion']['trigger'] as $key => $trigger) {
						echo $trigger['triggerId'].' - '.$trigger['name']."\n";
					} die;*/

                    $fb_triggers = array(9);
                    $dynamic_remarketing_triggers = array(10);
                    $ee_triggers = array(5,15,19,20,24,23,25);
                    $rich_nippets = array(36);

					$copy_triggers = $workspace_data['containerVersion']['trigger'];
					foreach ($copy_triggers as $key => $trigger) {
						if(empty($facebook_pixel_status) && in_array($trigger['triggerId'], $fb_triggers))
							unset($workspace_data['containerVersion']['trigger'][$key]);
						if(empty($dynamic_remarketing_status) && in_array($trigger['triggerId'], $dynamic_remarketing_triggers))
							unset($workspace_data['containerVersion']['trigger'][$key]);
						if(empty($enhanced_status) && in_array($trigger['triggerId'], $ee_triggers))
							unset($workspace_data['containerVersion']['trigger'][$key]);
						if(empty($rich_nippets_status) && in_array($trigger['triggerId'], $rich_nippets))
							unset($workspace_data['containerVersion']['trigger'][$key]);
					}
					$workspace_data['containerVersion']['trigger'] = array_values($workspace_data['containerVersion']['trigger']);
				//END

				//Devman Extensions - info@devmanextensions.com - 2017-04-02 15:09:57 - Remove variables
					/*foreach ($workspace_data['containerVersion']['variable'] as $key => $var) {
						echo $var['variableId'].' - '.$var['name']."\n";
					} die;*/

					$ga_variables = array(74);
                    $ee_variables = array(9,13,15,65,19,21,51);
                    $ee_ga4_variables = array(129,130,131,132,134,135,137,141,143,144,145,146,147,148,149,150,152);
                    $fb_variables = array(22,47,48);
                    $rich_snippets_variables = array(24);
                    $dynamic_remarketing_variables = array(66,26,27,31,67,68,29,77,78);
                    $conversion_variables = array(70,71);
                    $bing_ads_variables = array(62);
                    $criteo_variables = array(4,6,64,5);
                    $hotjar_variables = array(72);
                    $pinterest_variables = array(37,73);
                    $crazyegg_variables = array(63);
                    $google_optimize_variables = array(69);
                    $yahoo_variables = array(98,99);
                    $domodi_variables = array(117);

					$copy_variables = $workspace_data['containerVersion']['variable'];
					foreach ($copy_variables as $key => $var) {
					    $delete =
                            (empty($ga_ua) && in_array($var['variableId'], $ga_variables)) ||
                            ((in_array($var['variableId'], $ee_variables) || in_array($var['variableId'], $ee_ga4_variables)) && (empty($enhanced_status) || (!empty($enhanced_status) && in_array($var['variableId'], $ee_variables) && $delete_ga) || (!empty($enhanced_status) && in_array($var['variableId'], $ee_ga4_variables) && $delete_ga4))) ||
                            (empty($conversion_status) && in_array($var['variableId'], $conversion_variables)) ||
                            (empty($facebook_pixel_status) && in_array($var['variableId'], $fb_variables)) ||
                            (empty($criteo_status) && in_array($var['variableId'], $criteo_variables)) ||
                            (empty($rich_nippets_status) && in_array($var['variableId'], $rich_snippets_variables)) ||
                            (empty($dynamic_remarketing_status) && in_array($var['variableId'], $dynamic_remarketing_variables)) ||
                            (empty($bing_ads_status) && in_array($var['variableId'], $bing_ads_variables)) ||
                            (empty($hotjar_status) && in_array($var['variableId'], $hotjar_variables)) ||
                            (empty($pinterest_status) && in_array($var['variableId'], $pinterest_variables)) ||
                            (empty($crazyegg_status) && in_array($var['variableId'], $crazyegg_variables)) ||
					        ((empty($container_optimize_id) && in_array($var['variableId'], $google_optimize_variables)) || (in_array($var['variableId'], $google_optimize_variables) && $using_ga4)) ||
                            (empty($yahoo_status) && in_array($var['variableId'], $yahoo_variables)) ||
                            (empty($domodi_status) && in_array($var['variableId'], $domodi_variables));

					    if($delete)
						    unset($workspace_data['containerVersion']['variable'][$key]);
					}
					$workspace_data['containerVersion']['variable'] = array_values($workspace_data['containerVersion']['variable']);

					//Conversion label empty - Remove label from tag and tag constant variable
						if(!empty($conversion_status) && empty($conversion_label))
						{
						    $this->remove_tag_parameter($workspace_data, 21, 'conversionLabel');
						    $this->remove_variable($workspace_data, 71);
						}

					//Dynamic remarketing label empty - Remove label from tag and tag constant variable
						if(!empty($dynamic_remarketing_status) && empty($dynamic_remarketing_label))
						{
						    $this->remove_tag_parameter($workspace_data, 20, 'conversionLabel');
						    $this->remove_variable($workspace_data, 27);
						}
				    //END

                //<editor-fold desc="Remove GA custom dimmensions if GDR is disabled">
                    if(!$dynamic_remarketing_status && (!isset($ga_status) || $ga_status)) {
						$copy_tags = $workspace_data['containerVersion']['tag'];
                        foreach ($copy_tags as $key => $tag) {
                            $delete = in_array($tag['tagId'], $ga_tags);

                            if ($delete) {
                                //Custom dimensions 2 and 3
                                unset($workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list'][1]);
                                unset($workspace_data['containerVersion']['tag'][$key]['parameter'][9]['list'][2]);
                            }
                        }
                    }
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

		function exam_worspace_json()
		{
			$workspace_data_json = json_decode(file_get_contents(WWW_ROOT . DS . 'gmt_workspace/exam.json'));
			echo '<pre>'; print_r($workspace_data_json);  echo '</pre>'; die;
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
			$email_to = $license['Sale']['buyer_email'];
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
	}
?>