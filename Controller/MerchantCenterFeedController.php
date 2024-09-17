<?php
	class MerchantCenterFeedController extends AppController {

		public  $uses = array(
            'Extensions.Extension',
		);

		public $components = array('ExtensionTool');

		public function beforeFilter() {
			//Generate file
			$this->path_tmp = WWW_ROOT;
			$this->filename = 'merchant_center.xml';
			$this->filename_path = $this->path_tmp.$this->filename;

			/*$this->xw = xmlwriter_open_memory();
			xmlwriter_set_indent($this->xw, 1);
			$res = xmlwriter_set_indent_string($this->xw, ' ');
			xmlwriter_start_document($this->xw, '1.0', 'UTF-8');*/

	        $this->Auth->allow('feed');
	    }

        /**
         * @throws Exception
         */
        function feed() {
			//$extensions = $this->Extension->find("all");
			$extensions = $this->ExtensionTool->get_in_shop_extensions();
			//Format extensions to be compatible with array
			$elements = array();
			foreach ($extensions as $ext) {
				$dataLayerData = $this->Extension->formatExtensionToDatalayer(array("Extension" => $ext));

				$elements[] = array(
					'product_identificator' => $dataLayerData['id'],
					'name' => $dataLayerData['name'],
					'price_formatted' => $dataLayerData['price'],
					'description' => $ext['description'],
					'image_link' => $dataLayerData['image'],
					'product_link' => $dataLayerData['url'],
					'currency_code' => 'USD',
					'quantity' => 1000,
					'manufacturer' => 'DevmanExtensions'
				);
			}

			$tags = array(
				'g:id' => 'product_identificator',
				'title' => 'name',
				'link' => 'product_link',
				'g:brand' => 'manufacturer',
				'g:image_link' => 'image_link',
				//'g:mpn' => 'mpn',
				'g:quantity' => 'quantity',
			);

			//<editor-fold desc="Exclusive code to this feed type">
			$special_tags = array(
				'g:weight',
				'description',
				'g:availability_date',
				'g:product_type',
				'g:google_product_category',
				'g:identifier_exists',
				'g:price',
				'g:sale_price',
				'g:sale_price_effective_date',
				'g:availability',
				'g:additional_image_link',
				'g:gtin',
				'g:multipack',
				'g:color',
				'g:size',
				'g:item_group_id',
				'g:material',
				'g:age_group',
				'g:gender',
				'g:custom_label',
				'g:condition',
				//'g:adult',
				//'g:pattern',
				'g:store_code',
				'g:pickup_method',
				'g:pickup_sla',
			);
			//</editor-fold>

			$feed_elmements = array();

			foreach ($elements as $key => $product) {
				$temp = array();

				foreach ($tags as $tag_name => $index) {
					if($product[$index] !== '')
						$temp[$tag_name] = $this->format_xml_value($product[$index]);
				}

				//<editor-fold desc="Exclusive code to this feed type">
				foreach ($special_tags as $tag_name) {
					/*if($tag_name == 'g:weight' && $product['weight'] > 0) {
						$temp[$tag_name] = $this->weight->format($product['weight'], $product['weight_class_id']);
						continue;
					}*/

					/*if($tag_name == 'g:availability_date' && $product['quantity'] <= 0) {
						$temp[$tag_name] = date("c", strtotime($product['date_available']." 00:00:00"));
						continue;
					}*/

					if($tag_name == 'description') {
						$temp[$tag_name] = $this->format_xml_description($product['description'], 5000);
						continue;
					}

					/*if($tag_name == 'g:product_type' && !empty($product['last_category_tree'])) {
						$temp[$tag_name] = $this->format_xml_value($product['last_category_tree']);
						continue;
					}*/

					if($tag_name == 'g:condition') {
						$temp[$tag_name] = 'new';
						continue;
					}

					/*if($tag_name == 'g:google_product_category' && !empty($product['last_category_id'])) {
						$cat_id = $this->extract_google_category_id($this->config->get('google_all_feed_taxonomy_cat_'.$product['last_category_id']));
						if(is_numeric($cat_id))
							$temp[$tag_name] = $cat_id;
						continue;
					}*/

					if($tag_name == 'g:identifier_exists') {
						$rule = empty($product['gtin']) && empty($product['mpn']);
						if($rule)
							$temp[$tag_name] = 'no';
						continue;
					}

					/*if($tag_name == 'g:additional_image_link' && !empty($product['additional_images_link'])) {
						$temp[$tag_name] = array_slice($product['additional_images_link'], 0, 10);
					}*/

					if($tag_name == 'g:price') {
						$temp[$tag_name] = $product['price_formatted'].' '.$product['currency_code'];
						continue;
					}

					/*if($tag_name == 'g:sale_price' && $product['special'] > 0) {
						$temp[$tag_name] = $product['special_formatted'].' '.$product['currency_code'];
						continue;
					}*/

					/*if ($tag_name == 'g:sale_price_effective_date' && isset($product['special']) && $product['special'] > 0) {

						if($product['special_start'] == '0000-00-00 00:00:00' || empty($product['special_start']) || $product['special_start'] == '0000-00-00')
							$product['special_start'] = date('Y-m-d 00:00:00');

						$this->format_date_ISO_8601($product['special_start']);

						if($product['special_end'] == '0000-00-00 00:00:00' || empty($product['special_end']) || $product['special_end'] == '0000-00-00') {
							$time = strtotime(date("Y-m-d"));
							$product['special_end'] = date("Y-m-d 23:59:59", strtotime("+1 month", $time));
						}

						$this->format_date_ISO_8601($product['special_end']);

						$temp[$tag_name] = $product['special_start'].'/'.$product['special_end'];
					}*/


					if($tag_name == 'g:availability') {
						$temp[$tag_name] = $product['quantity'] > 0 ? 'in stock' : 'out of stock';
						continue;
					}

					if($tag_name == 'g:gtin' && !empty($product['gtin'])) {
						$temp[$tag_name] = $product['gtin'];
						continue;
					}

				}
				//</editor-fold>

				$feed_elmements[] = $temp;
			}

			$header  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
			$header .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
			$header .= '<channel>'."\n";
			$header .= '<title><![CDATA[DevmanExtensions - Opencart extensions]]></title>'."\n";
			$header .= '<description><![CDATA[The best Opencart extensions]]></description>'."\n";
			$header .= '<link><![CDATA[https://devmanextensions.com]]></link>'."\n";

			$footer = '</channel>'."\n";
			$footer .= '</rss>';

			$data = array(
				'header' => $header,
				'elements' => $feed_elmements,
				'footer' => $footer,
				'node' => 'item'
			);


			$this->insert_data($data);
			die("Feed generated");
		}

		public function format_xml_value($value) {
			if(!is_numeric($value))
				$value = html_entity_decode($value);

			return is_numeric($value) ? $value : '<![CDATA['.$value.']]>';
		}

		public function format_xml_description($description, $limit, $xml = true) {
			$description = trim(strip_tags(htmlspecialchars_decode($description), '<br></ br>'));
			$description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);
			$description = str_replace('&nbsp;', ' ', $description);
			$description = str_replace('<br />', '<br>', $description);
			$description = strlen($description) > $limit ? mb_substr($description, 0, ($limit-3)).'...' : $description;
			//Avoid chinnese error: Bytes: 0x08 0xEF 0xBC 0x8C
			$description = preg_replace('/[\x00-\x1f]/','',$description);
			return $xml ? $this->format_xml_value($description) : $description;
		}

		function insert_data($elements) {
			$xml_content = $elements['header']."\n";
			foreach ($elements['elements'] as $elements_inside) {
				$xml_content .= '<'.$elements['node'].'>'."\n";
				foreach ($elements_inside as $tag_name => $element) {
					if (!is_array($element))
						$xml_content .= $this->get_tag($tag_name, $element)."\n";
					else {
						if($this->array_depth($element) == 1) {
							foreach ($element as $el) {
								$xml_content .= $this->get_tag($tag_name, $this->format_xml_value($el)) . "\n";
							}
						} else {
							if(array_key_exists('attributes', $element)) {
								$xml_content .= '<' . $tag_name;
								foreach ($element['attributes'] as $attri_name => $attri_value) {
									$xml_content .= ' '.$attri_name.'="'.$attri_value.'" ';
								}
								$xml_content = trim($xml_content);
								$xml_content .= '>';

								$xml_content .= $this->format_xml_value($element['value']);
								$xml_content .= '</' . $tag_name.'>' . "\n";

							} else {
								$exist_tag_children = array_key_exists('tag_children', $element);
								$values_in_tag = array_key_exists('values_in_tag', $element) && $element['values_in_tag'];

								if (!$values_in_tag)
									$xml_content .= $exist_tag_children ? '<' . $tag_name . '>' . "\n" : '';
								else
									$xml_content .= '<' . $tag_name;

								$tag_children = $exist_tag_children ? $element['tag_children'] : (!$values_in_tag ? $tag_name : '');
								foreach ($element['values'] as $tag_name2 => $val) {
									$xml_content .= $this->get_tag($tag_children, $this->format_xml_value($val), $tag_name2) . (!$values_in_tag ? "\n" : '');
									if (empty($values_in_tag))
										$xml_content .= ' ';
								}
								if (empty($values_in_tag))
									$xml_content = rtrim($xml_content);

								if (!$values_in_tag)
									$xml_content .= $exist_tag_children ? '</' . $tag_name . '>' . "\n" : '';
								else
									$xml_content .= ' />' . "\n";
							}
						}
					}
				}
				$xml_content .= '</'.$elements['node'].'>'."\n";
			}
			$xml_content .= $elements['footer'];

			file_put_contents($this->filename_path, $xml_content);
		}

		function get_tag($tag_name, $value, $tag_attributes = '') {
			$attributes = '';

			if(!empty($tag_attributes)) {
				if (strpos($tag_attributes, '@') !== false) {
					$attributes = explode('@', $tag_attributes);
					$attributes = ' '.$attributes[0].'="'.$attributes[1].'" ';
				}
			}

			return !empty($tag_name) ? '<'.$tag_name.$attributes.'>'.$value.'</'.$tag_name.'>' : rtrim($attributes);
		}

		function array_depth(array $array) {
			$max_depth = 1;
			foreach ($array as $value) {
				if (is_array($value)) {
					$depth = $this->array_depth($value) + 1;

					if ($depth > $max_depth) {
						$max_depth = $depth;
					}
				}
			}
			return $max_depth;
		}
	}
?>
