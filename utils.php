<?php

require_once(dirname(__FILE__).'/bean/exchange_relation.class.php');


function strsToArray($strs) {
    $result = array();
    $array = array();
    $strs = str_replace('，', ',', $strs);
    $strs = str_replace("n", ',', $strs);
    $strs = str_replace("rn", ',', $strs);
    $strs = str_replace(' ', ',', $strs);
    $array = explode(',', $strs);
    foreach ($array as $key => $value) {
        if ('' != ($value = trim($value))) {
            $result[] = $value;
        }
    }
    return $result;
}


function get_unit_i($in_unit,$unit_list,$convert_list){
	$out_unit = $in_unit;
	if(in_array($in_unit,$unit_list)){
		foreach($unit_list as $t){
			if(array_key_exists($t,$convert_list))
				return $t;
		}
	}
	return false;
}

// $in_unit 销售单位
// $out_unit 成本单位
// $in_quantity 销售数量
// $convertd_list  $result1 = $cls_product_unit->get_by_product_id($pro_id,$log_info);  $convertd_list = unit_q_2_array($result1);
// return $out_quantity 成本数量
function get_out_quantity($in_unit,$out_unit,$in_quantity,$convertd_list){
	if($in_unit==$out_unit)
		return $in_quantity;
	if(is_in_similar_unit($in_unit,$out_unit))
		return $in_quantity;
	else {
		$in_unit3 = pick_similar_unit($in_unit,$convertd_list);	
		$out_unit3 = pick_similar_unit($out_unit,$convertd_list);
		if($in_unit3!=''){
			$amount_converted = convert_unit($in_unit3,$in_quantity,$out_unit3,$convertd_list);
			return  $amount_converted;      
		}
	}	
	return 0;
}

function is_in_similar_unit($a_unit,$b_unit){
	$lx_list = array(
		array('kg','公斤','KG','千克'),
		array('斤','市斤'),
		array('张','块'),
		array('台','只','辆','卷','根','件','个'),
		array('套','付'),
		array('米','m','M')
		);
		foreach($lx_list as $lx1){
			if(in_array($a_unit,$lx1)
				&& in_array($b_unit,$lx1)
			)
			return true;			
		}
		return false;
}

function pick_similar_unit($in_unit,$convert_list){
	$lx_list = array(
		array('kg','公斤','KG'),
		array('斤','市斤'),
		array('张','块'),
		array('台','只','辆','卷','根','件','个'),
		array('套','付'),
		array('米','m','M')
		);
	foreach($lx_list as $lx1){
		$out_unit = get_unit_i($in_unit,$lx1,$convert_list);
		if($out_unit!=false)
			return $out_unit;
	}
	return '';
}


//return out_quantity
 function convert_unit($in_unit,$in_quantity,$out_unit,$convert_list){	
	if(array_key_exists($out_unit,$convert_list)
		&& array_key_exists($in_unit,$convert_list)
		){
		return 
		$convert_list[$out_unit] 
		* $in_quantity
		/ $convert_list[$in_unit];
	}
	return false;
}



function unit_q_2_array($unit_qu_array){
	$convert_list = array();
	if($unit_qu_array['unit1']!='' && $unit_qu_array['q1']!=''){
		$convert_list[$unit_qu_array['unit1']]=$unit_qu_array['q1'];
	}
	if($unit_qu_array['unit2']!='' && $unit_qu_array['q2']!=''){
		$convert_list[$unit_qu_array['unit2']]=$unit_qu_array['q2'];
	}
	if($unit_qu_array['unit3']!='' && $unit_qu_array['q3']!=''){
		$convert_list[$unit_qu_array['unit3']]=$unit_qu_array['q3'];
	}
	return $convert_list;
}


function get_now(){
    date_default_timezone_set("Asia/Shanghai");
    $d = date('Y-m-d H:i:s');
    return $d;
}

?>