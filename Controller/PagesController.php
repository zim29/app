<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link https://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {
	var $layout = 'frontend';
/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('Sales.Sale', 'Session', 'Extensions.Extension');
	public $components = array('Client', 'CountryTools', 'ExtensionTool', 'Cart', 'ApiLicenses', 'Email');

/**
 * Displays a view
 *
 * @return CakeResponse|null
 * @throws ForbiddenException When a directory traversal attempt.
 * @throws NotFoundException When the view file could not be found
 *   or MissingViewException in debug mode.
 */
	public function display() {
		$this->set('cart_count', $this->Cart->count_products());
		$this->set('cart_products', $this->Cart->get_products());

		$shop_pass = array('extensions-shop', 'extensions-shop-opencart', 'extensions-shop-woocommerce', 'extensions-shop-prestashop', );
        $page = '';
		$datalayer = '';
        $meta_keywords = 'opencart extensions, opencart plugins, opencart addons, best opencart extensions, opencart premium extensions';
        $meta_title = 'Developers of extensions and modules for opencart';
        $meta_description = '👆Enter and discover DevMan, experts in the development of extensions and modules for Opencart with more than 10 years of experience.';


		$black_friday = $this->Extension->is_black_friday();
		$this->set("black_friday", $black_friday);

        if(!empty($this->request->params) && array_key_exists('pass', $this->request->params) && array_key_exists(0, $this->request->params['pass']))
		{
		    $page = $this->request->params['pass'][0];
			$is_home = $page == 'home';
			$is_products = $page == 'products';
			$is_testimonial_insert = $page == 'testimonial-insert';
			$is_star_products = $page == 'our-star-products';
			$is_testimonials = $page == 'testimonials';
			$is_services = in_array($page, array('services'));
			$is_shop = in_array($page, $shop_pass);
			$is_discount = $page == 'discount';
			$is_cart = $page == 'cart';
			$is_sitemaps = $page == 'sitemap';
			$is_video_tutorial = $page == 'video-tutorials';
			$is_open_ticket = $page == 'open_ticket';
			$is_gmt_additional_services = $page == 'google-marketing-tools-services';
			$is_kit_digital = $page == 'kit-digital';
			$is_black_friday = $page == 'black-friday' || $page == 'end-year-offers';
		}
		if(!empty($is_black_friday)) {
			$meta_title = 'Black Friday 2023';
			$meta_description = 'Opencart Black Friday 2023';
			$extensions = $this->ExtensionTool->get_in_shop_extensions();
			$this->set('extensions', $extensions);

		}
		if(!empty($is_testimonials)) {
            $meta_title = 'Reviews from our Clients, 100% Real Cases';
            $meta_description = 'Simply Outstanding, Fast Response Time, Great Support, these are some Testimonials from Our Clients. More than 9.000 Business are Growing With Us, What Are you Waiting For?';
        }
        if(!empty($is_open_ticket)) {
            $meta_title = 'Open a ticket and we will attend you asap!';
            $meta_description = 'Contact us for Opencart Extensions to make software Simpler and Better. Experts in Opencart providing Modules, Plugins, Themes and Custom Development.';
        }
        if(!empty($is_services)) {
            $meta_title = 'Custom Development for Opencart';
            $meta_description = 'More than 12 years of experience developing Custom Programing &Development in Opencart providing Modules, Plugins, Themes and so on. Extreme Quality and Performance.';
        }
        if(!empty($is_shop)) {
            $meta_title = 'Opencart Extensions Store';
            $meta_description = '✔️Store of Extensions, Modules and Themes for Opencart with a very high level of acceptance. We provide technical support of the highest quality.';
        }

		//Search page in SEO URL extensions
        if(!empty($page)) {
		    $this->Extension->recursive = 1;
		    $extension = $this->Extension->findBySeoUrl($page);

		    if(!empty($extension)) {

				//Datalayer
				$extension_data = $this->Extension->formatExtensionToDatalayer($extension);

                $datalayer .= '
                    <script>
                        dataLayer.push({
                            "event": "viewProduct",
                            "viewProduct": ' . json_encode($extension_data) . '
                        });
                    </script>
                ';

                $this->set("datalayer", $datalayer);

		        $this->set('force_active_menu_item', 'shop');
		        $this->set('ext', $extension);
		        $this->set('tematic', $extension['Extension']['system']);

		        $force_render = 'shop-extension';
		        $meta_keywords = !empty($extension['Extension']['meta_keywords']) ? $extension['Extension']['meta_keywords'] : $meta_keywords;
		        $meta_description = !empty($extension['Extension']['meta_description']) ? $extension['Extension']['meta_description'] : $meta_description;
		        $meta_title = !empty($extension['Extension']['meta_title']) ? $extension['Extension']['meta_title'] : $meta_title;

		        /*if($extension['Extension']['id'] == '542068d4-ed24-47e4-8165-0994fa641b0a')
		            $force_render = 'shop-extension-ie-pro';*/

				$this->Session->write("richsnippets", $extension['Extension']['rich_snippets']);
            }
        }

        if(!empty($is_products))
		{
			$extensions = $this->ExtensionTool->get_in_shop_extensions();
			$this->set(compact('extensions'));
		}

		if(!empty($is_home))
		{
			$clients_total_num = $this->Client->get_number_total_of_clients();
			$this->set('clients_total_num', $clients_total_num);
            $star_products = $this->ExtensionTool->get_start_products();
			$this->set(compact('star_products'));
		}

		if(!empty($is_testimonial_insert))
		{
			$countries = $this->CountryTools->select_format_countries();
			$this->set(compact('countries'));
		}

		if(!empty($is_star_products))
		{
			$star_products = $this->ExtensionTool->get_start_products();
			$this->set(compact('star_products'));
		}

		if(!empty($is_testimonials) || !empty($is_home))
		{
			$testimonials = $this->Client->get_testimonials(!empty($is_home));
			$this->set('testimonials', $testimonials);
            $testimonials_by_country = $this->Client->get_testimonials_by_country();
            $this->set('testimonials_by_country', $testimonials_by_country);

            $count = 0;
            foreach ($testimonials_by_country as $key => $tests) {
                $count += $tests['TestimonialByCountry']['number'];
            }
            $this->set('testimonials_count', $count);
		}

		if(!empty($is_services))
		{
			$num_sales = $this->Client->get_number_total_of_clients(true, false);
			$this->set('num_sales', $num_sales);
			$num_invoices = $this->Client->get_number_total_of_clients(false, true);
			$this->set('num_invoices', $num_invoices);
		}

		if(!empty($is_sitemaps)) {
		    $this->Extension->recusive = -1;
		    $extensions = $this->Extension->find('all', array('conditions' => array('Extension.in_shop' => 1), 'fields' => array('Extension.system', 'Extension.name', 'Extension.seo_url'), 'recursive' => -1));
            $this->set('extensions', $extensions);
		}

		if(!empty($is_shop))
		{
		    $this->set('force_active_menu_item', 'shop');

		    $system_button_default = 'opencart';
		    if($page == 'extensions-shop-woocommerce')
		        $system_button_default = 'woocommerce';

		    $this->set('system_button_default', $system_button_default);

            $title = __('Extensions Shop');

			$extensions = $this->ExtensionTool->get_in_shop_extensions();
			$this->set('extensions', $extensions);
		    $this->set('title', $title);

		    $force_render = 'shop';
		}

		if(!empty($is_cart))
		{
		    $products = $this->Cart->get_products();

            if(array_key_exists('automatic_add_product', $_GET)) {

                $extension_id = $_GET['automatic_add_product'];
                $add_product = true;
                foreach ($products as $prod) {
                    if($prod['id'] == $extension_id)
                        $add_product = false;
                }
                if($add_product)
                    $this->Cart->add_product($_GET['automatic_add_product']);
			}

			$auto_add = $this->Session->read('auto_product_add');

			if(!empty($auto_add)) {
			    $this->Session->write('auto_product_add', array());
                $this->Cart->add_product($auto_add['auto_add_extension_id'], $auto_add['auto_add_units']);
			}

			//Devman Extensions - info@devmanextensions.com - 25/5/24 18:15 - New method for auto add to cart
			if(!empty($_GET['auto_add_extension_id'])) {

				$units = !empty($_GET['auto_add_units']) && is_int($_GET['auto_add_units']) ? $_GET['auto_add_units'] : 1;
				$this->Cart->add_product($_GET['auto_add_extension_id'], $units);
			}

			if(array_key_exists('automatic_add_discount', $_GET) && array_key_exists('automatic_add_product', $_GET)) {
            	$extension_id = $_GET['automatic_add_product'];
                if(!array_key_exists($extension_id, $discounts))
                    $this->Cart->add_discount($_GET['automatic_add_product'], $_GET['automatic_add_discount']);
			}

			if(array_key_exists('automatic_add_discount_general', $_GET)) {
                $this->Cart->add_discount_general($_GET['automatic_add_discount_general']);
			}

			$products = $this->Cart->get_products();
			$discounts = $this->Cart->get_discounts();

			$this->set('products', $products);
			$this->set('discounts', $discounts);
			$this->set('cart_total', $this->Cart->get_total());
		}


		if(!empty($is_discount)) {
			$extension_id = array_key_exists('extension_id', $_GET) ? $_GET['extension_id'] : '';

			try {
				$extension = $this->ExtensionTool->get_extension($extension_id);
				$this->set('extension', $extension);
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
				$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
			}
		}

		if(!empty($is_video_tutorial)) {
		    $id = array_key_exists('id', $_GET) ? $_GET['id'] : '';
		    $this->set('id', $id);
		    $language = array_key_exists('language', $_GET) ? $_GET['language'] : 'en_GB';
		    $this->set('language', $language);
        }

        if(!empty($is_open_ticket)) {
		    $holidays_from = Configure::read('holidays_from').' 00:00:00';
		    $holidays_to = Configure::read('holidays_to').' 23:59:59';
            $today = date('Y-m-d H:i:s');
            $is_holidays = ($today > $holidays_from) && ($today < $holidays_to);
            $this->set('is_holidays', $is_holidays);
            $this->set('holidays_from', date('d/m/Y', strtotime($holidays_from)));
            $this->set('holidays_to', date('d/m/Y', strtotime($holidays_to)));
        }

        if(!empty($is_kit_digital)) {
            $meta_title = 'Kit Digital - Somos agente digitalizados';
            $meta_description = 'DevmanExtensions es agente digitalizador';

            if($this->request->is(array('post','put'))){
                try{

				//Devman Extensions - info@devmanextensions.com - 2016-10-12 19:42:38 - Captcha
					$userIP = $_SERVER["REMOTE_ADDR"];
				    $recaptchaResponse = $this->request->data['g-recaptcha-response'];
				    $secretKey = "6LeNxKAUAAAAAPNyzGddMVFpZLZUefwO4E3HXTh7";

				    $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

				    if(!strstr($request, "true"))
				    	throw new Exception('Complete el captcha');
				//END

                $content = '';

                $content = 'Nombre: '.$this->request->data['name'].'<br>';
                $content .= 'Email: '.$this->request->data['email'].'<br>';
                $content .= 'Teléfono: '.$this->request->data['phone'].'<br>';
                $content .= 'Empresa: '.$this->request->data['empresa'].'<br>';
                $content .= 'Empleados: '.$this->request->data['employees'].'<br>';
                $content .= 'Mensaje: '.$this->request->data['message'].'<br>';
                $this->Email->send_email('info@devmanextensions.com', 'kit-digital@devmanextensions.com', trim($this->request->data['email']), 'Nueva petición kit digital', $content);

				$this->Session->setFlash(
					'<i class="fa fa-paper-plane"></i> <b>Mensaje enviado</b>, Nos pondremos en contacto con usted lo antes posible.'
				);

				$this->data = $this->request->data = array();
			}catch(Exception $e){
				$this->Session->setFlash($e->getMessage(), 'default',array('class' => 'error'));
			}
            }
        }
        if(!empty($is_gmt_additional_services)) {
            $services = array(
                array(
                    'code' => 'install',
                    'price' => 30,
                    'title' => 'Install',
                    'what_is_it' => '<p>Our team will install Google Marketing Tools in your shop and solve conflicts (if appear these).</p>',
                    'what_include' => '<ol>
                       <li>Install Google Marketing Tools in your store.</li>
                       <li>Solve conflicts (if appear these).</li>
                       </ol>'
                ),
                array(
                    'code' => 'configuration_tests',
                    'price' => 50,
                    'title' => 'Configuration + tests',
                    'what_is_it' => '<p>Our team will make sure that Google Marketing Tools working well, we will do Google tag manager workspace import, publish your container and we will check all your frontend views (home, product, category, cart, checkout, purchase...) to make sure that all datalayer is being generated correctly. <b>The client will be in charge of create and configure external accounts (GTM, Google analytics, google ads....) and pass us the data required (GA-UA, GTM-ID, Conversions ID.....)</b></p>',
                    'what_include' => '<ol>
                       <li>Generate workspace and import it in google tag manager account.</li>
                       <li>Check all views to make sure that all datalayer is being formed correctly.</li>
                       <li>Make order tests to make sure that conversions are working.</li>
                       <li>Create a feed test to make sure that feed system is working correctly.</li>
                       </ol>'
                ),
                array(
                    'code' => 'google_tag_manager',
                    'price' => 35,
                    'title' => 'Google Tag Manager',
                    'what_is_it' => '<p>Google Tag Manager is a great tool for business owners or marketing teams to see how their website works: what areas are working, what performance is underperforming, what features are not being used as you thought, and so on.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Import Container from GMT to GTM and Publish.</li>
                       </ol>'
                ),
                array(
                    'code' => 'google_analytics',
                    'price' => 100,
                    'title' => 'Google Analytics',
                    'what_is_it' => '<p>Web Analytics with Google Analytics is today a fundamental part when we are implementing a website. Understanding what users do is not a luxury, it is a necessity.</p>
                    <p>Google Analytics provides a lot of information and usually, in its installation, some mistakes are made that you should avoid, with our Web Analytics Consulting Service you will proactively analyze your digital metrics and provide the necessary recommendations to achieve your business goals.</p>',
                    'what_include' => '<ol>
                                        <li>Property Configuration
                                            <ol>
                                                <li>Search Console</li>
                                                <li>Data Collection</li>
                                                <li>Data Retention</li>
                                                <li>User ID</li>
                                                <li>Referral Exclusion List</li>
                                                <li>Google Ads Linking</li>
                                                <li>Audience Definitions (Remarketing)</li>
                                                <li>Custom Dimensions</li>
                                                <li>Custom Metrics</li>
                                            </ol>
                                        </li>
                                        <li>View Configuration
                                            <ol>
                                                <li>At least 3 views needed (Main - Filtered - Testing)</li>
                                                <li>Time Zone and Country</li>
                                                <li>Currency</li>
                                                <li>Default page</li>
                                                <li>Site Search Tracking</li>
                                                <li>Filters
                                                    <ol>
                                                        <li>Bot filters</li>
                                                        <li>Exclude internal traffic</li>
                                                        <li>Include hostnames</li>
                                                        <li>Lowercase Search terms</li>
                                                        <li>Include traffic medium</li>
                                                        <li>Include device category</li>
                                                        <li>Include Country</li>
                                                    </ol>
                                                </li>
                                            </ol>
                                        </li>
                                        <li>Goals
                                            <ol>
                                                <li>Destination</li>
                                                <li>Durations</li>
                                                <li>Pages/Screens per Session</li>
                                                <li>Event</li>
                                            </ol>
                                        </li>
                                        <li>Ecommerce Setting</li>
                                        </ol>'
                ),
                array(
                    'code' => 'google_ads',
                    'price' => 120,
                    'title' => 'Google Ads (Adwords)',
                    'what_is_it' => '
                        <p>If you are going to start a strategy to get customers on the Internet, we recommend you start with paid traffic (Adwords) because:</p>
                        <ol>
                            <li>It allows you to advertise in the top positions immediately.</li>
                            <li>It allows you to accurately measure how much it costs to acquire a potential customer through this channel.</li>
                            <li>It is very effective because you only pay when someone interested in the service or product clicks on the ad.</li>
                        </ol>
                    ',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Billing Set Up.</li>
                        <li>Merchant Center Linking.</li>
                        <li>Setup Conversion Pixel.</li>
                        <li>Setup Remarketing Pixel.</li>
                        <li>Audience Creation.</li>
                        <li>Basic Negative Keywords.</li>
                       </ol>'
                ),
                array(
                    'code' => 'search_console',
                    'price' => 50,
                    'title' => 'Google Search Console',
                    'what_is_it' => '<p>Google Search Console (GSC) is a platform for websites to monitor how Google views their site and optimize its organic presence. That includes viewing your referring domains, mobile site performance, rich search results, and highest-traffic queries and pages. So, if you are trying to get Organic visits in your site through Google you should use GSC.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Google Analytics Linking.</li>
                       </ol>'
                ),
                array(
                    'code' => 'google_optimize',
                    'price' => 50,
                    'title' => 'Google Optimize',
                    'what_is_it' => '<p>Google Optimize is a Google platform that allows users to perform different A/B testing campaigns and personalization of a website using data collected from Google Analytics to works based on your target.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Google Analytics Linking.</li>
                       </ol>'
                ),
                array(
                    'code' => 'google_my_business',
                    'price' => 50,
                    'title' => 'Google My Business',
                    'what_is_it' => '
                    <p>Google My Business is a completely free Google tool (for now) by which local businesses have the opportunity to get more customers.</p>
                    <p>By creating a GMB account what you are doing is being part of Google\'s business directory, which will benefit you when it comes to improving the visibility of your project in the area where it is located.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Account verification.</li>
                       </ol>'
                ),
                array(
                    'code' => 'google_merchant_center',
                    'price' => 150,
                    'title' => 'Google Merchant Center',
                    'what_is_it' => '
                    <p>The primary goal of the Google Merchant Center is to allow businesses to upload and maintain product information, including pictures and pricing, to be displayed in relevant Google Shopping searches. The Google Merchant Center also integrates into other Google services, such as Google My Business, to allow robust oversight and control of Google-based marketing and e-commerce.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Program depends of Country.</li>
                       </ol>'
                ),
                array(
                    'code' => 'facebook',
                    'price' => 150,
                    'title' => 'Facebook',
                    'what_is_it' => '
                    <p>Facebook and Instagram Ads can be as simple or sophisticated as you want them to be. Create and run campaigns using simple self-serve tools, and track their performance with easy-to-read reports.</p>
                    <li>Facebook pixel implementation.</li>
                    <li>Facebook events.</li>
                    <li>Facebook remarketing.</li>
                    <li>Audience optimization.</li>
                    ',
                    'what_include' => '<ol>
                        <li>Facebook Business Setup.</li>
                        <li>Pixel Setup.</li>
                        <li>Events Setup.</li>
                        <li>Conversion Setup.</li>
                        <li>Audiences Setup.</li>
                       </ol>'
                ),
                array(
                    'code' => 'criteo',
                    'price' => 100,
                    'title' => 'Criteo OneTag',
                    'what_is_it' => '<p><a target="_blank" href="https://support.criteo.com/s/article?article=202726972-Criteo-OneTag-explained&language=en_US">About Criteo OneTag</a>.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Account configuration.</li>
                        <li>Criteo OneTag configuration.</li>
                       </ol>'
                ),
                array(
                    'code' => 'pinterest',
                    'price' => 50,
                    'title' => 'Pinterest',
                    'what_is_it' => '<p>Pinterest is a social network which can significantly increase the results of an online store by creating notoriety, reach, eCommerce traffic and even sales conversions, currently, it has more than 100 million users.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Ads Business creation.</li>
                        <li>Pixel setup.</li>
                       </ol>'
                ),
                array(
                    'code' => 'bing',
                    'price' => 50,
                    'title' => 'Bing ads',
                    'what_is_it' => '<p>Bing Ads is the strongest alternative to Google Ads, both in terms of the potential audience it reaches and its segmentation and configuration options, is a service that offers pay-per-click advertising on both Bing and Yahoo! Search.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Pixel setup.</li>
                       </ol>'
                ),
                array(
                    'code' => 'hotjar',
                    'price' => 50,
                    'title' => 'Hotjar',
                    'what_is_it' => '<p>Hotjar allows you to visualize how users engage with your site. Hotjar uses interactive heatmaps of their clicks and actions, recordings of their sessions, and gathering of their words from survey and feedback polls to help you build a strong, data-backed understanding of what exactly people are using your site for, and how they’re using it.</p>',
                    'what_include' => '<ol>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Pixel setup</li>
                       </ol>'
                ),
                array(
                    'code' => 'mailchimp',
                    'price' => 95,
                    'title' => 'Mailchimp (NOT FREE)',
                    'what_is_it' => '<p>With Google Marketing Tools + Mailchimp you will can create a campaign for Abandoned carts, forcing to your customers to recover his cart and finish his purchase.</p>
                        <p><b>IMPORTANT:</b> For make this type of campaigns on mailchimp, you will need the Standard plan (not free).</p>',
                    'what_include' => '<ol>
                        <li>Configure mailchimp in Google Marketing Tools.</li>
                        <li>Account creation (if you haven\'t it).</li>
                        <li>Make campaign.</li>
                        <li>Make sure that campaign is sending automatic emails for abandoned carts.</li>
                       </ol>'
                ),
            );

            $this->set('services', $services);

            $services = array(
                array(
                    'code' => 'seo_sem_audit',
                    'price' => 150,
                    'title' => 'SEO/SEM Audit',
                    'what_is_it' => '<p>In order to ensure that a website meets all the requirements of high visibility on the result pages of the search engines (Google), it is indispensable to carry periodically conduct an SEO audit. When auditing your website, you will have the necessary optimization measures to achieve sustained success in Google and increase traffic to your site.</p>
                    <p>Regarding SEM, understand the current state of their campaigns, various opportunities for improvement, ways to expand their campaigns to reach more targeted people, and all with the ultimate goal of boosting ROI. The audit results can help advertisers set a solid foundation for their paid search campaigns.</p>',
                    'what_include' => '
                        <ol>
                            <li>On-Page SEO Checker</li>
                            <li>Href lang analysis</li>
                            <li>Site Performance</li>
                            <li>Internal Linking</li>
                            <li>Schema Analysis</li>
                            <li>Competitors Analysis</li>
                            <li>Backlinks Audit</li>
                            <li>Paid Analysis</li>
                            <li>Pixel Revisions</li>
                        </ol>
                    '
                ),
                array(
                    'code' => 'monthly_plan_seo_sem',
                    'price' => 0,
                    'title' => 'Monthly plan SEO/SEM',
                    'what_is_it' => '<h2>THE PRICE OF THIS SERVICE WILL BE ESTIMATED AFTER DO SEO/SEM AUDIT</h2><br><p>It is to generate growth to your company from different technologies that generate visits, users or conversions.</p>
                        <p>An SEO strategy is not unique, it will depend on the project. Positioning at the top of Google is not a matter of two days, but of constancy and perseverance, along with a strategy and setting actions, prioritizing them, will help you achieve your goals.</p>
                        <p>If you are going to start a strategy to get customers on the Internet, we recommend you start with paid traffic (SEM) because It allows you to advertise in the top positions immediately. It is very effective because you only pay when someone interested in the service or product.</p>',
                    'what_include' => '
                        <h2>THE PRICE OF THIS SERVICE WILL BE ESTIMATED AFTER DO SEO/SEM AUDIT</h2>
                        <ol>
                            <li>Keyword Research</li>
                            <li>SEO On Page</li>
                            <li>Competitors tracking</li>
                            <li>Linkbuilding</li>
                            <li>Content Strategy</li>
                            <li>Google Ads Plan</li>
                            <li>Facebook Ads Plan</li>
                            <li>Instagram Plan</li>
                            <li>Pinterest Plan</li>
                            <li>Remarketing Plan</li>
                        </ol>'
                ),
            );

            $this->set('extra_services', $services);
        }

        //No index - Google
        $no_index = in_array($page, array(
				'privacy-policy',
				'cookies',
				'data-protection',
				'terms-and-conditions',
				'download-center',
				'download-center-recover',
				'payment-and-refunds',
				'install-iepro',
				'install-gmt',
				'open_ticket',
				'testimonials',
				'cart',
                'video-tutorials',
				'account/my-account',
				'account/login',
				'account/forgot-password',
				'account/register',
            )
        );

        if($is_cart || $no_index) {
		    $this->set('noindex', true);
        }
		//Count sales
                /*
            $count_sales = $this->Sale->query("SELECT COUNT(*) as total_sales FROM intranet_sales WHERE order_status = 'Complete'");
            $this->set('sales', $count_sales[0][0]['total_sales']);
                */


			/*$conditions = array('order_status' => 'Complete');
            $this->Sale->recursive = -1;
			$sales = $this->Sale->find('all', array('conditions' => $conditions));
			$this->set('sales', $sales);*/
		//END Count sales

		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		if(!empty($meta_title))
		    $title_for_layout = $meta_title;

		$this->set(compact('page', 'subpage', 'title_for_layout', 'meta_keywords', 'meta_description'));

		try {
			$this->render(isset($force_render) && !empty($force_render) ?  $force_render : implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
}
