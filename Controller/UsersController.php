<?php 
	class UsersController extends AppController {
 		var $layout = 'administration';
	    public $paginate = array(
	        'limit' => 25,
	        'conditions' => array('status' => '1'),
	        'order' => array('User.username' => 'asc' )
	    );
	     
	    public function beforeFilter() {
	        parent::beforeFilter();
	        $this->Auth->allow('login');
	    }
	     
	 
	 
	    public function login() {
	         
	        //if already logged-in, redirect
	        if($this->Session->check('Auth.User')){
	            $this->redirect(array('action' => 'index'));     
	        }
	         
	        // if we get the post information, try to authenticate
	        if ($this->request->is('post')) {
	            if ($this->Auth->login()) {
	                //$this->Session->setFlash(__('Welcome, '. $this->Auth->user('username')));

                    $user = $this->Session->read('Auth.User');
                    $role = $user['role'];
                    $url = null;
                    if($role == 'marketing') {
                        $url = array(
                            'controller' => 'invoices',
                            'action' => 'invoices'
                        );
                    }

	                $this->redirect($this->Auth->redirectUrl($url));
	            } else {
	            	$this->Session->setFlash(
					    'Invalid username or password',
					    'default',
					    array('class' => 'error')
					);
	            }
	        }
	    }
	 
	    public function logout() {
	        $this->redirect($this->Auth->logout());
	    }
	 
	    public function index() {
	        $this->paginate = array(
	            'limit' => 6,
	            'order' => array('User.username' => 'asc' )
	        );
	        $users = $this->paginate('User');
	        $this->set(compact('users'));
	    }
	 
	 
	    public function add() {
	        if ($this->request->is('post')) {
	                 
	            $this->User->create();
	            if ($this->User->save($this->request->data)) {
	                $this->Session->setFlash(__('The user has been created'));
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash(__('The user could not be created. Please, try again.'));
	            }  
	        }
	    }
	 
	    public function edit($id = null) {
	 
	            if (!$id) {
	                $this->Session->setFlash('Please provide a user id');
	                $this->redirect(array('action'=>'index'));
	            }
	 
	            $user = $this->User->findById($id);
	            if (!$user) {
	                $this->Session->setFlash('Invalid User ID Provided');
	                $this->redirect(array('action'=>'index'));
	            }
	 
	            if ($this->request->is('post') || $this->request->is('put')) {
	                $this->User->id = $id;
	                if ($this->User->save($this->request->data)) {
	                    $this->Session->setFlash(__('The user has been updated'));
	                    $this->redirect(array('action' => 'edit', $id));
	                }else{
	                    $errors = $this->User->validationErrors;
	                    $error_message = '';
	                    foreach ($errors as $field_name => $error) {
                            $error_message .= '<b>'.$field_name.'</b>: '.implode(', ', $error).'<br>';
	                    }
	                    $this->Session->setFlash($error_message);
	                    //$this->Session->setFlash(__('Unable to update your user.'));
	                }
	            }
	 
	            if (!$this->request->data) {
	                $this->request->data = $user;
	            }
	    }
	 
	    public function delete($id = null) {
	         
	        if (!$id) {
	            $this->Session->setFlash('Please provide a user id');
	            $this->redirect(array('action'=>'index'));
	        }
	         
	        $this->User->id = $id;
	        if (!$this->User->exists()) {
	            $this->Session->setFlash('Invalid user id provided');
	            $this->redirect(array('action'=>'index'));
	        }
	        if ($this->User->saveField('status', 0)) {
	            $this->Session->setFlash(__('User deleted'));
	            $this->redirect(array('action' => 'index'));
	        }
	        $this->Session->setFlash(__('User was not deleted'));
	        $this->redirect(array('action' => 'index'));
	    }
	     
	    public function activate($id = null) {
	         
	        if (!$id) {
	            $this->Session->setFlash('Please provide a user id');
	            $this->redirect(array('action'=>'index'));
	        }
	         
	        $this->User->id = $id;
	        if (!$this->User->exists()) {
	            $this->Session->setFlash('Invalid user id provided');
	            $this->redirect(array('action'=>'index'));
	        }
	        if ($this->User->saveField('status', 1)) {
	            $this->Session->setFlash(__('User re-activated'));
	            $this->redirect(array('action' => 'index'));
	        }
	        $this->Session->setFlash(__('User was not re-activated'));
	        $this->redirect(array('action' => 'index'));
	    }
	 
	}
?>