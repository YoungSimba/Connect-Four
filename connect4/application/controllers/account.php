<?php

class Account extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    }
        
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	

    		$protected = array('updatePasswordForm','updatePassword','index','logout');
    		
    		if (in_array($method,$protected) && !isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }
          
    
    function loginForm() {
    		$this->load->view('account/loginForm');
    }
    
    function createNew() {
    		$this->load->library('form_validation');
    	    $this->form_validation->set_rules('name', 'Name', 'required|is_unique[user.first]');
	    		    
	    	if ($this->form_validation->run() == FALSE)
	    	{
	    		$this->load->view('account/loginForm');
	    	}
	    	else  
	    	{
	    		$user = new User();
	    		 
	    		$user->first = $this->input->post('name');
	    		$this->load->model('user_model');
	    		$user->login = $this->user_model->generateRandomString();
				$user->last = $this->user_model->generateRandomString();
				$clearPassword = $this->user_model->generateRandomString();
				$user->encryptPassword($clearPassword);
				$user->email = $this->user_model->generateRandomString() . "@gmail.com";  		
	    		$error = $this->user_model->insert($user);
	    		
	    		$_SESSION['user'] = $user;
    			$data['user']=$user;
	    		
	    		$this->load->view('arcade/homePage');
	    	}
    }    
 }

