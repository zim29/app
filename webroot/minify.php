<?php
    $vendor_path = str_replace("app/webroot/minify.php", "vendors/minify", __FILE__);
    require_once $vendor_path . '/src/Minify.php';
    require_once $vendor_path . '/src/CSS.php';
    require_once $vendor_path . '/src/JS.php';
    require_once $vendor_path . '/src/Exception.php';
    require_once $vendor_path . '/src/Exceptions/BasicException.php';
    require_once $vendor_path . '/src/Exceptions/FileImportException.php';
    require_once $vendor_path . '/src/Exceptions/IOException.php';
    require_once $vendor_path . '/path-converter/src/ConverterInterface.php';
    require_once $vendor_path . '/path-converter/src/Converter.php';

    use MatthiasMullie\Minify;

    $www_path = str_replace("minify.php", "", __FILE__);

    $www_path2 = str_replace("webroot/minify.php", "", __FILE__);

    $paths = array(
        $www_path.'css/',
        $www_path.'js/',
        $www_path.'v2/',
        $www_path.'libraries/',
        $www_path2.'Plugin/'
    );
    
    foreach ($paths as $key => $path) {

        $it = new RecursiveTreeIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach($it as $path) {
            $path = utf8_decode($path);
            $path = str_replace("|-", "", $path);
            $path = str_replace("| | ", "", $path);
            $path = str_replace("| ", "", $path);
            $path = str_replace("\-", "", $path);
            $path = str_replace("/ /", "/", $path);
            $path = trim($path);

            $info = pathinfo($path);
            if(!empty($info["extension"]) && in_array($info["extension"], array("css",'js'))) {

                if (strpos($info['basename'], '_minified') !== false) {
                    //unlink($path);
                } else {
                    $minify_name = str_replace(".".$info["extension"], "_minified.".$info["extension"], $info['basename']);
                    $minify_path = $info["dirname"].'/'.$minify_name;

                    if($info["extension"] == 'js')
                        $minifier = new Minify\JS($path);
                    else if($info["extension"] == 'css')
                        $minifier = new Minify\CSS($path);
                    file_put_contents($minify_path, $minifier->minify());
                }
            }
        }
    }

    die("aaa");