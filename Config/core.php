<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

//setLocale(LC_ALL, 'deu');
//Configure::write('Config.language', 'deu');

/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */
Configure::write('debug', 0);


function currency_convert($from,$to,$amount,$sign=true,$sensitivity=5) {
	//return false;
	$RATES=$BASE=$in=$out=$append=NULL;

	//Array of available countries & currencies
	$CURRENCY=array("US"=>"USD","BE"=>"EUR","ES"=>"EUR","LU"=>"EUR","PT"=>"EUR","DE"=>"EUR","FR"=>"EUR","MT"=>"EUR","SI"=>"EUR","IE"=>"EUR","IT"=>"EUR","NL"=>"EUR","SK"=>"EUR","GR"=>"EUR","CY"=>"EUR","AT"=>"EUR","FI"=>"EUR","JP"=>"JPY","BG"=>"BGN","CZ"=>"CZK","DK"=>"DKK","EE"=>"EEK","GB"=>"GBP","HU"=>"HUF","LT"=>"LTL","LV"=>"LVL","PL"=>"PLN","RO"=>"RON","SE"=>"SEK","CH"=>"CHF","NO"=>"NOK","HR"=>"HRK","RU"=>"RUB","TR"=>"TRY","AU"=>"AUD","BR"=>"BRL","CA"=>"CAD","CN"=>"CNY","HK"=>"HKD","ID"=>"IDR","IN"=>"INR","KR"=>"KRW","MX"=>"MXN","MY"=>"MYR","NZ"=>"NZD","PH"=>"PHP","SG"=>"SGD","TH"=>"THB","ZA"=>"ZAR");

	if (strlen($from)==2 && strlen($to)==2) {//Operate using country code
		if(isset($CURRENCY[$from])) {$in=$CURRENCY[$from];}
		if(isset($CURRENCY[$to])) {$out=$CURRENCY[$to];}
	} elseif (strlen($from)==3 && strlen($to)==3) {//Operate using currency code
		if(in_array($from,$CURRENCY)) {$in=$from;}
		if(in_array($to,$CURRENCY)) {$out=$to;}
	} else {
		return false;
		echo "Error: You should either use 2 digit country codes or 3 digit currency codes!";
	}

	if ($in && $out) {//Both currencies available, continue

		//Load live conversion rates and set as an array
		$XMLContent= file("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
		if(is_array($XMLContent)) {
			foreach ($XMLContent as $line) {
				if (preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)) {
					if (preg_match("/rate='([[:graph:]]+)'/",$line,$rate)) {
						$RATES[$currencyCode[1]]=$rate[1];
					}
				}
			}
		}
		if(is_array($RATES)) {

			$RATES["EUR"]=1; //Add EUR reference to array

			$BASE=$RATES["EUR"];

			if ($in!="EUR" && $BASE > 0) {//Normalize rate to given input
				foreach ($RATES as $code => $rate) {
					$RATES[$code]=round($rate/$BASE,$sensitivity);}
			}

			//Prepend or append the currency information
			return round($amount*$RATES[$out],$sensitivity);//Output the converted amount

		} else {
			return false;
			echo "Error: Unable to load conversion rates!";
		}

	} else {
		return false;
		echo "Error: Either one or both of given currencies are not available for conversion!";
	}
}

$euro_value = currency_convert("USD", "EUR" ,1, false, 9);

$euro_value = !$euro_value ? 1.02 : $euro_value;
Configure::write('eur_currency_value', $euro_value);

$rub_value = currency_convert("RUB", "USD" ,1, false, 9);
$rub_value = !$rub_value ? 0.012 : $rub_value;
Configure::write('rub_currency_value', $rub_value);

Configure::write('minify_css_jss', true);

Configure::write('dollar_currency_value', 0);
Configure::write('paypal_fee', (4.7 / 100) + 1);

Configure::write('stripe_api_public', 'pk_live_k2LpTmdyYRBzDEuZZYDv6wlm');
Configure::write('stripe_api_private', 'sk_live_c2lqQNJnTnO1rcCz55ZiRXoE');

Configure::write('api_licenses_url', 'https://api.devmanextensions.com/bridge/');
Configure::write('api_licenses_key', 'e597236e254177fb391269d4a7e5f90b');

/*Configure::write('stripe_api_public', 'pk_test_cLSN2PmphiuTCQdTcgjSaxdG');
Configure::write('stripe_api_private', 'sk_test_cNw5ITTRdnqY9FjAZihEHe4N');*/

Configure::write('klaviyo_private_api_key', 'pk_edee9a3b896515fb810f70967772c4203c');
Configure::write('klaviyo_public_api_key', 'WkiHTE');


Configure::write('stripe_callback', 'invoices/stripe/callback');
//Configure::write('stripe_fee', 1);
Configure::write('stripe_fee', (3.3 / 100) + 1);

