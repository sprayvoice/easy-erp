<?php

class db_client {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	public function insert_client(
		 $client_company,$client_addr,$tax_no,$bank_name,$client_phone,$remark){
		  $sql = "insert into ".constant("TABLE_PREFIX")."client(client_company,client_addr,tax_no,bank_name,client_phone,remark,add_time) "
		 	." values('$client_company','$client_addr','$tax_no','$bank_name','$client_phone','$remark',now())";
		  $result = $this->db->query($sql);
		  if($result==false){
			  echo mysql_error();
			  return 0;
		  }
		  $sql = "select @@IDENTITY as id";
		  $result = $this->db->query($sql);
		  $result = $this->db ->fetch_assoc($result );
	      $id= $result['id'];
	      return $id;	 
	 }

	 public function update_client(
		 $client_no,$client_company,$client_addr,$tax_no,$bank_name,$client_phone,$remark){
		 $sql = "update ".constant("TABLE_PREFIX")."client set client_company='$client_company',client_addr='$client_addr',tax_no='$tax_no',"
		 ."bank_name='$bank_name',client_phone='$client_phone',remark='$remark' where client_no=$client_no";
		 $result = $this->db->query($sql);
		 return $result;
	 }

 	public function get_client_by_id($client_no){
 		$sql = "select * from ".constant("TABLE_PREFIX")."client where client_no = '$client_no'";
		$result = $this->db->query($sql);
		return $result;
 	}

 	 public function get_client_company(){
		  $sql = "select client_company from ".constant("TABLE_PREFIX")."client";
		  $result = $this->db->query($sql);
		  return $result;
	  }

	  public function check_exist_company_exact($company_name){
		  $sql = "select client_no from ".constant("TABLE_PREFIX")."client where client_company = '$company_name'";
		  $result = $this->db->query($sql);
		  $result = $this->db ->fetch_assoc($result);
		  $client_no= $result['client_no'];		
		  return $client_no;
	  }
 
	  public function check_exist_company($company_name){
		  $sql = "select client_company from ".constant("TABLE_PREFIX")."client where client_company like '%$company_name%'";
		  $result = $this->db->query($sql);
		  return $result;
	  }

}
?>