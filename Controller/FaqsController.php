<?php 
	class FaqsController extends AppController {

		public  $uses = array(
      		'Extensions.Extension',
      		'Faq'
		); 

		public function beforeFilter() {
		    $this->vacation_time = false;
	        $this->Auth->allow('page');
	    }

	    public function page($extension_id)
	    {
	    	try {
	    		$this->layout = 'frontend';
                $faq = $this->Faq->find('all', array('conditions' => array('Faq.extension_id' => $extension_id, 'Faq.in_faq_page' => 1), 'order' => array('Faq.order')));
	    		if(empty($faq))
	    			throw new Exception('FAQ not found');
		    	$extension = $this->Extension->findById($extension_id);

		    	$this->set('faqs', $faq);
		    	$this->set('extension', $extension);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->Session->setFlash($message, 'default', array('class' => 'error'));
                $this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
            }
	    }
	}
?>