<?php
	class ExtensionsController extends ExtensionsAppController 
	{
		public  $uses = array(
			'Extensions.Extension'
		); 

		/**
         * 
         * Función Index
         * 
         */
		function index()
		{
			$extensions = $this->Extension->find('all', array('order' => 'name'));
			$this->set('extensions', $extensions);

			$this->paginate = array(
				'limit' => 100,
				'order' => array(
					'Extension.name' => 'asc'
				)
			);

			$data = $this->paginate('Extension');
			$this->set("extensions", $data);

			$buttons = array(
				array(
					'type' => 'create'
				)
			);
			$this->set('buttons', $buttons);
		}

		function edit($id = null)
		{
		    $systems = array(
		        'opencart' => 'Opencart',
                'woocommerce' => 'Woocommerce',
                'prestashop' => 'Prestashop',
                 'magento' => 'Magento',
                 'cs-cart' => 'CS-Cart'
            );
		    $this->set('systems', $systems);

		    $types = array(
		        'analytics' => 'Analytics',
                'module' => 'Module',
                'template' => 'Template',
                'payment' => 'Payment',
                'shipping' => 'Shipping',
            );
		    $this->Extension->recursive = 1;
		    $this->set('types', $types);
			if ($id)
				$this->data = $this->Extension->findById($id);
			else
				$this->data = array(
					'Extension' => array(
						'id' => '',
						'name' => ''
					)
				);
		}


		function delete($id) {
			$this->Extension->id = $id;
			$extension_selected = $this->Extension->read();
			if (empty($extension_selected['Extension']) || !isset($extension_selected['Extension'])) {
				$this->Session->setFlash(
				    '<i class="fa fa-thumbs-down"></i>Extension not found',
				    'default',
				    array('class' => 'error')
				);
			} else {
				$extension_selected['Extension']['deleted'] = 1;

				if ($this->Extension->save($extension_selected))
					$this->Session->setFlash(
					    '<i class="fa fa-thumbs-up"></i>Extension deleted successfully!',
					    'default'
					);
				else
					$this->Session->setFlash(
					    '<i class="fa fa-thumbs-down"></i>Error deleting extension',
					    'default',
					    array('class' => 'error')
					);
			}
			$this->redirect(array('action'=>'index'));
		}
		

		function save()
		{
		    $folder = $this->Extension->formatName($this->request->data['Extension']['title_main']);
		    $system = $this->request->data['Extension']['system'];
		    $path_shop = WWW_ROOT.'img/pages/shop/'.$system.'/'.$folder;

		    if(!file_exists($path_shop))
		        mkdir($path_shop);

		    if(!empty($folder) && !empty($_FILES['data']['name']['Extension']['logo']) && !empty($_FILES['data']['tmp_name']['Extension']['logo'])) {
                $path_logo = $path_shop.'/logo.png';
                move_uploaded_file($_FILES['data']['tmp_name']['Extension']['logo'], $path_logo);
            }
            if(!empty($folder) && !empty($_FILES['data']['name']['Extension']['banner']) && !empty($_FILES['data']['tmp_name']['Extension']['banner'])) {
                $path_logo = $path_shop.'/main_banner.jpg';
                move_uploaded_file($_FILES['data']['tmp_name']['Extension']['banner'], $path_logo);
            }

            unset($this->request->data['Extension']['logo']);
		    unset($this->request->data['Extension']['banner']);

            for ($i = 0; $i < 20; $i++) {
                $image = $this->request->data['ExtensionFeature'][$i]['image'];

                if(is_array($image) && array_key_exists('name', $image) && !empty($image['name']) && array_key_exists('tmp_name', $image) && !empty($image['tmp_name'])) {
                    $path_features = $path_shop.'/features';
                    $image_name = $image['name'];
                    if(!file_exists($path_features))
		                mkdir($path_features);
                    $path_feature = $path_features.'/'.$image_name;
                    move_uploaded_file($image['tmp_name'], $path_feature);
                    $this->request->data['ExtensionFeature'][$i]['image'] = $image_name;
                } else {
                    unset($this->request->data['ExtensionFeature'][$i]['image']);
                }

                if(empty($this->request->data['ExtensionFeature'][$i]['title']))
                    unset($this->request->data['ExtensionFeature'][$i]);
            }
			$this->Extension->saveAll($this->request->data);

			$this->Session->setFlash(
					    '<i class="fa fa-thumbs-up"></i>Extension saved successfully!',
					    'default'
			);
			$this->redirect(array('action'=>'index'));
		}

	}
?>