Configure::write('discount_extensions_shop', 10);
Configure::write('discount_extensions_shop_calculate', (100-Configure::read('discount_extensions_shop'))/100);

Configure::write('mailchimp_api', '21a4332d595d464b63209c866073b71e-us17');
Configure::write('mailchimp_server', 'us17');



Configure::write('discount_pack',
	array(
		1 => 0,
		2 => 20,
		3 => 23,
		4 => 25,
		5 => 28,
		6 => 30,
		7 => 32,
		8 => 35,
		9 => 37,
		10 => 40
	)
);

Configure::write('holidays_from', '2019-10-07');
Configure::write('holidays_to', '2019-10-10');


Configure::write('black_friday',
	array(
		'status' => 1,
		'from' => '2022-11-24 00:00:00',
		'to' => '2022-11-25 23:59:59',
		'discount' => 30,
		'coupon' => 'BLACK2022'
	)
);

/**
 * Configure the Error handler used to handle errors for your application. By default
 * ErrorHandler::handleError() is used. It will display errors using Debugger, when debug > 0
 * and log errors with CakeLog when debug = 0.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle errors. You can set this to any callable type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `level` - integer - The level of errors you are interested in capturing.
 * - `trace` - boolean - Include stack traces for errors in log files.
 *
 * @see ErrorHandler for more information on error handling and configuration.
 */
Configure::write('Error', array(
	'handler' => 'ErrorHandler::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
));

/**
 * Configure the Exception handler used for uncaught exceptions. By default,
 * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
 * while debug > 0, framework errors like Missing Controller will be displayed. When debug = 0,
 * framework errors will be coerced into generic HTTP errors.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `renderer` - string - The class responsible for rendering uncaught exceptions. If you choose a custom class you
 *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
 * - `log` - boolean - Should Exceptions be logged?
 * - `extraFatalErrorMemory` - integer - Increases memory limit at shutdown so fatal errors are logged. Specify
 *   amount in megabytes or use 0 to disable (default: 4 MB)
 * - `skipLog` - array - list of exceptions to skip for logging. Exceptions that
 *   extend one of the listed exceptions will also be skipped for logging.
 *   Example: `'skipLog' => array('NotFoundException', 'UnauthorizedException')`
 *
 * @see ErrorHandler for more information on exception handling and configuration.
 */
Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'ExceptionRenderer',
	'log' => true
));

/**
 * Application wide charset encoding
 */
Configure::write('App.encoding', 'UTF-8');

/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below. But keep in mind
 * that plugin assets such as images, CSS and JavaScript files
 * will not work without URL rewriting!
 * To work around this issue you should either symlink or copy
 * the plugin assets into you app's webroot directory. This is
 * recommended even when you are using mod_rewrite. Handling static
 * assets through the Dispatcher is incredibly inefficient and
 * included primarily as a development convenience - and
 * thus not recommended for production applications.
 */
//Configure::write('App.baseUrl', env('SCRIPT_NAME'));

/**
 * To configure CakePHP to use a particular domain URL
 * for any URL generation inside the application, set the following
 * configuration variable to the http(s) address to your domain. This
 * will override the automatic detection of full base URL and can be
 * useful when generating links from the CLI (e.g. sending emails).
 * If the application runs in a subfolder, you should also set App.base.
 */
//Configure::write('App.fullBaseUrl', 'http://example.com');

/**
 * The base directory the app resides in. Should be used if the
 * application runs in a subfolder and App.fullBaseUrl is set.
 */
//Configure::write('App.base', '/my_app');

/**
 * Web path to the public images directory under webroot.
 * If not set defaults to 'img/'
 */
Configure::write('App.imageBaseUrl', 'webroot/img/');

/**
 * Web path to the CSS files directory under webroot.
 * If not set defaults to 'css/'
 */
Configure::write('App.cssBaseUrl', 'webroot/css/');

/**
 * Web path to the js files directory under webroot.
 * If not set defaults to 'js/'
 */
Configure::write('App.jsBaseUrl', 'webroot/js/');

/**
 * Uncomment the define below to use CakePHP prefix routes.
 *
 * The value of the define determines the names of the routes
 * and their associated controller actions:
 *
 * Set to an array of prefixes you want to use in your application. Use for
 * admin or other prefixed routes.
 *
 * 	Routing.prefixes = array('admin', 'manager');
 *
 * Enables:
 *	`admin_index()` and `/admin/controller/index`
 *	`manager_index()` and `/manager/controller/index`
 */
//Configure::write('Routing.prefixes', array('admin'));

/**
 * Turn off all caching application-wide.
 */
Configure::write('Cache.disable', true);

/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * public $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting public $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 */
//Configure::write('Cache.check', true);

/**
 * Enable cache view prefixes.
 *
 * If set it will be prepended to the cache name for view file caching. This is
 * helpful if you deploy the same application via multiple subdomains and languages,
 * for instance. Each version can then have its own view cache namespace.
 * Note: The final cache file name will then be `prefix_cachefilename`.
 */
