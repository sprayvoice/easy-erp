<?php

class db_book {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 
 	
 	public function delete_by_id($id){
 		$sql = "delete from ".constant("TABLE_PREFIX")."book where book_id = $id";
		$this->db->query($sql);
 	}
 	
 	public function get_by_id($id){
 		$sql = "select * from ".constant("TABLE_PREFIX")."book where book_id=".$id;
     	 $result = $this->db->query($sql);
     	 return $result;
 	}
 	
 	public function insert_book($book_name,$page_num,$author,$add_date,$publisher){
 		$sql = "insert into ".constant("TABLE_PREFIX")."book(book_name,page_num,author,add_date,publisher) values('$book_name',$page_num,'$author','$add_date','$publisher')";
    		$this->db->query($sql);
 	}
 	
 	public function update_book($book_id,$book_name,$page_num,$author,$add_date,$publisher){
 		$sql = "update ".constant("TABLE_PREFIX")."book set book_name='$book_name',page_num=$page_num,author='$author',add_date='$add_date',publisher='$publisher' where book_id=$book_id";
    		$this->db->query($sql);
 	}
 	
 	public function count_book_by_name($book_name){
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."book a where a.book_name = '$book_name' ";
 	      $result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
 		$result = $this->db->fetch_assoc($result );
	      $c= $result['c'];
	      return $c;
 	}
 	
 	public function count_book($filter1,$from,$to){
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."book a where 1=1 ";
		if($filter1!=""){
			$sql = $sql . " and (a.book_name like '%$filter1%' or a.author like '%$filter1%' or a.publisher like '%$filter1%' )";
		}
                if($from!=""){
                    $sql = $sql . " and (a.add_date >= '".$from."')";
                }
                if($to!=""){
                    $sql = $sql . " and (a.add_date <= '".$to."') ";
                }
 	      $result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
 		$result = $this->db->fetch_assoc($result );
	      $c= $result['c'];
	      return $c;
 	}
        
        public function list_book_all($filter1,$from,$to){
            $sql = "select a.* from ".constant("TABLE_PREFIX")."book a where 1=1 ";
            if($filter1!=""){
                    $sql = $sql . " and (a.book_name like '%$filter1%' or a.author like '%$filter1%' or a.publisher like '%$filter1%' )";
            }
            if($from!=""){
                $sql = $sql . " and (a.add_date >= '".$from."')";
            }
            if($to!=""){
                $sql = $sql . " and (a.add_date <= '".$to."') ";
            }
            $sql = $sql . " order by a.add_date asc";
            $result = $this->db->query($sql);
            if($result==false){
		return mysql_error();
            }
            return $result;            
        }
 	public function list_book($page_id,$page_size,$filter1,$from,$to){
		if($page_id==""){
			$page_id = 1;
		}
		$limit = $page_size;
		$offset = ($page_id-1) * $page_size;
		$sql = "select a.* from ".constant("TABLE_PREFIX")."book a where 1=1 ";
		if($filter1!=""){
			$sql = $sql . " and (a.book_name like '%$filter1%' or a.author like '%$filter1%' or a.publisher like '%$filter1%' )";
		}
                if($from!=""){
                    $sql = $sql . " and (a.add_date >= '".$from."')";
                }
                if($to!=""){
                    $sql = $sql . " and (a.add_date <= '".$to."') ";
                }
		$sql = $sql . " order by a.add_date desc";
		$sql = $sql." limit ".$offset.",".$limit;

		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		return $result;
	 }
 
}
?>