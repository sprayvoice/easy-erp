<?php

class Pager{
    public function mypage($total, $pageId, $pageSize)
    {
        if ($total == 0)
            return "";
        if ($pageSize == 0)
            $pageSize = 10;
        if ($pageId <= 0)
            $pageId = 1;
        
        echo "共有<font color=red>". $total . "</font>条数据,每页<font color=red>" . $pageSize . "</font>条数据&nbsp;&nbsp;&nbsp;&nbsp;第";
        if($total % $pageSize==0){
        	$totalpage = (int)($total / $pageSize);
        } else {
        	$totalpage = (int)($total / $pageSize)+1;
        }
        
        if ($pageId * $pageSize > $total)
            $pageId = $totalpage;
        echo $pageId . "/" . $totalpage . "页&nbsp;&nbsp;&nbsp;&nbsp;";
        if ($pageId > 1)
        {
            echo "<a href='javascript:void(0)' onclick='go_page(1);return false;'" . ">首页</a>";
            echo "&nbsp;&nbsp;";            
            echo "<a href='javascript:void(0)' onclick='go_page(".($pageId-1).");return false;'". ">上一页</a>";
        }
        else
        {
            echo "首页";
            echo "&nbsp;&nbsp;";
            echo "上一页";
        }

        echo "&nbsp;&nbsp;";
        if ($pageId < $totalpage)
        {
            echo "<a href='javascript:void(0)' onclick='go_page(".($pageId+1).");return false;'". ">下一页</a>";
            echo "&nbsp;&nbsp;";
            echo "<a href='javascript:void(0)' onclick='go_page(".$totalpage.");return false;'". ">尾页</a>";
            
        }
        else
        {
            echo "下一页";
            echo "&nbsp;&nbsp;";
            echo "尾页";
        }

    }
}



?>