<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

	Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
	Router::connect('/open_ticket', array('controller' => 'pages', 'action' => 'display', 'open_ticket'));
	Router::connect('/terms-and-conditions', array('controller' => 'pages', 'action' => 'display', 'terms-and-conditions'));
	Router::connect('/privacy-policy', array('controller' => 'pages', 'action' => 'display', 'privacy-policy'));
	Router::connect('/data-protection', array('controller' => 'pages', 'action' => 'display', 'data-protection'));
	Router::connect('/payment-and-refunds', array('controller' => 'pages', 'action' => 'display', 'payment-and-refunds'));
	Router::connect('/testimonial-insert', array('controller' => 'pages', 'action' => 'display', 'testimonial-insert'));
	Router::connect('/our-star-products', array('controller' => 'pages', 'action' => 'display', 'our-star-products'));
	Router::connect('/testimonials', array('controller' => 'pages', 'action' => 'display', 'testimonials'));
	Router::connect('/opencart-prestashop-woocommerce-custom-module-development', array('controller' => 'pages', 'action' => 'display', 'opencart-prestashop-woocommerce-custom-module-development'));
	Router::connect('/opencart-prestashop-woocommerce-customized-quick-develops', array('controller' => 'pages', 'action' => 'display', 'opencart-prestashop-woocommerce-customized-quick-develops'));
	Router::connect('/full-projects-development', array('controller' => 'pages', 'action' => 'display', 'full-projects-development'));
	Router::connect('/sitemap', array('controller' => 'pages', 'action' => 'display', 'sitemap'));
	Router::connect('/black-friday', array('controller' => 'pages', 'action' => 'display', 'black-friday'));
	Router::connect('/end-year-offers', array('controller' => 'pages', 'action' => 'display', 'end-year-offers'));
Router::connect('/marketing', array('controller' => 'pages', 'action' => 'display', 'marketing'));

	Router::connect('/testimonials', array('controller' => 'pages', 'action' => 'display', 'testimonials'));
	Router::connect('/shop/discount', array('controller' => 'pages', 'action' => 'display', 'discount'));
	Router::connect('/cart', array('controller' => 'pages', 'action' => 'display', 'cart'));
	Router::connect('/kit-digital', array('controller' => 'pages', 'action' => 'display', 'kit-digital'));
    Router::connect('/services', array('controller' => 'pages', 'action' => 'display', 'services'));
    Router::connect('/cookies', array('controller' => 'pages', 'action' => 'display', 'cookies'));
    Router::connect('/extensions-shop', array('controller' => 'pages', 'action' => 'display', 'extensions-shop'));

	Router::connect('/video-tutorials', array('controller' => 'pages', 'action' => 'display', 'video-tutorials'));
	Router::connect('/google-marketing-tools-services', array('controller' => 'pages', 'action' => 'display', 'google-marketing-tools-services'));

	//Router::connect('/account/my-account', array('controller' => 'pages', 'action' => 'display', 'account/my-account'));
	//Router::connect('/login', array('controller' => 'pages', 'action' => 'display', 'login'));
	//Router::connect('/forgot-password', array('controller' => 'pages', 'action' => 'display', 'forgot-password'));
	//Router::connect('/account/register', array('controller' => 'pages', 'action' => 'display', 'account/register'));

	Router::connect('/account/my-account', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'index'));
	Router::connect('/account/register', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'register'));
	Router::connect('/account/login', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'login'));
	Router::connect('/account/logout', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'logout'));
	Router::connect('/account/licenses', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'licenses'));
	Router::connect('/account/create_accounts', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'create_accounts'));
	Router::connect('/account/password_recovery_assign', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'password_recovery_assign'));
	Router::connect('/account/password-recovery', array('plugin' => 'accounts', 'controller' => 'accounts', 'action' => 'password_recovery'));



	Router::connect('/marketing-tools-for-woocommerce-trial-vs-pro', array('controller' => 'pages', 'action' => 'display', 'marketing-tools-for-woocommerce-trial-vs-pro'));

	App::uses('ClassRegistry', 'Utility');
	$extension_model = ClassRegistry::init('Extensions.Extension');
	$extension_model->recursive = -1;
    $extensions = $extension_model->find('all', array('fields' => array('Extension.seo_url')));
    foreach ($extensions as $key => $ext) {
        Router::connect('/'.$ext['Extension']['seo_url'], array('controller' => 'pages', 'action' => 'display', $ext['Extension']['seo_url']));
    }

	Router::connect('/install-gmt', array('controller' => 'pages', 'action' => 'display', 'install-gmt'));
	Router::connect('/install-iepro', array('controller' => 'pages', 'action' => 'display', 'install-iepro'));
	Router::connect('/install-iepro-cron-jobs', array('controller' => 'pages', 'action' => 'display', 'install-iepro-cron-jobs'));
	Router::connect('/install-iepro-custom-fields', array('controller' => 'pages', 'action' => 'display', 'install-iepro-custom-fields'));
	Router::connect('/install-swish', array('controller' => 'pages', 'action' => 'display', 'install-swish'));
	Router::connect('/install-qnec', array('controller' => 'pages', 'action' => 'display', 'install-qnec'));
	Router::connect('/install-otp', array('controller' => 'pages', 'action' => 'display', 'install-otp'));
	Router::connect('/install-oen', array('controller' => 'pages', 'action' => 'display', 'install-oen'));
	Router::connect('/install-options-packs', array('controller' => 'pages', 'action' => 'display', 'install-options-packs'));

	Router::connect('/download-center', array('controller' => 'pages', 'action' => 'display', 'download-center'));
	Router::connect('/download-center-recover', array('controller' => 'pages', 'action' => 'display', 'download-center-recover'));

	Router::connect('/invoices/cs-cart/new_invoice', array('plugin' => 'invoices', 'controller' => 'cscart', 'action' => 'new_invoice'));


	Router::connect('/products', array('controller' => 'pages', 'action' => 'display', 'products'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
