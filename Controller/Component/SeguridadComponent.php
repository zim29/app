<?php

	//Se importa la clase 'Sanitize': usada para eliminar datos maliciosos y otra información no deseada
	App::import('Sanitize');
		
	/**
	 * Componente encargado de verificar la seguridad de los datos 
	 */
	class SeguridadComponent extends Component {		
		/**
		 * Función encargada de limpiar los datos de caracteres peligrosos
		 * 
		 * @param mixed $data Datos a filtrar (Puede ser un string o un array) -> Se pasa por referencia
		 * @param array $fields Campos Array de campos que se filtrarán pero no se eliminarán etiquetas HTML 
		 * @return mixed Datos filtrados
		 */
		function cleanData(&$data, $fields = array()) {
			if (empty($data)) {
				return $data;
			}

			if (is_array($data)) {
				foreach ($data as $key => $val) {
					if(in_array($key, $fields))
						$data[$key] =  $this->_removeXSS($val, "_"); //cuando encuentre un riesgo de xss pondrá el segundo parametro entre medias de la palabra
					else if($key!="_Token") //Comprobamos que no editemos el token
						$data[$key] = $this->cleanData($val, $fields);
				}
				return $data;
			} else {
				$data = strip_tags($data); //Eliminamos cualquier tag!
				return $data;
			}
		}

		
		/**
		 * Función encargada de limpiar una ruta de caracteres peligrosos
		 * 
		 * @param string $path Ruta a filtrar
		 * @return string Ruta filtrada de caratéres peligrosos
		 */
		function cleanPath($path = null){
			$path = preg_replace("/\.\.+\//", "", $path); // Un . seguido de como mínimo un . o más puntos y al final una / (../,.../,..../, etc)
			return $path;
		}

		
		/**
		 * Función encargada de limpiar las comillas de un string
		 * 
		 * @param string $string Cadena a filtrar
		 * @return string Ruta filtrada de comillas
		 */
		function cleanQuotes($string = null){
			$string = str_replace('"','',$string);
			$string = str_replace("'",'',$string);
			return $string;
		}

		
		/**
		 * Función privada encargada de eliminar ataques XSS
		 */
		function _removeXSS($val, $replaceString = '<x>') {
			// don't use empty $replaceString because then no XSS-remove will be done
			if ($replaceString == '') {
				$replaceString = '<x>';
			}
			// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
			// this prevents some character re-spacing such as <java\0script>
			// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
			$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x19])/', '', $val);

			// straight replacements, the user should never need these since they're normal characters
			// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
			$search = '/&#[xX]0{0,8}(21|22|23|24|25|26|27|28|29|2a|2b|2d|2f|30|31|32|33|34|35|36|37|38|39|3a|3b|3d|3f|40|41|42|43|44|45|46|47|48|49|4a|4b|4c|4d|4e|4f|50|51|52|53|54|55|56|57|58|59|5a|5b|5c|5d|5e|5f|60|61|62|63|64|65|66|67|68|69|6a|6b|6c|6d|6e|6f|70|71|72|73|74|75|76|77|78|79|7a|7b|7c|7d|7e);?/ie';
			$val = preg_replace($search, "chr(hexdec('\\1'))", $val);
			$search = '/&#0{0,8}(33|34|35|36|37|38|39|40|41|42|43|45|47|48|49|50|51|52|53|54|55|56|57|58|59|61|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80|81|82|83|84|85|86|87|88|89|90|91|92|93|94|95|96|97|98|99|100|101|102|103|104|105|106|107|108|109|110|111|112|113|114|115|116|117|118|119|120|121|122|123|124|125|126);?/ie';
			$val = preg_replace($search, "chr('\\1')", $val);

			// now the only remaining whitespace attacks are \t, \n, and \r
			$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
			$ra_tag = array('applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
			$ra_attribute = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
			$ra_protocol = array('javascript', 'vbscript', 'expression');

			//remove the potential &#xxx; stuff for testing
			$val2 = preg_replace('/(&#[xX]?0{0,8}(9|10|13|a|b);)*\s*/i', '', $val);
			$ra = array();

			foreach ($ra1 as $ra1word) {
				//stripos is faster than the regular expressions used later
				//and because the words we're looking for only have chars < 0x80
				//we can use the non-multibyte safe version
				if (stripos($val2, $ra1word ) !== false ) {
					//keep list of potential words that were found
					if (in_array($ra1word, $ra_protocol)) {
						$ra[] = array($ra1word, 'ra_protocol');
					}
					if (in_array($ra1word, $ra_tag)) {
						$ra[] = array($ra1word, 'ra_tag');
					}
					if (in_array($ra1word, $ra_attribute)) {
						$ra[] = array($ra1word, 'ra_attribute');
					}
					//some keywords appear in more than one array
					//these get multiple entries in $ra, each with the appropriate type
				}
			}

			//only process potential words
			if (count($ra) > 0) {
				// keep replacing as long as the previous round replaced something
				$found = true;
				while ($found == true) {
					$val_before = $val;
					for ($i = 0; $i < sizeof($ra); $i++) {
						$pattern = '';
						for ($j = 0; $j < strlen($ra[$i][0]); $j++) {
							if ($j > 0) {
								$pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*';
							}
							$pattern .= $ra[$i][0][$j];
						}
						//handle each type a little different (extra conditions to prevent false positives a bit better)
						switch ($ra[$i][1]) {
							case 'ra_protocol':
								//these take the form of e.g. 'javascript:'
								$pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=:)';
								break;
							case 'ra_tag':
								//these take the form of e.g. '<SCRIPT[^\da-z] ....';
								$pattern = '(?<=<)' . $pattern . '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=[^\da-z])';
								break;
							case 'ra_attribute':
								//these take the form of e.g. 'onload='  Beware that a lot of characters are allowed
								//between the attribute and the equal sign!
								$pattern .= '[\s\!\#\$\%\&\(\)\*\~\+\-\_\.\,\:\;\?\@\[\/\|\\\\\]\^\`]*(?==)';
								break;
						}
						$pattern = '/' . $pattern . '/i';
						// add in <x> to nerf the tag
						$replacement = substr_replace($ra[$i][0], $replaceString, 2, 0);
						// filter out the hex tags
						$val = preg_replace($pattern, $replacement, $val);
						if ($val_before == $val) {
							// no replacements were made, so exit the loop
							$found = false;
						}
					}
				}
			}
			return $val;
		}
	}
?>