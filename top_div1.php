<?php 
//echo basename(__FILE__);

function IsActivePage($page_name){
    if(basename($_SERVER["PHP_SELF"])==$page_name){
        echo " class='active'";
    } else {
        //echo basename($_SERVER["PHP_SELF"]);
    }
}

?>

<div class="container-fluid" id="top_div1">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <a class="brand" href='javascript:void(0)' onclick='fill_tag("")'>进销存</a>
                        <ul class="nav">
                            <li<?php IsActivePage("product.php")?>>
                                <a href='product.php' target='_blank'>商品列表</a>	
                            </li>    
       						<li<?php IsActivePage("category.php")?>>
                                <a href='category.php' target='_blank'>商品分类</a>	
                            </li>      			
                            <li<?php IsActivePage("sales.php")?>>
                                <a href='sales.php' target='_blank'>新增销售</a>
                            </li>
                            <li<?php IsActivePage("instock.php")?>>
                                <a href='instock.php' target='_blank'>新增入库</a>
                            </li>
                            <li<?php IsActivePage("stock.php")?>>
                                <a href='stock.php' target='_blank'>库存列表</a>
                            </li>
                            <li<?php IsActivePage("big_product_stock.php")?>>
                                <a href='big_product_stock.php' target='_blank'>大件商品库存列表</a>
                            </li>
      						<li<?php IsActivePage("drug.php")?>>
                                <a href='drug.php' target='_blank'>易过期产品列表</a>
                            </li>
                            <li<?php IsActivePage("client_product_price.php")?>>
                                <a href='client_product_price.php' target='_blank'>客户商品价格表</a>
                            </li> 
                            <li<?php IsActivePage("company.php")?>>
                                <a href='company.php' target='_blank'>单位列表</a>
                            </li> 
     						<li<?php IsActivePage("art_cat.php")?>>
                                <a href='art_cat.php' target='_blank'>记事分类</a>
                            </li>
    						<li<?php IsActivePage("art.php")?>>
                                <a href='art.php' target='_blank'>记事</a>
                            </li>
                            <li<?php IsActivePage("tj1.php")?>>
                                <a href='tj1.php' target='_blank'>统计</a>
                            </li>
                            <li<?php IsActivePage("log.php")?>>
                                <a href='log.php' target='_blank'>日志</a>
                            </li>
                            <li>
                                <a href='javascript:void(0)' onclick="changepswd()">修改密码</a>
                            </li>
                            <li>
                                <a href='javascript:void(0)' onclick="logout()">注销</a>
                            </li>
                        </ul>
                    </div>
      <div style="position:inline; float: left;" id='global_warning_info' style='color:red;'>
    
    </div>