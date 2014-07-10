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
