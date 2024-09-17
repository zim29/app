<?php
	class ChangelogsController extends ChangelogsAppController 
	{
		public  $uses = array(
			'Changelogs.Changelog',
			'Extensions.Extension'
		); 

		public function beforeFilter() {
	        $this->Auth->allow('create','get_changelogs');
	    }
		/**
         * 
         * Función Index
         * 
         */
		function index()
		{
			//Load extensions
			$extensions = $this->Extension->find('all', array('order' => array('Extension.name ASC')));
			
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
		    $this->Extension->recursive = 1;
			$extension = $this->Extension->findById($id);
			
  	     	$this->set('extension', $extension);
		}


		function delete($id) {
			$this->Changelog->id = $id;
			$changelog_selected = $this->Changelog->read();
			if (empty($changelog_selected['Changelog']) || !isset($changelog_selected['Changelog'])) {
				$this->Session->setFlash(
				    '<i class="fa fa-thumbs-down"></i>Changelog not found',
				    'default',
				    array('class' => 'error')
				);
			} else {
				$changelog_selected['Changelog']['deleted'] = 1;

				if ($this->Changelog->save($changelog_selected))
					$this->Session->setFlash(
					    '<i class="fa fa-thumbs-up"></i>Changelog deleted successfully!',
					    'default'
					);
				else
					$this->Session->setFlash(
					    '<i class="fa fa-thumbs-down"></i>Error deleting changelog',
					    'default',
					    array('class' => 'error')
					);
			}
			$this->redirect(array('action'=>'index'));
		}
		

		function save()
		{
			$this->Changelog->saveAll($this->request->data);

			$this->Session->setFlash(
					    '<i class="fa fa-thumbs-up"></i>Changelog saved successfully!',
					    'default'
			);
			$this->redirect(array('action'=>'index'));
		}

		function get_changelogs($id_extension = null)
		{
			$this->layout = 'html';

			$changelogs = $this->Changelog->find('all', array('conditions' => array('Changelog.id_extension' => $id_extension), 'order' => array('Changelog.version' => 'DESC')));
			$this->set('changelogs', $changelogs);
		}
	}
?>