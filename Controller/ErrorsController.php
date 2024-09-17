<?php
	class ErrorsController extends AppController {
	    public $name = 'Errors';

	    public function beforeFilter() {
	        parent::beforeFilter();
	        $this->Auth->allow('general', 'administration');
	    }

	    public function general() {
	        $this->layout = 'frontend';
	    }

	    public function administration() {
	        $this->layout = 'administration';
	    }
	}
?>