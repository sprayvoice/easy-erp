<?php



require_once ( 'mysqli.class.php');



class db_log {

    

  



    private $db;

     

    

     public function __construct() { 

         require(dirname(__FILE__).'/../data/config.php');

          

          $this->db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");

    }

    

    public function get_batch_id(){

        $sql = "insert into " . constant("TABLE_PREFIX")."log_batch() values() ";

        $this->db->query($sql);

        $sql = "select max(log_batch_id) m from ". constant("TABLE_PREFIX")."log_batch";

        $result = $this->db->query($sql);

        if ($result == false) {

            return $this->db->mysql_error();

        }

        $row = $this->db->fetch_assoc($result);

        if ($row != null) {

            $c = $row["m"];

            return $c;

        } else {

            return 0;

        }        

    }



    public function insert_log($log_batch_id, $page_name, $action_name, $sql_text, $sql_type, $execute_result, $add_user) {

        $sql_text = addslashes($sql_text);

        $sql = "insert into " . constant("TABLE_PREFIX")

                . "log(log_batch_id,page_name,action_name,sql_text,sql_type,"

                . "execute_result,add_date,add_user) values($log_batch_id,'$page_name','$action_name','$sql_text','$sql_type',"

                . "'$execute_result',now(),'$add_user')";

        $result = $this->db->query($sql);        

        if ($result == false) {

            return false;

        }

        $sql = "select @@IDENTITY as id";

        $result = $this->db->query($sql);

        $row = $this->db ->fetch_assoc($result );		 

        $id= $row['id'];

        return $id;

    }

    

    

    

    public function update_log_result($log_id,$execute_result){

        $sql = "update ".constant("TABLE_PREFIX")."log set execute_result='$execute_result' where log_id=$log_id";

        $result1 = $this->db->query($sql);        

        if ($result1 == false) {

            return false;

        }

        return "success";

    }

    

 



  

}



?>