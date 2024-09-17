<?php
	require_once VENDORS . 'minify/src/Minify.php';
    require_once VENDORS . 'minify/src/CSS.php';
    require_once VENDORS . 'minify/src/JS.php';
    require_once VENDORS . 'minify/src/Exception.php';
    require_once VENDORS . 'minify/src/Exceptions/BasicException.php';
    require_once VENDORS . 'minify/src/Exceptions/FileImportException.php';
    require_once VENDORS . 'minify/src/Exceptions/IOException.php';
    require_once VENDORS . 'minify/path-converter/src/ConverterInterface.php';
    require_once VENDORS . 'minify/path-converter/src/Converter.php';

	class MinifyController extends AppController
	{
	    public function __construct() {

        }

		public function beforeFilter() {
	        $this->Auth->allow('minify');
	    }

	    public function minify() {
            $paths = array(
                WWW_ROOT.'css',
                WWW_ROOT.'js',
                WWW_ROOT.'v2',
            );



            foreach ($paths as $key => $path) {

                $it = new RecursiveTreeIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach($it as $path) {
                    $path = str_replace("|-", "", $path);
                    $info = pathinfo($path);
                    if(!empty($info["extension"]) && in_array($info["extension"], array("css",'js'))) {
                        $minify_name = str_replace(".".$info["extension"], "_minified.".$info["extension"]. $info['basename']);
                        $minify_path = $info["dirname"].'/'.$minify_name;

                        if($info["extension"] == 'js')
                            $minifier = new Minify\JS($path);
                        else if($info["extension"] == 'css')
                            $minifier = new Minify\CSS($path);

                        echo $minifier->minify(); die;

                    }
                }


            }

            die("aaa");

        }
	}
?>