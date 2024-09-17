<?php
/*
	V 1.2.7

	CHANGELOG:

	2016-04-09: Fix to flag image to OC 2.2.X
	2016-05-08: Added fields to generate table function OC.2.X. (before only allow text)
	2016-05-17: Fix to index 'value' not defined in date input. Add class "products_autocomplete" to input text of input type "products_autocomplete"
	2016-05-18: Fix to loose fields in table inputs. Add multilanguage in table input
	2016-05-20: Added fields to generate table function OC.1.5.X. (before only allow text)
	2016-06-01: Fix to insert loose values in inputs table.
	2016-06-06: Unify all code.
	2016-06-07: Fixed miror bugs in OC2
	2016-06-08: Add button upload_image (only to OC.1.5.X)
	2016-06-11: Add select multiple and bootstrap select
	2016-06-12: Some bug fixed in form OC15X
	2016-06-16: Added $this->extension_group_config to generate field function - Bug fixed to 1.5.x versions in no table inputs
	2016-07-23: Add input type password
	2016-08-25: Fix problem pass array values configuration to input tables, conflict only in OC 2.0.0.0 version
	2016-09-10: Fix multi store document on ready, put 0 instead of value select store beacuse was "undefined"
	2016-09-28: Fix loose value boolean in table inputs
	2016-10-03: Fix error javascript document ready hide all stores less 0 in multistore form
	2016-10-20: Added "style_container" params
	2016-10-21: Added "force_function" params
	2016-11-20: Added function "events_after_add_new_row_table_inputs" jquery to button add new row
	2016-12-05: Added function "getCustomerGroups"
	2016-12-07: Added function "getTaxClassesSelect"
	2016-12-08: Added function "getCategoriesSelect"
	2016-12-09: Added function "getGroupAttributesSelect", "getOptionsSelect", "getGroupFiltersSelect", "getCountriesSelect"
	2016-12-10: Allow replace "language_id" in input name
	2016-12-11: Added new function to get select data and add language
	2016-12-12: Added clone button
	2016-12-29: Added extra class to textarea
	2017-01-21: Added "disabled" attribute to checkbox
	2017-01-22: Added "class" attribute to checkbox
	2017-01-29: Compatibility with multilanguage input and module > oc 2.0.0.0
	2017-01-31: Fix problem multistore, the store marked in select is saved and by default show store fields with id 0
	2017-02-13: Added "default" value to fields
	2017-02-22: Remove button calendar to date input to OC 1.5 and fix languages id
	2017-02-28: Add table input dragable sort improve function field_generate_table
	2017-04-01: Added onchange event to boolean input
	2017-04-07: Added strip_tags to placeholder
	2017-04-29: Added array input name compatibility | Added "inline inputs" | Added columns to inputs
	2017-05-09: Added url to store default function getStores()
	2017-06-03: Incompatibilities with OC 1.5.X
	2017-07-15: Added readonly and disabled to input text
	2017-12-07: html_code type fix col-md-10 when have label.
	2017-12-13: Added class to legend
	2018-01-03: Added radio inputs, added $type_class
	2018-07-25: Fix multilanguage fields, only check if is isset and not if is not empty
	2019-01-11: Added "help_bottom" to field
	2019-09-26: Bug fixed with select value with symbols like ">", "<"....
	2023-01-17: Added Opencart 4 compatibility
	2023-06-17: Add Opencart 4.0.2.X compatibility
	2023-12-26: Add support to tfoot in simple table
	2024-03-18: Add selected_store_id property and impove change_store js function
	2024-05-16: Add support to set $is_multistore in false if the html_type input has the attr multi_store in false
*/
class OpencartFormGeneratorComponent extends Component {

	public $components = array('OpencartExtension');

	public function initialize(Controller $controller) {
		$this->Changelog = ClassRegistry::init('Changelogs.Changelog');
		$this->Extension = ClassRegistry::init('Extensions.Extension');
		$this->TrialLicense = ClassRegistry::init('TrialLicense');
		$this->Faq = ClassRegistry::init('Faq');
		$this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';

	}

	//Devman Extensions - info@devmanextensions.com - 2017-08-29 19:29:27 - Functions generate form
	public function generateForm($form_view, $basic_datas, $is_trial = false)
	{
		$this->is_trial = $is_trial;
		//Devman Extensions - info@devmanextensions.com - 2017-08-30 18:25:54 - Define global variables
		$this->oc_4 = array_key_exists('oc_4', $basic_datas) ? $basic_datas['oc_4'] : '';
		$this->oc_3 = array_key_exists('oc_3', $basic_datas) ? $basic_datas['oc_3'] : '';
		$this->oc_2 = array_key_exists('oc_2', $basic_datas) ? $basic_datas['oc_2'] : '';

		$this->stores = array_key_exists('stores', $basic_datas) ? $basic_datas['stores'] : '';
		$this->extension_group_config = array_key_exists('extension_group_config', $basic_datas) ? $basic_datas['extension_group_config'] : '';
		$this->extension_name = array_key_exists('extension_name', $basic_datas) ? $basic_datas['extension_name'] : $basic_datas['extension_group_config'];
		$this->first_configuration = array_key_exists('first_configuration', $basic_datas) ? $basic_datas['first_configuration'] : '';
		$this->token = array_key_exists('token', $basic_datas) ? $basic_datas['token'] : '';
		$this->langs = array_key_exists('languages', $basic_datas) ? $basic_datas['languages'] : '';
		$this->version = array_key_exists('version', $basic_datas) ? $basic_datas['version'] : '';
		$this->ckeditor_lang = $this->oc_4 && array_key_exists('ckeditor_lang', $basic_datas) ? $basic_datas['ckeditor_lang'] : 'en';
		$this->selected_store_id = array_key_exists('selected_store_id', $basic_datas) ? $basic_datas['selected_store_id'] : '';

		$ie_pro = array_key_exists('extension_id', $basic_datas) && $basic_datas['extension_id'] == '542068d4-ed24-47e4-8165-0994fa641b0a';
		$ie_pro_new_version = array_key_exists('extension_version', $basic_datas) && !empty($basic_datas['extension_version']) && version_compare($basic_datas['extension_version'], '8.0.0', '>=');

		if( !$this->oc_4 && !$this->oc_3 && !$this->oc_2 && $ie_pro && $ie_pro_new_version) {
			$this->oc_2 = true;
			$this->version = 2;
		}

		$this->license_id = array_key_exists('license_id', $basic_datas) ? $basic_datas['license_id'] : '';
		$this->domain = array_key_exists('domain', $basic_datas) ? $basic_datas['domain'] : '';
		$this->no_image_thumb = array_key_exists('no_image_thumb', $basic_datas) ? $basic_datas['no_image_thumb'] : '';

		$this->is_demo = array_key_exists('ts6EpBx2', $basic_datas);

		$this->jquery_symbol = array_key_exists('jquery_compatibility', $basic_datas) ? 'jQuery' : '$';
		$this->retina_icons = array_key_exists('retina_icons', $basic_datas) ? $basic_datas['retina_icons'] : false;
		//Language
		$this->lang_choose_store = array_key_exists('choose_store', $basic_datas['lang']) ? $basic_datas['lang']['choose_store'] : '';
		$this->lang_text_browse = array_key_exists('text_browse', $basic_datas['lang']) ? $basic_datas['lang']['text_browse'] : '';
		$this->lang_text_clear = array_key_exists('text_clear', $basic_datas['lang']) ? $basic_datas['lang']['text_clear'] : '';
		$this->lang_text_sort_order = array_key_exists('text_sort_order', $basic_datas['lang']) ? $basic_datas['lang']['text_sort_order'] : '';
		$this->lang_text_clone_row = array_key_exists('text_clone_row', $basic_datas['lang']) ? $basic_datas['lang']['text_clone_row'] : '';
		$this->lang_text_remove = array_key_exists('text_remove', $basic_datas['lang']) ? $basic_datas['lang']['text_remove'] : '';
		$this->lang_text_add_module = array_key_exists('text_add_module', $basic_datas['lang']) ? $basic_datas['lang']['text_add_module'] : '';
		$this->lang_text_tab_help = array_key_exists('tab_help', $basic_datas['lang']) ? $basic_datas['lang']['tab_help'] : '';
		$this->lang_text_tab_changelog = (array_key_exists('tab_changelog', $basic_datas['lang']) && !empty($basic_datas['lang']['tab_changelog']) ? $basic_datas['lang']['tab_changelog'].' - downloads' : '');
		$this->lang_text_tab_faq = array_key_exists('tab_faq', $basic_datas['lang']) ? $basic_datas['lang']['tab_faq'] : '';
		//END

		//Devman Extensions - info@devmanextensions.com - 2017-10-18 12:04:36 - Add faq tab
		if(array_key_exists('tab_faq', $basic_datas) && !empty($basic_datas['tab_faq']) && array_key_exists('extension_id', $basic_datas) && !empty($basic_datas['extension_id']))
		{
			$extensions_with_documentation = array(
				'5420686f-9450-4afa-a9c1-0994fa641b0a' => 'https://devmanextensions.com/docs/en/opencart-extensions/google-marketing-tools'
			);
			$link_documentation = array_key_exists($basic_datas['extension_id'], $extensions_with_documentation) ? $extensions_with_documentation[$basic_datas['extension_id']] : '';

			$conditions = array('Faq.extension_id' => $basic_datas['extension_id'], /*'Faq.system' => 'opencart'*/);
			$faqs = $this->Faq->find('all', array('conditions' => $conditions, 'order' => array('Faq.order ASC', 'Faq.created ASC')));

			$tab_help = $this->get_tab_faq($faqs, $link_documentation);
			$form_view['tabs'][$this->lang_text_tab_faq] = $tab_help;
		}
		//END

		//Devman Extensions - info@devmanextensions.com - 2017-08-31 20:03:55 - Add help tab
		if(array_key_exists('tab_help', $basic_datas) && !empty($basic_datas['tab_help']) && array_key_exists('extension_id', $basic_datas) && !empty($basic_datas['extension_id']))
		{
			$id_extension = $basic_datas['extension_id'];
			if(!$this->is_trial) {
				$license_checked = $this->OpencartExtension->check_license($this->license_id, $this->domain);
				$license_expired = !empty($license_checked['expired']);
				$license = $this->OpencartExtension->license_get_license($this->license_id);
				$email = !empty($license) && array_key_exists('Sale', $license) && array_key_exists('buyer_email', $license['Sale']) ? $license['Sale']['buyer_email'] : '';
			} else  {
				$license_expired = false;
				$license = $this->TrialLicense->findByLicenseId($this->license_id);
				$email = !empty($license) && array_key_exists('TrialLicense', $license) ? $license['TrialLicense']['customer_email'] : '';
			}

			$tab_help = $this->get_tab_help($license_expired, $email, $this->is_demo);

			$form_view['tabs'][$this->lang_text_tab_help] = $tab_help;
		}
		//END

		//Devman Extensions - info@devmanextensions.com - 2017-08-31 20:03:55 - Add changelog tab
		if(array_key_exists('tab_changelog', $basic_datas) && !empty($basic_datas['tab_changelog']) && array_key_exists('extension_id', $basic_datas) && !empty($basic_datas['extension_id']))
		{
			$id_extension = $basic_datas['extension_id'];
			$tab_changelog = $this->get_tab_changelog($id_extension);
			$form_view['tabs'][$this->lang_text_tab_changelog] = $tab_changelog;
		}
		//END

		$html = '<form autocomplete="off" action="'.$form_view['action'].'" method="post" enctype="multipart/form-data" id="'.$form_view['id'].'" class="form-horizontal"><input type="hidden" value="0" name="no_exit"><input type="hidden" value="" name="force_function"><input type="hidden" name="'.$this->extension_group_config.'_license_id" value="'.$this->license_id.'">';
		$html .= $this->generateTabs($form_view, $basic_datas);
		$html .= $this->generateContent($form_view);
		$html .= '</form>';

		return $html;
	}

