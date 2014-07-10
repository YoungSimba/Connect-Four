-------------------------------------------------------
                      ARCADE.PHP (CONTROLLER)
-------------------------------------------------------

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


-------------------------------------------------------
                      ACCOUNT.PHP (CONTROLLER)
-------------------------------------------------------
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

-------------------------------------------------------
                      USER.PHP (MODEL, CLASS)
-------------------------------------------------------
<?php

class User  {

	public $id;
	public $login;
	public $first;
	public $last;
	public $password;   // hashed version
	public $salt;
	public $email;	
	public $user_status_id = User::OFFLINE;
	public $invite_id;
	public $match_id;
	
	public function encryptPassword($clearPassword) {
		$this->salt = mt_rand();
		$this->password = sha1($this->salt . $clearPassword);
	}
	
	
}

-------------------------------------------------------
                      USER_MODEL.PHP (MODEL)
-------------------------------------------------------

<?php
class User_model extends CI_Model {
	
	function get($username)
	{
		$this->db->where('login',$username);
		$query = $this->db->get('user');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	function getFromId($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('user');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	function insert($user) {
		return $this->db->insert('user',$user);
	}
	
	function generateRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	function getAvailable() {
		$query = $this->db->query("select * from user where invite_id is null");
		if ($query && $query->num_rows() > 0)
			return $query->result('User');
		else
			return null;
	}
	
	function partners($user1, $user2) {
		$this->db->where('id', $user1);
		return $this->db->update('user', array('invite_id'=>$user2));
	}
		
}
?>

-------------------------------------------------------
                      LOGINFORM.PHP (VIEW)
-------------------------------------------------------

<!DOCTYPE html>

<html>
<body>  
	<h1>New User</h1>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo form_open('account/createNew');
	echo form_label('Name'); 
	echo form_error('name');
	echo form_input('name',set_value('name'),"required");
	echo form_submit('submit', 'Login');
	echo form_close();
?>	
</body>

</html>

-------------------------------------------------------
                      HOMEPAGE.PHP (VIEW)
-------------------------------------------------------

<!DOCTYPE html>

<html>
<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>
		$(function(){
			$("#partners").everyTime(1000,function(){
				$("#partners").load('<?= base_url() ?>arcade/getAvailableUsers');
			});
		});
	</script>
</head>
<body>  
	<h1>Easy Pairing</h1>
	
<div id="partners">
</div>
</body>

</html>

-------------------------------------------------------
                      AVAILABLEUSERS.PHP (VIEW)
-------------------------------------------------------

<table>
<?php 

	$first_name = $currentUser->first;
	echo "<p>" . $first_name . ", please choose someone to partner with</p>";
	
	if ($users) {
		foreach ($users as $user) {
			if ($user->first != $currentUser->first) {
?>		
			<tr>
			<td> 
			<?= anchor("arcade/pairUp?login=" . $user->id,$user->first) ?> 
			</td>
			</tr>

<?php 	
			}
		}
	}
?>

</table>

-------------------------------------------------------
                      DATABASE.PHP (CONFIG)
-------------------------------------------------------
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'bungle08.cs.toronto.edu';
$db['default']['username'] = 'g2ayo';
$db['default']['password'] = '30790138';
$db['default']['database'] = 'g2ayo';  
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

-------------------------------------------------------
                      ROUTES.PHP (CONFIG)
-------------------------------------------------------

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "arcade";
$route['404_override'] = '';
?>