//Configure::write('Cache.viewPrefix', 'prefix');

/**
 * Session configuration.
 *
 * Contains an array of settings to use for session configuration. The defaults key is
 * used to define a default preset to use for sessions, any settings declared here will override
 * the settings of the default config.
 *
 * ## Options
 *
 * - `Session.cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `Session.timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `Session.useForwardsCompatibleTimeout` - Whether or not to make timeout 3.x compatible.
 * - `Session.cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `Session.checkAgent` - Do you want the user agent to be checked when starting sessions? You might want to set the
 *    value to false, when dealing with older versions of IE, Chrome Frame or certain web-browsing devices and AJAX
 * - `Session.defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `Session.handler` - Can be used to enable a custom session handler. Expects an array of callables,
 *    that can be used with `session_save_handler`. Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `Session.autoRegenerate` - Enabling this setting, turns on automatic renewal of sessions, and
 *    sessionids that change frequently. See CakeSession::$requestCountdown.
 * - `Session.cacheLimiter` - Configure the cache control headers used for the session cookie.
 *   See http://php.net/session_cache_limiter for accepted values.
 * - `Session.ini` - An associative array of additional ini values to set.
 *
 * The built in defaults are:
 *
 * - 'php' - Uses settings defined in your php.ini.
 * - 'cake' - Saves session files in CakePHP's /tmp directory.
 * - 'database' - Uses CakePHP's database sessions.
 * - 'cache' - Use the Cache class to save sessions.
 *
 * To define a custom session handler, save it at /app/Model/Datasource/Session/<name>.php.
 * Make sure the class implements `CakeSessionHandlerInterface` and set Session.handler to <name>
 *
 * To use database sessions, run the app/Config/Schema/sessions.php schema using
 * the cake shell command: cake schema create Sessions
 */
Configure::write('Session', array(
	'defaults' => 'php'
));

/**
 * A random string used in security hashing methods.
 */
Configure::write('Security.salt', 'asdfasdfADW452Q4RQWEFWQRWFA');

/**
 * A random numeric string (digits only) used to encrypt/decrypt strings.
 */
Configure::write('Security.cipherSeed', '234523523525235345235');

/**
 * Apply timestamps with the last modified time to static assets (js, css, images).
 * Will append a query string parameter containing the time the file was modified. This is
 * useful for invalidating browser caches.
 *
 * Set to `true` to apply timestamps when debug > 0. Set to 'force' to always enable
 * timestamping regardless of debug value.
 */
//Configure::write('Asset.timestamp', true);

/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
//Configure::write('Asset.filter.css', 'css.php');

/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JsHelper::link().
 */
//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');

/**
 * The class name and database used in CakePHP's
 * access control lists.
 */
Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');

/**
 * Uncomment this line and correct your server timezone to fix
 * any date & time related errors.
 */
//date_default_timezone_set('UTC');

/**
 * `Config.timezone` is available in which you can set users' timezone string.
 * If a method of CakeTime class is called with $timezone parameter as null and `Config.timezone` is set,
 * then the value of `Config.timezone` will be used. This feature allows you to set users' timezone just
 * once instead of passing it each time in function calls.
 */
//Configure::write('Config.timezone', 'Europe/Paris');

/**
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'File', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 * 		'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 * 		'lock' => false, //[optional]  use file locking
 * 		'serialize' => true, //[optional]
 * 		'mask' => 0664, //[optional]
 *	));
 *
 * APC (http://pecl.php.net/package/APC)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Apc', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 *
 * Xcache (http://xcache.lighttpd.net/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Xcache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *		'user' => 'user', //user from xcache.admin.user settings
 *		'password' => 'password', //plaintext password (xcache.admin.pass)
 *	));
 *
 * Memcached (http://www.danga.com/memcached/)
 *
 * Uses the memcached extension. See http://php.net/memcached
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Memcached', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 * 		'servers' => array(
 * 			'127.0.0.1:11211' // localhost, default port 11211
 * 		), //[optional]
 * 		'persistent' => 'my_connection', // [optional] The name of the persistent connection.
 * 		'compress' => false, // [optional] compress data in Memcached (slower, but uses less memory)
 *	));
 *
 *  Wincache (http://php.net/wincache)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Wincache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 */

/**
 * Configure the cache handlers that CakePHP will use for internal
 * metadata like class maps, and model schema.
 *
 * By default File is used, but for improved performance you should use APC.
 *
 * Note: 'default' and other application caches should be configured in app/Config/bootstrap.php.
 *       Please check the comments in bootstrap.php for more info on the cache engines available
 *       and their settings.
 */
$engine = 'File';

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	$duration = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = 'myapp_';

/**
 * Configure the cache used for general framework caching. Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

/**
 * Configure the cache for model and datasource caches. This cache configuration
 * is used to store schema descriptions, and table listings in connections.
 */
Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));


Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));