	public function generateTabs($form_view, $basic_datas)
	{
		$system = array_key_exists('system', $basic_datas) ? $basic_datas['system'] : '';
		$html = '';

		//Multi Store - Add select stores
		if(!empty($form_view['multi_store']))
		{
			$html .= '<script type="text/javascript">
		                                '.$this->jquery_symbol.'(document).ready(function(){';
			$html .= $this->oc_2 || $this->oc_4 ? $this->jquery_symbol.'(\'div.tab-content div.store_input\').hide();' : $this->jquery_symbol.'(\'div.content form tr.store_input\').hide();';
			$html .= 'change_store('.$this->jquery_symbol.'("select.select_stores").val(), true);';
			$html .= '});';
			$html .= '</script>';

			$options_select = array();
			foreach ($this->stores as $key => $store)
				$options_select[$store['store_id']] = $store['name'];

			$temp_input = array(
				'label' => '<i class="fa fa-home"></i>'.$this->lang_choose_store,
				'type' => 'select',
				'name' => 'stores',
				'onchange' => 'change_store('.$this->jquery_symbol.'(this).val());',
				'class' => 'select_stores',
				'value' => $this->selected_store_id,
				'options' => $options_select
			);

			if(version_compare($this->version, '1.5.6.4', '<='))
			{
				$temp_input['remove_border_button'] = true;
				$temp_input['before'] = '<table class="form multistore_table"><tbody>';
				$temp_input['after'] = '</tbody></table>';
			}

			$html .= $this->generateField($temp_input);
		}
		//END Multi Store - Add select stores

		if( $this->oc_4 ) {
			$html .= '<ul class="nav nav-tabs">';
			$count = 0;

			foreach( $form_view['tabs'] as $tab_name => $tab ) {
				$html .= '<li class="nav-item"><a class="nav-link '.($count == 0 ? 'active':''). ' tab_'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" href="#tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" data-bs-toggle="tab">'.(!empty($tab['icon'])?$tab['icon']:'').$tab_name.'</a></li>';
				$count++;
			}
			$html .= '</ul>';
		} elseif($this->oc_2) {
			$html .= '<ul class="nav nav-tabs">';
			$count = 0;
			foreach ($form_view['tabs'] as $tab_name => $tab) {
				$html .= '<li '.($count == 0 ? 'class="active"':'').'><a class="tab_'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" href="#tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" data-toggle="tab">'.(!empty($tab['icon'])?$tab['icon']:'').$tab_name.'</a></li>';
				$count++;
			}
			$html .= '</ul>';
		}
		else
		{
			$html .= '<div id="tabs" class="htabs">';
			$count = 0;
			foreach ($form_view['tabs'] as $tab_name => $tab) {
				$html .= '<a class="'.($count == 0 ? 'selected ':'').'tab_'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" href="#tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'">'.(!empty($tab['icon'])?$tab['icon']:'').$tab_name.'</a>';
				$count++;
			}
			$html .= '</div>';
		}

		return $html;
	}

	public function generateField($field, $language = null, $force2 = false)
	{
		if($force2)
			$this->oc_2 = true;

		$extension_name = $this->extension_group_config;

		if (!empty($field['type']) && $field['type'] == 'html_hard')
			return $field['html_code'];

		if(empty($field['name']))
			$field['name'] = '';

		//If possible that call to this function from table inputs, not add extension name to field name.
		if(substr($field['name'], 0, strlen ($extension_name)) != $extension_name)
			$field['name'] = !empty($extension_name) ? $extension_name.'_'.$field['name'] : $field['name'];

		$from_table_input = array_key_exists('from_table_input', $field) && $field['from_table_input'];

		//If input if multilanguage edit id and name
		if(!empty($language) && !$from_table_input)
		{
			$temp_name = $field['name'];
			if (strpos($temp_name, 'language_id') !== false) {
				$field['name'] = $field['id'] = str_replace('language_id', $language['language_id'], $temp_name);
			}
			else
			{
				if(!empty($field['name']) && !empty($language) && substr($field['name'], -1) == ']')
					$field['name'] = $field['id'] = $temp_name.'['.$language['language_id'].']';
				else
					$field['name'] = $field['id'] = $temp_name.'_'.$language['language_id'];
			}

			//Devman Extensions - info@devmanextensions.com - 2017-08-30 20:53:12 - Get value from multilanguage field
			$language_id = $language['language_id'];
			$field['value'] = is_array($field['value']) && array_key_exists($language_id, $field['value']) && !empty($field['value'][$language_id]) ? $field['value'][$language_id] : '';
			//END
		}
		//END If input if multilanguage edit id and name

		//Devman Extensions - info@devmanextensions.com - 2017-02-13 19:10:46 - Default value
		$considered_boolean = array('boolean', 'switch_label');
		$is_boolean = !empty($field['type']) && in_array($field['type'], $considered_boolean);

		if($this->first_configuration && !empty($field['default']) && empty($field['value']) && !$is_boolean)
			$field['value'] = $field['default'];

		//2018-08-02 - He añadido $this->first_configuration porque estaba cargando siempre los defaults en los text inptus
		if($this->first_configuration && !empty($field['default']) && $is_boolean)
			$field['value'] = $field['default'];

		$only_input = isset($field['only_input']) ? $field['only_input'] : false;

		if($only_input)
		{
			if(!isset($field['class']))
				$field['class'] = '';
			$field['class'] .= ' only_input';
		}

		//Devman Extensions - info@devmanextensions.com - 2017-04-29 18:31:24 - Columns to bootstrap
		$columns = !empty($field['columns']) ? 12/$field['columns'] : (!empty($field['force-columns']) ? $field['force-columns'] : '');
		$split_50 = array_key_exists('split_50', $field) && $field['split_50'];
		$span_label = $split_50 ? 6 : (array_key_exists('span_label', $field) ? $field['span_label'] : 2);
		$span_input = $split_50 ? 6 : (array_key_exists('span_input', $field) ? $field['span_input'] : 10);

		$table = !empty($field['table']) ? true : false;

		$html = '';

		$type_class = 'type_'.(array_key_exists('type', $field) ? $field['type'] : 'no_defined');
		$field['class_container'] = array_key_exists('class_container', $field) ? $field['class_container'].' '.$type_class : $type_class;
		if( $this->oc_4 )
		{
			$input_container_begin = '<div '.(!empty($field['store']) ? 'data-store="'.preg_replace('/[^0-9]/', '', $field['store']).'"' : '').' class="'.(!empty($columns) ? 'col-sm-'.$columns.' form-group-columns' : 'row mb-3').(!empty($field['class_container']) ? ' '.$field['class_container'] : '').(!empty($field['store']) ? ' store_input '.$field['store']:'').'" '.(!empty($field['type']) && $field['type'] == 'hidden' ? 'style="display:none;"':'').(!empty($field['style_container']) ? ' style="'.$field['style_container'].'"' : '').'>';
			$input_container_end = '</div></div>';
		}
		elseif($this->oc_2)
		{
			$input_container_begin = '<div '.(!empty($field['store']) ? 'data-store="'.preg_replace('/[^0-9]/', '', $field['store']).'"' : '').' class="'.(!empty($columns) ? 'col-md-'.$columns.' form-group-columns' : 'form-group').(!empty($field['class_container']) ? ' '.$field['class_container'] : '').(!empty($field['store']) ? ' store_input '.$field['store']:'').'" '.(!empty($field['type']) && $field['type'] == 'hidden' ? 'style="display:none;"':'').(!empty($field['style_container']) ? ' style="'.$field['style_container'].'"' : '').'>';
			$input_container_end = '</div></div>';
		}
		else
		{
			$html = '';
			$input_container_begin = '';
			$input_container_end = '';

			if($table)
			{
				$html = '<tr class="field_tr'.(!empty($field['store']) ? ' store_input '.$field['store']:'').'">';
				$input_container_begin = '';
				$input_container_end = '</td>';
			}
			else
			{
				if(!$only_input && (in_array($field['type'], array('text', 'select', 'textarea', 'html_editor')) || in_array($field['type'], $considered_boolean)))
				{
					$html = '<div class="row_no_table">';
					$input_container_begin = '<div class="input_no_table">';
					$input_container_end = '</div></div>';
				}

			}
		}

		if($only_input){
			$input_container_begin = '';
			$input_container_end = '';
		}

		$placeholder = !empty($field['placeholder']) ? $field['placeholder'] : '';
		$placeholder = empty($placeholder) && !empty($field['label']) ? $field['label'] : $placeholder;
		$placeholder = strip_tags($placeholder);

		$flag_in_input = !empty($language['code']) && !empty($field['type']) && $field['type'] == 'text';
		$flag_route = '';

		if($flag_in_input)
			$input_container_end .= '</div>';

		if( $this->oc_4 || $this->oc_2 )
			$html .= $input_container_begin;

		if(!empty($language)) {
			if (version_compare($this->version, '2.2.0.0', '>='))
				$flag_route = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
			else
				$flag_route = 'view/image/flags/' . $language['image'];
		}

		//Label
		if (!empty($field['label']))
		{
			$field['label'] = strip_tags($field['label'], '<img>');

			$language = array_key_exists('insert_flag', $field) ? $this->langs[$field['insert_flag']] : $language;

			if( $this->oc_4 )
			{
				if( empty($language['code']) ) {
					$html .= '<label class="'.(empty($columns) ? 'col-sm-'.$span_label : '').' col-form-label form-label">'.$field['label'].(!empty($field['help'])?'<span data-bs-toggle="tooltip" data-bs-html="true" title="'.$field['help'].'"></span>':'').'</label>';
				}
				else
				{
					$html .= '<label class="'.(empty($columns) ? 'col-sm-'.$span_label : '').' col-form-label form-label">'.(!empty($field['type']) && $field['type'] != 'text' ? '<img src="'.$flag_route.'">&nbsp;&nbsp;' : '').$field['label'].(!empty($field['help'])?'<span data-bs-toggle="tooltip" data-bs-html="true" title="'.$field['help'].'"></span>':'').'</label>';
				}
			}
			elseif($this->oc_2)
			{
				if (empty($language['code']))
					$html .= '<label class="'.(empty($columns) ? 'col-md-'.$span_label : '').' control-label">'.$field['label'].(!empty($field['help'])?'<span data-toggle="tooltip" data-html="true" title="'.$field['help'].'"></span>':'').'</label>';
				else
				{
					$html .= '<label class="'.(empty($columns) ? 'col-md-'.$span_label : '').' control-label">'.(!empty($field['type']) && $field['type'] != 'text' ? '<img src="'.$flag_route.'">&nbsp;&nbsp;' : '').$field['label'].(!empty($field['help'])?'<span data-toggle="tooltip" data-html="true" title="'.$field['help'].'"></span>':'').'</label>';
				}
			}
			else
			{
				if($table)
				{
					$html .= '<td>'.(!empty($language['code']) ? '<img src="view/image/flags/'.$language['image'].'">&nbsp;&nbsp;':'').$field['label'].(!empty($field['help']) ? '<br><span class="help">'.$field['help'].'</span>':'').'</td>'.$input_container_begin;
				}
				elseif(!$table)
				{
					$html .= '<label class="'.(empty($columns) ? 'col-md-'.$span_label : '').' control-label">'.(!empty($language['code']) ? '<img src="view/image/flags/'.$language['image'].'">&nbsp;&nbsp;':'').$field['label'].(!empty($field['help']) ? '<br><span class="help">'.$field['help'].'</span>':'').'</label>'.$input_container_begin;
				}
			}
		}
		//END Label

		//Container parent input
		if( !$only_input && $this->oc_4 )
		{
			$full_width = in_array($field['type'], array('legend','table_inputs')) || empty($field['label']) || ($field['type'] == 'html_code' && empty($field['label']));

			$html .= '<div class="'.($full_width ? 'col-sm-12' : (empty($columns) ? 'col-sm-'.$span_input :'')).'"'.(!empty($field['style_content']) ? ' style="'.$field['style_content'].'"': '').'>';

			if(!empty($flag_in_input))
				$html .= '<div class="input-group">';
		}
		elseif(!$only_input && $this->oc_2)
		{
			$full_width = in_array($field['type'], array('legend','table_inputs')) || empty($field['label']) || ($field['type'] == 'html_code' && empty($field['label']));

			$html .= '<div class="'.($full_width ? 'col-md-12' : (empty($columns) ? 'col-md-'.$span_input :'')).'"'.(!empty($field['style_content']) ? ' style="'.$field['style_content'].'"': '').'>';

			if(!empty($flag_in_input))
				$html .= '<div class="input-group">';

		}elseif(!$only_input && version_compare($this->version, '1.5.6.4', '<='))
		{
			$full_width = in_array($field['type'], array('legend','html_code','module','button','table','table_inputs')) && empty($field['label']);

			if($full_width)
				$html .= $field['type'] != "module" ? '<td colspan="2">':'<td style="padding:20px 0px !important;" colspan="2">';
			else
				$html .= '<td'.(!empty($field['style_content']) ? ' style="'.$field['style_content'].'"': '').'>';
		}
		//END Container parent input

		if(!array_key_exists('value', $field))
			$field['value'] = '';

		//Input
		$field['type'] = empty($field['type']) ? '' : $field['type'];
		switch ($field['type']) {
			case 'boolean':
				if( $this->oc_4 ) {
					$html .= '<div class="form-check form-switch form-switch-lg"><input type="checkbox" name="'.$field['name'].'" value="1" class="form-check-input' . (!empty($field['class']) ? ' '.$field['class']:'').'" '.(!empty($field['data']) ? ' '.implode(' ', $field['data']):'') . (!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '') . ($field['value']==1 ? 'checked':'').'/></div>';
				} else {
					$html .= '<label class="checkbox_container'.(!empty($field['disabled']) ? ' disabled':'').'"><input '.(!empty($field['data']) ? ' '.implode(' ', $field['data']):'').(!empty($field['disabled']) ? ' disabled="disabled" ':'').(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').' name="'.$field['name'].'" type="checkbox" class="ios-switch green'.(!empty($field['class']) ? ' '.$field['class']:'').'" value="1"'.($field['value']==1 ? 'checked="selected"':'').'/><div><div></div></div></label>';
				}
				break;

			case 'radio':
				$html .= '<input '.(!empty($field['disabled']) ? ' disabled="disabled" ':'').(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').' name="'.$field['name'].'" type="radio" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'" value="'.$field['value_checked'].'"'.($field['value']==$field['value_checked'] ? 'checked="selected"':'').'/>';
				break;

			case 'switch_label':
				if($this->oc_2)
					$html .= '<input '.(!empty($field['disabled']) ? ' disabled="disabled" ':'').'name="'.$field['name'].'" class="switch_label" data-on-text="'.(!empty($field['on_label']) ? $field['on_label'] : 'ON').'" data-off-text="'.(!empty($field['off_label']) ? $field['off_label'] : 'OFF').'" data-label-text="'.(!empty($field['middle_label']) ? $field['middle_label'] : '').'" value="1" type="checkbox"'.($field['value']==1 ? ' checked':'').'>';
				else
				{
					$html .= '<select'.(!empty($field['disabled']) ? ' disabled="disabled" ':'').' name="'.$field['name'].'" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'">';
					$html .= '<option value="1" '.(!empty($field['value']) ? ' selected="selected"':'').'>'.(!empty($field['middle_label']) ? $field['middle_label'] : '').' - '.(!empty($field['on_label']) ? $field['on_label'] : 'ON').'</option>';
					$html .= '<option value="0" '.(empty($field['value']) ? ' selected="selected"':'').'>'.(!empty($field['middle_label']) ? $field['middle_label'] : '').' - '.(!empty($field['off_label']) ? $field['off_label'] : 'OFF').'</option>';
					$html .= '</select>';
				}
				break;

			case 'text':
			case 'password':
				if($flag_in_input && $this->oc_2)
					$html .= '<span class="input-group-addon multilanguage_flag"><img src="'.$flag_route.'"></span>';

				if( $this->oc_4 ) {
					$is_autocomplete = isset($field['after']) && !empty($field['after']) && strpos($field['after'], '<datalist></datalist>') !== false ? true : false;

					if( version_compare($this->version, '4.0.2.0', '>=') ) {
									$html .= '<input '.(!empty($placeholder) ? 'placeholder="'.$placeholder.'" ':'').'id="'.$field['name'].'" name="'.$field['name'].'" type="'.$field['type'].'" value="'.$field['value'].'" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').(!empty($field['onkeyup']) ? ' onkeyUp="'.$field['onkeyup'].'"' : '').(!empty($field['disabled']) ? ' disabled':'').(!empty($field['readonly']) ? ' readonly':''). ($is_autocomplete ? ' data-oc-target="autocomplete-'.$field['name'].'" autocomplete="off"' : '' ) . '/>';
								} else {
									$html .= '<input '.(!empty($placeholder) ? 'placeholder="'.$placeholder.'" ':'').'id="'.$field['name'].'" name="'.$field['name'].'" type="'.$field['type'].'" value="'.$field['value'].'" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').(!empty($field['onkeyup']) ? ' onkeyUp="'.$field['onkeyup'].'"' : '').(!empty($field['disabled']) ? ' disabled':'').(!empty($field['readonly']) ? ' readonly':''). ($is_autocomplete ? ' list="list-'.$field['name'].'"' : '' ) . '/>';
								}
				} else {
					$html .= '<input '.(!empty($placeholder) ? 'placeholder="'.$placeholder.'" ':'').'id="'.$field['name'].'" name="'.$field['name'].'" type="'.$field['type'].'" value="'.$field['value'].'" class="form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? ' onChange="'.$field['onchange'].'"' : '').(!empty($field['onkeyup']) ? ' onkeyUp="'.$field['onkeyup'].'"' : '').(!empty($field['disabled']) ? ' disabled':'').(!empty($field['readonly']) ? ' readonly':'').'/>';
				}
				break;

			case 'select':
				if(!empty($field['multiple']) && substr($field['name'], -2) != '[]')
					$field['name'] .= '[]';

				//$field['value'] = html_entity_decode($field['value']);

				$html .= '<select'.(!empty($field['multiple']) ? ' multiple="multiple"':'').(!empty($field['all_options']) ? ' data-actions-box="true"':'').(!empty($field['noneSelectedText']) ? ' title="'.$field['noneSelectedText'].'" ':'').' name="'.$field['name'].'" class="selectpicker form-control'.(!empty($field['class']) ? ' '.$field['class']:'').'"'.(!empty($field['onchange']) ? 'onChange="'.$field['onchange'].'"' : '').' data-live-search="true">';

				$count_option = 0;

				foreach ($field['options'] as $option_value => $option_name) {

					//Option groups
					if(is_array($option_name))
					{
						$html .= '<optgroup label="'.$option_name['option_group'].'">';
						foreach ($option_name['options'] as $option_value_2 => $option_name_2) {
							$selected = false;
							if(!empty($field['multiple']))
							{
								foreach ($field['value'] as $key => $val) {
									if($option_value_2 === $val || (is_numeric($option_value_2) && $option_value_2 == $val))
									{
										$selected = true;
										break;
									}
								}
							}
							else
								$selected = $option_value_2 === $field['value'] || (is_numeric($option_value_2) && $option_value_2 == $field['value']);

							$html .= '<option '.(empty($option_name_2) && !is_numeric($option_name_2) && empty($option_value_2) && !is_numeric($option_value_2) ? 'data-hidden="true"' : '').' data-content="'.$option_name_2.'" value="'.$option_value_2.'"'.($selected ? ' selected="selected"': '').' data-optionposition="'.$count_option.'">'.$option_name_2.'</option>';
							$count_option++;
						}
						$html .= '</optgroup>';
					}

					//Option normal
					else
					{
						$selected = false;
						if(!empty($field['multiple']))
						{
							if(!empty($field['value']))
							{
								foreach ($field['value'] as $key => $val) {
									if($option_value === $val || (is_numeric($option_value) && $option_value == $val))
									{
										$selected = true;
										break;
									}
								}
							}
						}
						else
							$selected = $option_value === $field['value'] || (is_numeric($option_value) && $option_value == $field['value']);

						$html .= '<option '.(empty($option_name) && !is_numeric($option_name) && empty($option_value) && !is_numeric($option_value) ? 'data-hidden="true"' : '').' data-content="'.$option_name.'" value="'.$option_value.'"'.($selected ? ' selected="selected"': '').' data-optionposition="'.$count_option.'">'.$option_name.'</option>';
						$count_option++;
					}
				}
				$html .= '</select>';
				break;

				break;

			case 'legend':
				$html .= '<legend'.(!empty($field['style']) ? ' style="'.$field['style'].'"': '').(!empty($field['class']) ? ' class="'.$field['class'].'"': '').'>'.$field['text'].'</legend><div style="clear:both;"></div>';
				break;

			case 'html_code':
				$html .= $field['html_code'];
				break;

			case 'date':
				$html .= '<div class="input-group date'.(!empty($field['class']) ? $field['class'] : '').'">';
				$html .= '<input type="text" name="'.$field['name'].'" value="'.$field['value'].'" data-date-format="YYYY-MM-DD" data-format="YYYY-MM-DD" id="'.$field['name'].'" class="form-control date" />';
				if($this->oc_2)
				{
					$html .= '<span class="input-group-btn">';
					$html .= '<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
					$html .= '</span>';
				}
				$html .= '</div>';
				break;

			case 'textarea':
				$html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control'.(!empty($field['class']) ? ' '.$field['class'] : '').'" id="'.$field['name'].'" name="'.$field['name'].'" />'.$field['value'].'</textarea>';
				break;

			case 'html_editor':
				$id = $field['name'];
				if( $this->oc_4 )
				{
					$html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'" data-oc-toggle="ckeditor" data-lang="'.$this->ckeditor_lang.'" />'.$field['value'].'</textarea>';

					$html .= '<script type="text/javascript">';
					$html .= $this->jquery_symbol.'(\'#'.$id.'\').ckeditor();';
					$html .= '</script>';
				}
				elseif($this->oc_2)
				{
					$html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'"'.($this->oc_3 ? ' data-lang="'.$field['lang_code'].'" data-toggle="summernote"' : '').' />'.$field['value'].'</textarea>';

					if(!$this->oc_3)
					{
						$html .= '<script type="text/javascript">';
						$html .= $this->jquery_symbol.'(document).on(\'ready\', function(){';
						$html .= $this->jquery_symbol.'(\'#'.$id.'\').summernote({onChange: function(e) { var value = $(event.target).html(); $(event.target).closest(\'div.note-editor\').prev(\'textarea\').val(value); },height: '.(!empty($field['height']) ? $field['height'] : 300).'});';
						$html .= '});';
						$html .= '</script>';
					}
				}
				else
				{
					$html .= '<textarea '.(!empty($field['style']) ? 'style="'.$field['style'].'" ':'').' placeholder="'.$placeholder.'" class="form-control" id="'.$field['name'].'" name="'.$field['name'].'" />'.$field['value'].'</textarea>';

					$html .= '<script type="text/javascript">';
					$html .= $this->jquery_symbol.'(document).on(\'ready\', function(){';
					$html .= 'CKEDITOR.replace(\''.$id.'\', {';
					$html .= 'filebrowserBrowseUrl: \'index.php?route=common/filemanager&token='.$this->token.'\',';
					$html .= 'filebrowserImageBrowseUrl: \'index.php?route=common/filemanager&token='.$this->token.'\',';
					$html .= 'filebrowserFlashBrowseUrl: \'index.php?route=common/filemanager&token='.$this->token.'\',';
					$html .= 'filebrowserUploadUrl: \'index.php?route=common/filemanager&token='.$this->token.'\',';
					$html .= 'filebrowserImageUploadUrl: \'index.php?route=common/filemanager&token='.$this->token.'\',';
					$html .= 'filebrowserFlashUploadUrl: \'index.php?route=common/filemanager&token='.$this->token.'\'';
					$html .= '});';

					$html .= '});';
					$html .= '</script>';
				}
				break;

			case 'file':
				$html .= '<input class="form-control" id="'.$field['name'].'" placeholder="'.$placeholder.'" name="'.$field['name'].'" '.(array_key_exists('multiple', $field) && !empty($field['multiple']) ? 'multiple="multiple"' : '').' type="file" />';
				break;

			case 'hidden':
				$html .= '<input id="'.$field['name'].'"'.(!empty($field['class']) ? ' class="'.$field['class'].'"' : '').' name="'.$field['name'].'" type="hidden" value="'.$field['value'].'" />';
				break;

			case 'colpick':
				/*
				Need Colpick library in view.tpl and call event in ready.
				<link rel="stylesheet" type="text/css" href="view/stylesheet/colpick.css" />
				<script type="text/javascript" src="view/javascript/colpick.js"></script>
				*/
				$html .= '<input name="'.$field['name'].'" type="hidden" value="'.$field['value'].'">';
				$html .= '<div id="'.$field['name'].'"></div>';

				if( $this->oc_4 )
				{
					$html .= '<script type="text/javascript">';
					$html .= $this->jquery_symbol.'(\'#'.$field["name"].'\').colpick({';
					$html .= 'flat:true,';
					$html .= 'layout:\'rgbhex\',';
					$html .= 'submit:0,';
					if ($field["value"] != "")
						$html .= 'color: \''.$field["value"].'\',';
					$html .= 'onChange: function (hsb, hex, rgb) {';
					$html .= $this->jquery_symbol.'(\'input[name="'.$field["name"].'"]\').val(hex);';
					$html .= '}';
					$html .= '});';
					$html .= '</script>';
				}
				else
				{
					$html .= '<script type="text/javascript">';
					$html .= $this->jquery_symbol.'(document).on(\'ready\', function(){';
					$html .= $this->jquery_symbol.'(\'#'.$field["name"].'\').colpick({';
					$html .= 'flat:true,';
					$html .= 'layout:\'rgbhex\',';
					$html .= 'submit:0,';
					if ($field["value"] != "")
						$html .= 'color: \''.$field["value"].'\',';
					$html .= 'onChange: function (hsb, hex, rgb) {';
					$html .= $this->jquery_symbol.'(\'input[name="'.$field["name"].'"]\').val(hex);';
					$html .= '}';
					$html .= '});';
					$html .= '});';
					$html .= '</script>';
				}
				break;


			case 'image':
				$placeholder_empty = $this->no_image_thumb;

				//Devman Extensions - info@devmanextensions.com - 2017-09-04 20:42:58 - If field is from input table
				if(is_array($field['value']))
				{
					$value = $field['value']['value'];
					$placeholder = !empty($field['value']['thumb']) ? $field['value']['thumb'] : $placeholder_empty;
				}
				else
				{
					$value = $field['value'];
					//$placeholder = !empty($field['thumb']) ? $field['thumb'] : $placeholder_empty;
					$placeholder = !empty($field['thumb']) ? $field['thumb'][$language['language_id']] : $placeholder_empty;
				}
				//END

				if($this->oc_2)
				{
					$name_formated = str_replace(array('[',']'), '-', $field["name"]);

					$html .= '<div><a id="thumb-'.$name_formated.'" href="" data-toggle="image" class="img-thumbnail'.(!empty($field['class']) ? $field['class']:'').'"><img src="'.$placeholder.'" alt="" title="" data-placeholder="'.$placeholder_empty.'" /></a>';
					$html .= '<input id="input-'.$name_formated.'" type="hidden" name="'.$field["name"].'" value="'.$value.'" /></div>';
				}
				else
				{
					$formatted_name = str_replace(array('[',']'), '-', $field['name']);

					$html .= '<div class="image'.(!empty($field['class']) ? $field['class']:'').'">';
					$html .= '<img src="'.$placeholder.'" alt="" id="thumb-'.$formatted_name.'" />';
					$html .= '<input type="hidden" name="'.$field['name'].'" value="'.$value.'" id="image-'.$formatted_name.'" /><br />';
					$html .= '<a onclick="image_upload(\'image-'.$formatted_name.'\', \'thumb-'.$formatted_name.'\');">'.$this->lang_text_browse.'</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
					$html .= '<a onclick="'.$this->jquery_symbol.'(\'#thumb-'.$field['name'].'\').attr(\'src\', \''.$placeholder_empty.'\'); '.$this->jquery_symbol.'(\'#image-'.$formatted_name.'\').attr(\'value\', \'\');">'.$this->lang_text_clear.'</a>';
					$html .= '</div>';
				}
				break;

			case 'button':
				$html .= '<a'.(!empty($field['data']) ? ' data-'.$field['data']['name'].'="'.$field['data']['value'].'"' : '').''.(!empty($field['onclick']) ? ' onclick="'.$field['onclick'].'"' : '').''.(!empty($field['href']) ?  ' href="'.$field['href'].'"' : '').' class="button'.(!empty($field['class']) ? ' '.$field['class']:'').'">'.$field['text'].'</a>';
				break;

			case 'table':
				$html .= $this->field_generate_table($field['theads'], $field['data'], $field, array_key_exists('value', $field) && !empty($field['value']) ? $field['value'] : array());
				break;

			case 'table_inputs':
				$html .= $this->field_generate_table_inputs($field);
				break;

			case 'button_upload_images':
				$html .= '<a onclick="image_upload();" class="button">'.$field['text'].'</a>';
				break;

			case 'products_autocomplete':
				$html .= $this->field_generate_products_autocomplete($field);
				break;

			case 'module':
				$html .= $this->field_generate_module_oc15x($field);
				break;

			default:
				# code...
				break;
		}
		//END Input
		if( array_key_exists('help_bottom', $field) ) {
			if( $this->oc_4 ) {
				$html .= '<span class="form-text">'.$field['help_bottom'].'</span>';
			} else {
				$html .= '<span class="help_bottom">'.$field['help_bottom'].'</span>';
			}
		}

		if( !empty($field['after']) ) {
			if( strpos($field['after'], '<datalist></datalist>') !== false ) {
				if( $this->oc_4 ) {
					if( version_compare($this->version, '4.0.2.0', '>=')) {
								$field['after'] = str_replace('<datalist></datalist>', '<ul id="autocomplete-'.$field['name'].'" class="dropdown-menu"></ul>', $field['after']);
							} else {
								$field['after'] = str_replace('<datalist>', '<datalist id="list-'.$field['name'].'">', $field['after']);
							}
				} else {
					$field['after'] = str_replace('<datalist></datalist>', '', $field['after']);
				}
			}

			$html .= $field['after'];
		}

		$html .= $input_container_end;

		return $html;
	}

	public function field_generate_table($theads, $fields, $table_extra, $config_data = array())
	{
		$html = '';

		$html = '<table '.(!empty($table_extra['id']) ? 'id="'.$table_extra['id'].'"' : '').' class="list table table-bordered table-hover '.(!empty($table_extra['class']) ? $table_extra['class'] : '').'"'.(!empty($table_extra['style']) ? ' style="'.$table_extra['style'].'"' : '').'>';
		if(!empty($theads))
		{
			if(!empty($table_extra['order_draggable']))
				array_unshift($theads, $this->lang_text_sort_order);

			$html .= '<thead>';
			$html .= '<tr>';
			$number_column = 0;
			foreach ($theads as $key => $th_name) {
				$hide_td = empty($th_name);
				$html .= '<td class="left"'.($hide_td ? ' style="display:none;"' : '').'>'.$th_name.'<a class="hide_column" onclick="hide_column('.$this->jquery_symbol.'(this), '.$number_column.');"><i class="fa fa-toggle-left"></i></a></td>';
				if(!$hide_td)
					$number_column++;
			}
			$html .= '</tr>';
			$html .= '</thead>';
		}
		$html .= '<tbody>';
		foreach ($fields as $key => $values) {
			$html .= '<tr data-numrow="'.$key.'">';

			if(!empty($table_extra['order_draggable']))
				$html .= '<td class="text-left draggable_element"><i class="fa fa-reorder"></i></td>';

			foreach ($values as $key2 => $val_real) {

				if(!is_array($val_real))
					$html .= '<td class="text-left">'.$val_real.'</td>';
				else
				{
					$temp_inputs = array();
					$insert_clear = false;
					if(!empty($val_real[0]) && is_array($val_real[0]))
					{
						$insert_clear = true;
						foreach ($val_real as $input) {
							$temp_inputs[] = $input;
						}
					}
					else
						$temp_inputs[] = $val_real;

					$hidden_field = !empty($val_real['type']) && $val_real['type'] == 'hidden';

					$html .= '<td class="text-left"'.($hidden_field ? ' style="display:none;"' : '').'>';
					foreach ($temp_inputs as $key => $val_real) {

						$input_name = !empty($val_real['name']) ? $val_real['name'] : '';

						//Devman Extensions - info@devmanextensions.com - 2017-02-27 18:40:58 - Get value
						$value = !empty($val_real['value']) ? $val_real['value'] : '';

						if(empty($value))
						{
							$indexes = array();
							preg_match_all("/\[([^\]]*)\]/", $input_name, $indexes);

							if(!empty($indexes[1]) && count($indexes[1]) == 1)
							{
								$index_0 = $indexes[1][0];

								$value = !empty($config_data[$index_0]) ? $config_data[$index_0] : '';
							}
							elseif(!empty($indexes[1]) && count($indexes[1]) == 2)
							{
								$index_0 = $indexes[1][0];
								$index_1 = $indexes[1][1];

								$value = !empty($config_data[$index_0][$index_1]) ? $config_data[$index_0][$index_1] : '';
							}
						}
						//END

						$val_real['name'] = (!empty($table_extra['preffix_config']) ? $table_extra['preffix_config'] : '') . $input_name;

						if(!empty($val_real['multilanguage']))
						{
							$html .= '';
							foreach ($this->langs as $language_id => $language) {

								//Devman Extensions - info@devmanextensions.com - 2017-02-27 18:40:58 - Get value
								if(!empty($indexes[1]) && count($indexes[1]) == 1)
								{
									$index_0 = $indexes[1][0];

									$value = !empty($config_data[$index_0][$language['language_id']]) ? $config_data[$index_0][$language['language_id']] : '';
								}
								elseif(!empty($indexes[1]) && count($indexes[1]) == 2)
								{
									$index_0 = $indexes[1][0];
									$index_1 = $indexes[1][1];

									$value = !empty($config_data[$index_0][$index_1][$language['language_id']]) ? $config_data[$index_0][$index_1][$language['language_id']] : '';
								}
								//END
								if(version_compare($this->version, '2.2.0.0', '>='))
									$flag_route = 'language/'.$language['code'].'/'.$language['code'].'.png';
								else
									$flag_route = 'view/image/flags/'.$language['image'];

								$temp_input = $val_real;
								$temp_input['value'] = $value;
								$temp_input['name'] = $temp_input['name'].'['.$language_id.']';
								$temp_input['only_input'] = true;
								$temp_input['lang_code'] = $language['code'];

								$flag_in_input = false;

								if(version_compare($this->version, '2.0.0.0', '<') || ($this->oc_2 && !empty($temp_input['type']) && $temp_input['type'] != 'text'))
									$html .= '<img src="'.$flag_route.'">&nbsp;&nbsp;';
								else
								{
									$flag_in_input = true;
									$html .= '<div class="input-group"><span class="input-group-addon multilanguage_flag"><img src="'.$flag_route.'"></span>';
								}

								$html .= $this->generateField($temp_input);

								if(!empty($flag_in_input))
									$html .= '</div>';

								$html .= '<div style="clear:both;"></div>';
							}
						}
						else
						{
							$val_real['value'] = $value;
							$val_real['only_input'] = true;
							$html .= $this->generateField($val_real);
						}

						$html .= $insert_clear ? '<div style="clear:both;"></div>' : '';
					}
					$html .= '</td>';
				}
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		
		//Devman Extensions - andres@devmanextensions.com - 2023-12-26 11:13:00 - Add support to tfoot in simple table
		if (!empty($table_extra) && isset($table_extra['tfoot'])) {
			$html .= '<tfoot>';
				$html .= $table_extra['tfoot'];
			$html .= '</tfoot>';
		}
		
		$html .= '</table>';

		return $html;
	}

	public function generateContent($form_view)
	{
		$html = '';
		if($this->oc_2 || $this->oc_4) $html = '<div class="tab-content">';

		$count = 0;

		foreach ($form_view['tabs'] as $tab_name => $tab) {

			$table = false;
			if($this->oc_2 || $this->oc_4)
			{
				$html .= '<div class="tab-pane'.($count == 0 ? ' active':'').'" id="tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'">';
			}
			else
			{
				$table = !isset($tab['no_table']) || !$tab['no_table'];

				$html .= '<div id="tab-'.$this->formatName(str_replace(' ', '-',strtolower($tab_name))).'" class="tab-pane '.(!$table ? ' no_table' : '').'">';
				if(!$table) $html .= '<div style="position:relative; float:left; width:98%; padding: 10px;">';
				if($table) $html .= '<table class="form">';
				if($table) $html .= '<tbody>';
			}
			if (!empty($tab['fields']))
			{
				foreach ($tab['fields'] as $key => $input)
				{
					$is_multistore = !empty($form_view['multi_store']) && !array_key_exists('no_multistore', $tab) && !in_array($input['type'], array('legend', 'html_code'));
					
					// 2024-05-16: Add support to set $is_multistore in false if the html_type input has the attr multi_store in false
					if ( $input['type'] === 'html_hard' && array_key_exists('multi_store', $input) && $input['multi_store'] === false) {
						$is_multistore = false;
					}

					$input['table'] = $table;

					if (isset($input['multilanguage']) && !empty($input['multilanguage']))
					{
						foreach ($this->langs as $key => $lng)
						{
							if($is_multistore)
							{
								$is_first_store = true;
								foreach ($this->stores as $key => $store)
								{
									if(!$is_first_store && !empty($input['skip_for_multistore']))
										continue;

									$is_first_store = false;
									$temp_input = $input;
									$temp_input['store'] = 'store_'.$store['store_id'];
									$temp_input['name'] .= '_'.$store['store_id'];
									$temp_input['value'] = $temp_input['value'][$store['store_id']];
									$temp_input['thumb'] = array_key_exists('thumb', $temp_input) && is_array($temp_input['value']) ? $temp_input['thumb'][$store['store_id']] : '';
									$base_name_input = $temp_input['name'];
									$base_value_input = $temp_input['value'];
									$repeat = !empty($temp_input['repeat']) ? $temp_input['repeat'] : 1;

									for ($i=0; $i < $repeat; $i++) {
										if($repeat > 1)
										{
											$temp_input['name'] = $base_name_input . '['.$i.']';
											$temp_input['value'] = $base_value_input[$i];
										}
										$html .= $this->generateField($temp_input, $lng);
									}
								}
							}
							else
							{
								$repeat = !empty($input['repeat']) ? $input['repeat'] : 1;
								$base_name_input = $input['name'];
								$base_value_input = !empty($input['value']) ? $input['value'] : '';
								for ($i=0; $i < $repeat; $i++) {
									if($repeat > 1)
									{
										$input['name'] = $base_name_input . '['.$i.']';
										$input['value'] = !empty($base_value_input[$i]) ? $base_value_input[$i] : '';
									}
									$html .= $this->generateField($input, $lng);
								}
							}
						}
					}
					else
					{
						if($is_multistore)
						{
							$is_first_store = true;

							foreach ($this->stores as $key => $store)
							{
								if(!$is_first_store && !empty($input['skip_for_multistore']))
									continue;

								$is_first_store = false;
								$temp_input = $input;

								if(!isset($temp_input['name']))
									$temp_input['name'] = '';

								$temp_input['store'] = 'store_'.$store['store_id'];
								$temp_input['name'] .= '_'.$store['store_id'];

								if(!array_key_exists('value', $temp_input))
									$temp_input['value'] = '';
								else
									$temp_input['value'] = $temp_input['value'][$store['store_id']];

								if(!array_key_exists('thumb', $temp_input))
									$temp_input['thumb'] = '';
								else
									$temp_input['thumb'] = $temp_input['thumb'][$store['store_id']];

								$repeat = !empty($temp_input['repeat']) ? $temp_input['repeat'] : 1;
								$base_name_input = $temp_input['name'];
								for ($i=0; $i < $repeat; $i++) {
									if($repeat > 1)
										$temp_input['name'] = $base_name_input . '['.$i.']';
									$html .= $this->generateField($temp_input);
								}
							}
						}
						else
						{
							$repeat = !empty($input['repeat']) ? $input['repeat'] : 1;
							$base_name_input = !empty($input['name']) ? $input['name'] : '';
							for ($i=0; $i < $repeat; $i++) {
								if($repeat > 1)
									$input['name'] = $base_name_input . '['.$i.']';
								$html .= $this->generateField($input);
							}
						}
					}
				}
			}
			else
			{
				$html .= $tab['custom_content'];
			}
			if(version_compare($this->version, '1.5.6.4', '<='))
			{
				if($table) $html .= '</tbody>';
				if($table) $html .= '</table>';
				if(!$table) $html .= '</div><div style="clear:both;"></div>';
				$html .= '</div>';
			}
			else
				$html .= '</div>';
			$count++;
		}

		if($this->oc_2 || $this->oc_4) $html .= '</div>';

		return $html;
	}

	public function field_generate_table_inputs($table_input)
	{
		$this->jquery_symbol = $this->jquery_symbol == '' ? '$' : $this->jquery_symbol;
		$html = '';

		$add_button = isset($table_input['add_button']) ? $table_input['add_button'] : true;
		$delete_button = isset($table_input['delete_button']) ? $table_input['delete_button'] : true;
		$theads = $table_input['theads'];
		$count_theads = 0;
		$model_row = $table_input['model_row'];

		$clone_button = !empty($table_input['clone_button']);

		$data = array();

		$datas = array_key_exists('value', $table_input) && !empty($table_input['value']) ? $table_input['value'] : array();

		$num_rows = !empty($datas) ? count($datas)+1 : 0;
		$html = '<table data-rows="'.$num_rows.'" class="list table table-bordered table-hover'.(!empty($table_input['store']) ? ' store_input '.$table_input['store']:'').' '.(!empty($table_input['class']) ? $table_input['class'] : '').'"'.(!empty($table_input['style']) ? ' style="'.$table_input['style'].'"' : '').'>';

		$html .= '<thead>';
		$html .= '<tr>';
		foreach ($theads as $key => $th_name) {
			$html .= '<td class="left"'.(empty($th_name) ? ' style="display:none;"':'').'>'.$th_name.'<a class="hide_column" onclick="hide_column('.$this->jquery_symbol.'(this), '.$key.');"><i class="fa fa-toggle-left"></i></a></td>';
			if(!empty($th_name)) $count_theads++;
		}
		if($clone_button)
		{
			$count_theads++;
			$html .= '<td>'.$this->lang_text_clone_row.'</td>';
		}
		if($add_button || $delete_button)
			$html .= '<td></td>';
		$html .= '</tr>';
		$html .= '</thead>';

		$html .= '<tbody>';
		if(!empty($datas))
		{
			foreach ($datas as $key => $datas_inputs) {
				$html .= '<tr data-numrow="'.$key.'">';
				foreach ($model_row as $key2 => $fields) {
					$html .= '<td' . (!empty($field['td_hidden']) ? ' style="display:none;"' : '') . '>';
					$final_fields = array_key_exists('multiples_fields', $fields) ? $fields['multiples_fields'] : array($fields);
					foreach ($final_fields as $key3 => $field) {
						$input_name = $field['name'];
						$input_final = $field;
						$input_final['name'] = $table_input['name'] . '[' . $key . '][' . $input_name . ']';

						$input_final['value'] = array_key_exists($input_name, $datas_inputs) ? $datas_inputs[$input_name] : '';
						$input_final['only_input'] = true;

						$repeat = !empty($input_final['repeat']) ? $input_final['repeat'] : 1;
						$base_name_input = $input_final['name'];
						$base_value_input = $input_final['value'];

						for ($i = 0; $i < $repeat; $i++) {
							if ($repeat > 1) {
								$input_final['name'] = $base_name_input . '[' . $i . ']';
								$input_final['value'] = !empty($base_value_input[$i]) ? $base_value_input[$i] : '';
							}

							if (!empty($input_final['multilanguage'])) {
								foreach ($input_final['value'] as $language_code => $value) {
									$language = $this->langs[$language_code];

									if (version_compare($this->version, '2.2.0.0', '>='))
										$flag_route = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
									else
										$flag_route = 'view/image/flags/' . $language['image'];

									$temp_input = $input_final;
									$temp_input['value'] = $temp_input['value'][$language_code];
									$temp_input['name'] = $temp_input['name'] . '[' . $language_code . ']';

									if(version_compare($this->version, '2.0.0.0', '<'))
										$html .= '<img src="'.$flag_route.'">&nbsp;&nbsp;';
									$temp_input['from_table_input'] = true;
									$html .= (version_compare($this->version, '2', '>=') ? '<div class="input-group">' : '').$this->generateField($temp_input, $language).(version_compare($this->version, '2', '>=') ? '</div>' : '');
									$html .= '<div style="clear:both;"></div>';
								}
							} else {
								$html .= $this->generateField($input_final);
							}
						}
					}
					$html .= '</td>';
				}

				if($clone_button)
					$html .= '<td><a class="btn btn-primary" onclick="clone_tr('.$this->jquery_symbol.'(this));"><i class="fa fa-copy" style="margin-right:0px;"></i></a></td>';

				if($add_button)
				{
					$html .= '<td>';
					if($delete_button)
						$html .= '<a class="btn btn-danger" onclick="'.$this->jquery_symbol.'(this).closest(\'tr\').remove();"><i class="fa fa-minus-circle" style="margin-right:0px;"></i></a>';
					$html .= '</td>';
				}
				$html .= '</tr>';
			}
		}

		$html .= '<tr class="model_row" style="display:none;">';
		foreach ($model_row as $key => $field) {
			$html .= '<td'.(!empty($field['td_hidden']) ? ' style="display:none;"':'').'>';
			$fields = array();
			if(!empty($field['multiples_fields']))
				$fields = $field['multiples_fields'];
			else
				$fields = array($field);

			foreach ($fields as $key => $fi) {
				$fi['only_input'] = true;

				if (!empty($fi['name']) && strpos($fi['name'], '[') !== false)
					$fi['name'] = $table_input['name'].'[replace_by_number]'.(!empty($fi['name']) ? $fi['name'] : '');
				else
					$fi['name'] = $table_input['name'].'[replace_by_number]['.(!empty($fi['name']) ? $fi['name'] : '').']';

				$repeat = !empty($fi['repeat']) ? $fi['repeat'] : 1;
				$base_name_input = $fi['name'];
				for ($i=0; $i < $repeat; $i++) {
					if($repeat > 1)
						$fi['name'] = $base_name_input . '['.$i.']';
					if (!empty($fi['multilanguage']))
					{
						foreach ($this->langs as $key_lng => $lng) {
							if(version_compare($this->version, '2.2.0.0', '>='))
								$flag_route = 'language/'.$lng['code'].'/'.$lng['code'].'.png';
							else
								$flag_route = 'view/image/flags/'.$lng['image'];

							if(version_compare($this->version, '2.0.0.0', '<'))
								$html .= '<img src="'.$flag_route.'">&nbsp;&nbsp;';

							$html .= (version_compare($this->version, '2', '>=') ? '<div class="input-group">' : '').$this->generateField($fi, $lng).(version_compare($this->version, '2', '>=') ? '</div>' : '');
							$html .= '<div style="clear:both;"></div>';
						}
					}
					else
					{
						$html .= $this->generateField($fi);
					}
				}
			}

			$html .= '</td>';
		}

		if($clone_button)
			$html .= '<td><a class="btn btn-primary" onclick="clone_tr('.$this->jquery_symbol.'(this));"><i class="fa fa-copy" style="margin-right:0px;"></i></a></td>';

		if($delete_button)
			$html .= '<td><a class="btn btn-danger" onclick="'.$this->jquery_symbol.'(this).closest(\'tr\').remove();"><i class="fa fa-minus-circle" style="margin-right:0px;"></i></a></td>';
		$html .= '</tr>';

		$html .= '</tbody>';

		if($add_button)
		{
			$html .= '<tfoot>';
			$html .= '<tr><td colspan="'.($count_theads).'"></td><td><a class="btn btn-primary" onclick="add_row_table_input('.$this->jquery_symbol.'(this));events_after_add_new_row_table_inputs('.$this->jquery_symbol.'(this));'.(!empty($table_input['add_button_extra_action']) ? $table_input['add_button_extra_action'] : '').'"><i class="fa fa-plus-circle" style="margin-right:0px;"></i></a></td></tr>';
			$html .= '</tfoot>';
		}
		$html .= '</table>';

		return $html;
	}

	public function field_generate_products_autocomplete($field)
	{
		$only_input = !empty($field['only_input']);

		$html = "";

		$products = array();
		$products_temp = !empty($field['value']) ? $field['value'] : array();

		if (!empty($products_temp))
		{
			foreach ($products_temp as $product_id => $product_name)
			{
				$products[] = array(
					'product_id' => $product_id,
					'name'       => $product_name
				);
			}
		}

		if( $this->oc_4 ) {
			if( version_compare($this->version, '4.0.2.0', '>=') ) {
						$html .= '<input name="'.$field['name'].'" '.(!empty($field['label']) ? 'placeholder="'.$field['label'].'" ':'').' id="input-'.$field['name'].'" class="form-control products_autocomplete" data-oc-target="autocomplete-'.$field['name'].'" autocomplete="off">';
						$html .= '<ul id="list-'.$field['name'].'" class="dropdown-menu"></ul>';
					} else {
						$html .= '<input name="'.$field['name'].'" '.(!empty($field['label']) ? 'placeholder="'.$field['label'].'" ':'').' id="input-'.$field['name'].'" class="form-control products_autocomplete" list="list-'.$field['name'].'">';
						$html .= '<datalist id="list-'.$field['name'].'"></datalist>';
					}
		} else {
			$html .= '<input name="'.$field['name'].'" '.(!empty($field['label']) ? 'placeholder="'.$field['label'].'" ':'').'class="form-control products_autocomplete">';
		}

		if( $this->oc_4 ) {
			$html .= '<div class="input-group"><div class="form-control p-0 products-autocomplete-table-container" style="height: 150px; overflow: auto;"><table id="'.$field['name'].'" class="table table-sm m-0"><tbody>';

			if( !empty($products) ) {
				foreach( $products as $product ) {
					$html .= '<tr id="element-'.$product['product_id'].'">';
					$html .= '<td>'.$product['name'].'<input type="hidden" name="'.$field['name'].'[]" value="'.$product['product_id'].'"/></td>';
					$html .= '<td class="text-end"><button type="button" class="btn btn-danger btn-sm"><i class="fas fa-minus-circle"></i></button></td>';
					$html .= '</tr>';
				}
			}

			$html .= '</tbody></table></div></div>';

			$html .= '<script type="text/javascript">';
			$html .= 'load_products_autocomplete_inputs();';
			$html .= '</script>';
		}
		elseif($this->oc_2)
		{
			$html .= '<div id="'.$field['name'].'" class="well well-sm" style="height: 150px; overflow: auto;">';
			if(!empty($products))
			{
				foreach ($products as $product)
				{
					$html .= '<div id="element-'.$product['product_id'].'"><i class="fa fa-minus-circle"></i> ';
					$html .= $product['name'];
					$html .= '<input type="hidden" name="'.$field['name'].'[]" value="'.$product['product_id'].'" />';
					$html .= '</div>';
				}
			}
			$html .= '</div>';
		}
		else
		{
			if(!$only_input) $html .= '</td></tr>';
			if(!$only_input) $html .= '<tr class="field_tr'.(!empty($field['store']) ? ' store_input '.$field['store']:'').'">';
			if(!$only_input) $html .= '<td></td>';
			if(!$only_input) $html .= '<td>';
			$html .= '<div id="'.$field['name'].'" class="scrollbox">';
			$class = 'odd';
			foreach ($products as $product) {
				$class = ($class == 'even' ? 'odd' : 'even');
				$html .= '<div id="element-'.$product['product_id'].'" class="'.$class.'">';
				$html .= $product['name'].'<img class="delete_item_autocomplete" src="view/image/delete.png" alt="" />';
				$html .= '<input type="hidden" name="'.$field['name'].'[]" value="'.$product['product_id'].'" />';
				$html .= '</div>';
			}
			$html .= '</div>';
			if(!$only_input) $html .= '</td>';
			if(!$only_input) $html .= '</tr>';
		}

		return $html;
	}

	//1.5.X FUNCTIONS
	public function field_generate_module_oc15x($field)
	{
		$html = '<table id="'.$field['name'].'-module" class="list">';
		$html .= '<thead>';
		$html .= '<tr>';
		foreach ($field['theads'] as $key => $value) {
			$html .= '<td class="left">'.$value.'</td>';
		}
		$html .= '<td></td>';
		$html .= '</tr>';
		$html .= '</thead>';


		//Get config modules
		//TODO $modules = $this->config->get($field['name']);

		$count_modules = 0;

		if(!empty($modules))
		{
			foreach ($modules as $mod_key => $mod) {
				$html .= '<tbody id="'.$field['name'].'-module-row-'.$mod_key.'">';
				$html .= '<tr>';
				//Set all values to empty & set names
				$temp_field = $field;
				foreach ($temp_field['inputs'] as $key => $fi) {
					if (empty($fi['multilanguage']))
					{
						$temp_field['inputs'][$key]['value'] = !empty($mod[$fi['name']])? $mod[$fi['name']]:'';
						$temp_field['inputs'][$key]['name'] = $temp_field['name'].'['.$mod_key.']['.$fi['name'].']';
					}
				}

				foreach ($temp_field['inputs'] as $key2 => $fie) {
					if (!empty($fie['multilanguage']))
					{
						$html .= '<td class="left">';
						foreach ($this->langs as $key_lng => $lng) {
							$temp_fi = $fie;
							$temp_fi['value'] = $mod[$temp_fi['name'].'_'.$lng['language_id']];
							$temp_fi['name'] = $temp_field['name'].'['.$mod_key.']['.$temp_fi['name'].'_'.$lng['language_id'].']';
							$temp_fi['only_input'] = true;
							$html .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_fi).'<div style="clear:both;"></div>';
						}
						$html .= '</td>';
					}
					else
					{
						$fie['only_input'] = true;
						$html .= '<td class="left">'.$this->generateField($fie).'</td>';
					}
				}

				$html .= '<td class="left"><a class="button" onclick="'.$this->jquery_symbol.'(\'#'.$field['name'].'-module-row-'.$mod_key.'\').remove();">'.$this->lang_text_remove.'</a></td>';
				$html .= '</tr>';
				$html .= '</tbody>';
				$count_modules++;
			}
		}
		else
		{
			//Set all values to empty & set names
			$temp_field = $field;
			foreach ($temp_field['inputs'] as $key => $fi) {
				$temp_field['inputs'][$key]['value'] = "";
				if (empty($fi['multilanguage']))
					$temp_field['inputs'][$key]['name'] = $temp_field['name'].'[0]['.$fi['name'].']';
			}

			//Insert empty row
			$html .= '<tbody id="'.$temp_field['name'].'-module-row-0">';
			$html .= '<tr>';
			foreach ($temp_field['inputs'] as $key2 => $fi) {
				if (!empty($fi['multilanguage']))
				{
					$html .= '<td class="left">';
					foreach ($this->langs as $key => $lng) {
						$temp_fi = $fi;
						$temp_fi['name'] = $temp_field['name'].'[0]['.$fi['name'].'_'.$lng['language_id'].']';
						$temp_fi['only_input'] = true;
						$html .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_fi).'<div style="clear:both;"></div>';
					}
					$fi['only_input'] = true;
					$this->generateField($fi);
					$html .= '</td>';

				}
				else
				{
					$fi['only_input'] = true;
					$html .= '<td class="left">'.$this->generateField($fi).'</td>';
				}
			}
			$html .= '<td></td>';
			$html .= '</tr>';
			$html .= '</tbody>';

			$count_modules++;
		}

		$html .= '<tfoot>';
		$html .= '<tr>';
		$html .= '<td colspan="'.count($field['inputs']).'"></td>';
		$html .= '<td class="left"><a class="button" onclick="addModule_'.$field['name'].'();">'.$this->lang_text_add_module.'</a></td>';
		$html .= '</tr>';
		$html .= '</tfoot>';
		$html .= '</table>';

		$html .= $this->field_generate_script_add_module_oc15x($field, $count_modules);

		return $html;
	}

	public function field_generate_script_add_module_oc15x($field, $count_modules)
	{
		$script = '<script type="text/javascript">';
		$script .= 'var module_row = '.$count_modules.';';

		$script .= 'function addModule_'.$field['name'].'(){';
		$script .= "html  = '<tbody id=\"".$field['name']."-module-row-' + module_row + '\">';";
		$script .= "html  += '<tr>';";
		//Set all values to empty & set names
		foreach ($field['inputs'] as $key => $fi) {
			$field['inputs'][$key]['value'] = "";

			if (empty($fi['multilanguage']))
				$field['inputs'][$key]['name'] = $field['name'].'[\'+module_row+\']['.$fi['name'].']';
		}

		foreach ($field['inputs'] as $key2 => $fi) {
			if (isset($fi['multilanguage']) && !empty($fi['multilanguage']))
			{
				$script .= "html  += '<td class=\"left\">";
				foreach ($this->langs as $key => $lng) {
					$temp_field = $fi;
					$temp_field['name'] = $field['name'].'[\'+module_row+\']['.$fi['name'].'_'.$lng['language_id'].']';
					$temp_field['only_input'] = true;
					$script .= '<img src="view/image/flags/'.$lng['image'].'">&nbsp;&nbsp;'.$this->generateField($temp_field).'<div style="clear:both;"></div>';
				}
				$script .= "</td>';";
			}
			else
			{
				$fi['only_input'] = true;
				$script .= "html  += '<td class=\"left\">".$this->generateField($fi)."</td>';";
			}
		}
		$script .= "html  += '<td class=\"left\"><a onclick=\"".$this->jquery_symbol."(\'#".$field['name']."-module-row-' + module_row + '\').remove();\" class=\"button\">".$this->lang_text_remove."</a></td>';";
		$script .= "html  += '</tr>';";
		$script .= "html  += '</tbody>';";
		$script .= $this->jquery_symbol."('#".$field['name']."-module tfoot').before(html);";
		$script .= "module_row++;";
		$script .= '}';
		$script .= '</script>';

		return $script;
	}
	//END 1.5.X FUNCTIONS
	//END

	//Devman Extensions - info@devmanextensions.com - 2017-08-29 19:22:55 - Another functions
	public function aasort ($array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		return $ret;
	}
	public function formatName($name)
	{
		$unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		return strtr( $name, $unwanted_array );
	}
	public function get_tab_changelog($id_extension)
	{
		$extension = $this->Extension->findById($id_extension);

		$changelogs = $this->Changelog->find('all', array('conditions' => array('Changelog.id_extension' => $id_extension), 'order' => array('Changelog.created' => 'DESC')));

		$html_changelogs = '';

		if(!$this->is_demo && !$this->is_trial)
		{
			$message = '<div class="alert alert-success"><i class="fa fa-check-circle"></i>'.$this->OpencartExtension->get_download_id_message($this->license_id).'</div>';
			$html_changelogs = $message;
		}

		if(!empty($changelogs))
		{
			$html_changelogs .= '<ul>';
			foreach ($changelogs as $key => $cl) {
				$lines = explode(PHP_EOL, $cl['Changelog']['text']);

				$html_changelogs .= '<li style="list-style:none; margin-bottom:5px;"><b><u>Version '.$cl['Changelog']['version'].' - '.date('d/m/Y', strtotime($cl['Changelog']['created'])).'</u></b>';
				$html_changelogs .= '<ul>';
				foreach ($lines as $key2 => $line) {
					$html_changelogs .= '<li>'.$line.'</li>';
				}
				$html_changelogs .= '</ul>';
				$html_changelogs .= '</li>';
			}
			$html_changelogs .= '</ul>';
		}
		else
			$html_changelogs .= 'No changelogs.';
		$icon = $this->retina_icons ? '<span class="retina-theessentials-2569"></span>' : '<i class="fa fa-file-text"></i>';
		return array(
			'no_multistore' => true,
			'form_colums' => 2,
			'icon' => $icon,
			'custom_content' => $html_changelogs
		);
	}

	public function get_tab_faq($faqs, $official_documentation_link = false)
	{
		if($this->jquery_symbol == '')
			$this->jquery_symbol = '$';
		$html_faqs = '';

		if(!$official_documentation_link) {
			foreach ($faqs as $key => $faq) {
				$html_faqs .= '<h2 class="faq_title" onclick="toggle_faq(' . $this->jquery_symbol . '(this))">' . ($key + 1) . '.- ' . $faq['Faq']['title'] . '</h2>';
				$html_faqs .= '<div class="faq_description">' . $faq['Faq']['description'] . '</div>';
			}
		} else
			$html_faqs = '<h2>Find all documentation in <a href="'.$official_documentation_link.'" target="_blank">this link</a></h2>';

		$html_faqs .= '
					<style type="text/css">
						h2.faq_title
						{
							position: relative;
						    float: left;
						    width: 100%;
						    border-bottom: 1px solid #ddd;
						    padding-bottom: 15px;
						    font-size: 17px;
						    font-weight: bold;
						    padding-top: 15px;
						    margin-bottom: 0px;
						}
						h2.faq_title:hover,
						h2.faq_title.openned
						{
							cursor: pointer;
							background: #eee;
						}
						h2.faq_title:before
						{
							font-family: "FontAwesome";
						    content: "\f150";
						    margin-right: 10px;
						    font-size: 11px;
						    top: -3px;
						    position: relative;
						    font-weight: normal;
						    margin-left: 10px;
						}
						h2.faq_title.openned:before
						{
							content: "\f151";
						}
						div.faq_description
						{
							display: none;
						    margin-bottom: 0px;
						    position: relative;
						    float: left;
						    width: 100%;
						    margin-top: 20px;
						    border-bottom: 1px solid #eee;
						    padding-bottom: 15px;
						    padding-left: 20px;
						    padding-right: 30px;
						    font-size: 14px;

						}
						h2.faq_title.openned + div.faq_description
						{
							display: block;
						}
					</style>
					<script type="text/javascript">
						function toggle_faq(heading_pressed)
						{
							heading_pressed.toggleClass(\'openned\');
						}
					</script>
	        	';
		$icon = $this->retina_icons ? '<span class="retina-theessentials-2619"></span>' : '<i class="fa fa-question-circle"></i>';
		return array(
			'no_multistore' => true,
			'form_colums' => 1,
			'icon' => $icon,
			'custom_content' => $html_faqs
		);
	}

	public function get_tab_help($expired = false, $email = '', $is_demo = false)
	{
		$icon = $this->retina_icons ? '<span class="retina-business-0386"></span>' : '<i class="fa fa-ticket"></i>';
		$tab_help = array(
			'icon' => $icon,
			'no_multistore' => true,
			'fields' => array(
				array(
					'type' => 'html_code',
					'html_code' => '<center><img src="https://devmanextensions.com/images/extensions/support_status.jpg?v='.date('YmdHms').'" alt="" class="img-responsive"></center><br>'
				),
				array(
					'label' => 'Your name:',
					'type' => 'text',
					'name' => 'name',
				),
				array(
					'label' => 'Your email:',
					'type' => 'text',
					'name' => 'email',
					'value' => $email
				),
				array(
					'label' => 'Subject:',
					'type' => 'text',
					'name' => 'subject'
				),
				array(
					'label' => 'Tell me your problem:',
					'placeholder' => 'Make easy! Give as much information as possible about your problem.',
					'type' => 'textarea',
					'name' => 'text',
					'style' => 'height: 150px;'
				),
				array(
					'label' => 'FTP and Admin:',
					'placeholder' => 'If to solve your problem, I will need connect to your shop, create me a temporary FTP account and temporary Admin Opencart user.',
					'type' => 'textarea',
					'name' => 'conections',
					'style' => 'height: 150px;'
				),
				/*array(
					'label' => 'Attached files:',
					'type' => 'file',
					'name' => 'attach[]',
					'multiple' => true
				),*/
			)
		);

		if(!$expired)
		{
			$button_send = array(
				'type' => 'button',
				'label' => 'Send ticket',
				'text' => '<i class="fa fa-ticket"></i> Send ticket',
				'onclick' => 'send_ticket();'
			);
		}
		else
		{
			$link_renew = $this->api_url.'invoices/opencart/new_invoice?type=renew_license&license_id='.$this->license_id;
			$button_send = array(
				'type' => 'button',
				'label' => 'Send ticket',
				'text' => '<i class="fa fa-exclamation-triangle"></i> License expired',
				'class' => 'disabled',
				'onclick' => 'javascript:{}',
				'after' => '<div style="clear: both"></div><a href="'.$link_renew.'" target="_new" style="font-weight: bold; color: #c72f1d; text-decoration: underline">Click here to renew your license with a 25% discount</a>'
			);
		}

		if($is_demo)
		{
			$button_send = array(
				'type' => 'button',
				'label' => 'Send ticket',
				'text' => '<i class="fa fa-exclamation-triangle"></i> Not available in demo',
				'class' => 'disabled',
				'onclick' => 'javascript:{}',
			);
		}

		$tab_help['fields'][] = $button_send;

		if($expired) {
			$tab_help['fields'][] = array(
				'type' => 'html_hard',
				'html_code' => ''
			);
		}

		return $tab_help;
	}
	//END
}
?>
