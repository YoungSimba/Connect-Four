<?php

class Arcade extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    }
        
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	
    		
    		if (!isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }
       
    
    function index() {
		    	$data['user']=$_SESSION['user'];
		    	if (isset($_SESSION['errmsg'])) {
		    		$data['errmsg']=	$_SESSION['errmsg'];
		    		unset($_SESSION['errmsg']);
		    	}
		    	$this->load->view('arcade/homePage',$data);
    }

    function getAvailableUsers() {
 	   	$this->load->model('user_model');
    		$users = $this->user_model->getAvailable();
    		$data['users']=$users;
    		$data['currentUser']=$_SESSION['user'];
    		$this->load->view('arcade/availableUsers',$data);
    }
    
    function pairUp() {
		$user2ID = $this->input->get('login');
		$user1ID = $_SESSION['user']->login;
		$this->load->model('user_model');
		$user1ID = $this->user_model->get($user1ID);
		$user2ID = $this->user_model->getFromId($user2ID);
		
		$tt = $this->user_model->partners($user1ID->id, $user2ID->id);
		$gg = $this->user_model->partners($user2ID->id, $user1ID->id);
		
		$this->load->view('arcade/homePage');
	} 
 }

