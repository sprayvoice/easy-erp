<?php
	  require_once ( 'data/config.php'); 

class db_admin_login {
	

	
	private $db;
 	public function __construct($db){
		 $this->db = $db;
 	}
 	
 	public function insert_or_update($ip){ 	
 		$time1 = date("YmdGi");
		$sql = "delete from ".constant("TABLE_PREFIX")."admin_login where time_for_login < '$time1'";
		$this->db->query($sql);

 		$sql = "select try_time c from ".constant("TABLE_PREFIX")."admin_login where ip='$ip' and time_for_login='$time1' ";
		$row = $this->db->query($sql);
		if(mysql_num_rows($row)>0){
			$result = $this->db ->fetch_assoc($row);
		    $c= $result['c'];
		 	if($c>3){
			 	return "登陆过于频繁，请稍后再试！";
		 	} else if($c>0){			 
		 	 	$sql = "update ".constant("TABLE_PREFIX")."admin_login set try_time=try_time+1 where ip='$ip' and time_for_login='$time1' ";
				$this->db->query($sql);
				return "success";
		 	} 		 	 
		} else {
			$sql = "insert into ".constant("TABLE_PREFIX")."admin_login(ip,time_for_login,try_time) values('$ip','$time1',1)";
			$this->db->query($sql);
			return "success";
		}		
	 }
	 
	

}
?>