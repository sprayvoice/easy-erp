<?php

ini_set("display_errors", "On");

class db_stock_group {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function add_product_to_group($stock_group_id,$product_id){
        $mysql = "insert into " . constant("TABLE_PREFIX") . "product_stock_group_detail(`group_id`,`product_id`,`sort_order`) values($stock_group_id,$product_id,1000)";                
        $this->db->query($mysql);
    }
    
    public function add_group($ids, $name) {
        $mysql = "insert into " . constant("TABLE_PREFIX") . "product_stock_group(`group_name`) values('$name')";
        $result = $this->db->query($mysql);
        $mysql = "select @@IDENTITY as id";
        $result = $this->db->query($mysql);
        $row = $this->db ->fetch_assoc($result );
        $id= $row['id'];

        $array = explode(',', $ids);
        foreach ($array as $key => $value) {
            if ('' != ($value = trim($value))) {                
                $mysql = "insert into " . constant("TABLE_PREFIX") . "product_stock_group_detail(`group_id`,`product_id`,`sort_order`) values($id,$value,$value)";                
                $this->db->query($mysql);
            }
        }
    }

  public function del_group_detail($id){
        $mysql = "delete from " . constant("TABLE_PREFIX") . "product_stock_group_detail where id = $id";
        $this->db->query($mysql);
    }

    public function save_group($id,$name){
        $mysql = "update ". constant("TABLE_PREFIX") . "product_stock_group set group_name='$name' where group_id=$id";
        $this->db->query($mysql);
    }

    public function get_stock_group($id) {
        $mysql = "select * from ". constant("TABLE_PREFIX") . "product_stock_group where group_id=".$id;
        $result = $this->db->query($mysql);
        return $result;
    }

    public function list_stock_group() {
        $mysql = "select * from ". constant("TABLE_PREFIX") . "product_stock_group";
        $result = $this->db->query($mysql);
        return $result;
    }
    
    
    public function list_detail_by_group_id($group_id){
        $mysql = "select a.id,b.product_id,b.product_name,b.product_model,b.product_made,c.stock_quantity,c.stock_unit"
            ." from ". constant("TABLE_PREFIX") . "product_stock_group_detail a inner join "
            ."". constant("TABLE_PREFIX") . "product b on a.product_id = b.product_id"
            ." left join ". constant("TABLE_PREFIX") . "stock c on a.product_id = c.product_id"
            ." where a.group_id = ".$group_id." order by a.sort_order";
        $result = $this->db->query($mysql);
        return $result;        
    }
    
    public function update_sort_order($sort_array){
        $count = 1;
        $num = count($sort_array);
        for($i=0;$i<$num;$i++){
            $mysql = "update ". constant("TABLE_PREFIX") . "product_stock_group_detail set sort_order = ".$count." where id = ".$sort_array[$i];
            $this->db->query($mysql);
            $count++;
        }
    }
   
  
    
   
    
    
}
