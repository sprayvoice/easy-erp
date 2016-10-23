<?php
	  require_once ( 'data/config.php'); 

class db_login {
	

	
	private $db;
 	public function __construct($db){
		 $this->db = $db;
 	}
 	
 	public function update_user_and_pwd($uid,$old_pass,$pwd){
 	
 		if($this->login($uid,$old_pass)==false){
 			return false;
 		}
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."admin";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $c= $result['c'];
		 if($c>0){
		 	$sql = "update ".constant("TABLE_PREFIX")."admin set user_id=AES_ENCRYPT('$uid','".constant("ADMINENCPWD")."'),user_pass=AES_ENCRYPT('$pwd','".constant("ADMINENCPWD")."')";
		 	 
		 } else {
		 	$sql = "insert into ".constant("TABLE_PREFIX")."admin(user_id,user_pass) values(AES_ENCRYPT('$uid','".constant("ADMINENCPWD")."'),AES_ENCRYPT('$pwd','".constant("ADMINENCPWD")."'))"; 
		 }
		 $this->db->query($sql);
		 return true;	 
	 }
	 
	 public function create_user_and_pwd($uid,$pwd){
		 $sql = "insert into ".constant("TABLE_PREFIX")."admin(user_id,user_pass) values(AES_ENCRYPT('$uid','".constant("ADMINENCPWD")."'),AES_ENCRYPT('$pwd','".constant("ADMINENCPWD")."'))"; 
		 $this->db->query($sql);
		 return true;	 
	 }
	 
	 public function login($uid,$pwd){
	 	 
		 $sql = "select count(*) c from ".constant("TABLE_PREFIX")."admin where  AES_DECRYPT(user_id,'".constant("ADMINENCPWD")."') = '$uid' and  AES_DECRYPT(user_pass,'".constant("ADMINENCPWD")."') = '$pwd' ";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $c= $result['c'];
		 if($c>0)
		 	 return true;
		 return false;
	 }

	 public function update_login_time_and_ip($uid,$ip){
		 $sql = "update ".constant("TABLE_PREFIX")."admin set last_login=now(),last_ip='$ip' where AES_DECRYPT(user_id,'".constant("ADMINENCPWD")."') = '$uid'" ;
		 $result = $this->db->query($sql);
		 return $result;
	 }

}
?>