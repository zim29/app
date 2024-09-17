<h1>Edit extension</h1>
<?php 
	echo $this->Form->create('Extension', array('action' => 'save', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal'));
    	$inputs = array(
    	    $this->Form->input('system', array('label' => 'System', 'type' => 'select', 'options' => array($systems), 'value' => !empty($this->request->data['Extension']['system']) ? $this->request->data['Extension']['system'] : '')).$this->Form->input('id', array('type' => 'hidden')),
    	    $this->Form->input('name', array('label' => 'Name')),
            $this->Form->input('logo', array('label' => 'Logo (.png)', 'type' => 'file')),
            $this->Form->input('banner', array('label' => 'Banner (.jpg)', 'type' => 'file')),
            $this->Form->input('oc_extension_id', array('type' => 'text', 'label' => 'OC ext.id')),
            $this->Form->input('oc_support_months', array('type' => 'text', 'label' => 'OC Support', 'default' => 3)),
            $this->Form->input('price', array('type' => 'text', 'label' => 'Price')),
            $this->Form->input('in_shop', array('type' => 'checkbox', 'label' => 'In shop')),
            $this->Form->input('demo_frontend', array('type' => 'text', 'label' => 'Demo front')),
            $this->Form->input('demo_backend', array('type' => 'text', 'label' => 'Demo back')),
            $this->Form->input('type', array('label' => 'Type', 'type' => 'select', 'options' => array($types), 'value' => !empty($this->request->data['Extension']['type']) ? $this->request->data['Extension']['type'] : '')),
            $this->Form->input('title_main', array('type' => 'text', 'label' => 'Main title')),
            $this->Form->input('title_sub', array('type' => 'text', 'label' => 'Subtitle')),
            $this->Form->input('meta_title', array('type' => 'text', 'label' => 'Meta title')),
            $this->Form->input('meta_description', array('type' => 'text', 'label' => 'Meta description')),
            $this->Form->input('meta_keywords', array('type' => 'text', 'label' => 'Meta keywords')),
            $this->Form->input('seo_url', array('type' => 'text', 'label' => 'SEO URL')),
            $this->Form->input('description', array('type' => 'textarea', 'label' => 'Description (hover orange)')),
            $this->Form->input('additional_info', array('type' => 'textarea', 'label' => 'Additional info')),
            $this->Form->input('features', array('type' => 'textarea', 'label' => 'List features')),
            $this->Form->input('zip_name', array('type' => 'text', 'label' => 'Zip name')),
            $this->Form->input('order', array('type' => 'text', 'label' => 'Short order')),
        );
        $this->FormTool->fieldset(
    		array(
    			'inputs' => $inputs
    		)
        );


    	for ($i = 0; $i < 20; $i++) {
    	    echo '<h2>Feature '.($i+1).'</h2>';
    	    $inputs = array(
                $this->Form->input('ExtensionFeature.'.$i.'.title', array('label' => 'Title'))
                .$this->Form->input('ExtensionFeature.'.$i.'.id', array('type' => 'hidden', 'value' => !empty($this->request->data['ExtensionFeature'][$i]['id']) ? $this->request->data['ExtensionFeature'][$i]['id'] : ''))
                .$this->Form->input('ExtensionFeature.'.$i.'.extension_id', array('type' => 'hidden', 'value' => $this->request->data['Extension']['id'])),
                $this->Form->input('ExtensionFeature.'.$i.'.description', array('label' => 'Description', 'type' => 'textarea', 'value' => !empty($this->request->data['ExtensionFeature'][$i]['description']) ? $this->request->data['ExtensionFeature'][$i]['description'] : '')),
                $this->Form->input('ExtensionFeature.'.$i.'.sort_order', array('label' => 'Sort order', 'type' => 'text','default' => 0, 'value' => !empty($this->request->data['ExtensionFeature'][$i]['sort_order']) ? $this->request->data['ExtensionFeature'][$i]['sort_order'] : '')),
                $this->Form->input('ExtensionFeature.'.$i.'.image', array('label' => 'Image (.jpg)', 'type' => 'file', 'value' => !empty($this->request->data['ExtensionFeature'][$i]['image']) ? $this->request->data['ExtensionFeature'][$i]['image'] : '')),
            );
            $this->FormTool->fieldset(
                array(
                    'inputs' => $inputs
                )
            );
        }



    $this->FormTool->button('Save', 'save');
?>