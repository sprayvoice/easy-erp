
<?php
//用途: 金额小写转大写
//范围: 万亿>= (-,-) >=分
//作者: 283879541
/*
example:
$test = new digit2chinese;
$test->num = '5009999999.12';
$test->chuli();
$test->huey_print();
*/
class digit2chinese
{
var $num; //金额小写
private $d = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
private $e = array('圆','拾','佰','仟','万','拾万','百万','千万','亿','拾亿','佰亿','仟亿','万亿');
private $p = array('分','角');
private $zheng=''; //追加"整"字
private $final = array(); //结果

public function chuli()
{
$inwan=0; //是否有万
$inyi=0; //是否有亿
$len_pointdigit=0; //小数点后长度
$y=0;

if($c = strpos($this->num, '.')) 
{ //有小数点
$len_pointdigit = strlen($this->num)-strpos($this->num, '.')-1;
if($c>13) //简单的错误处理
{
echo "数额太大,已经超出万亿.";
die();
}
elseif($len_pointdigit>2)
{
echo "小数点后只支持2位.";
die();
}
}
else //无小数点
{
$c = strlen($this->num);
$this->zheng = '整';
}

for($i=0;$i<$c;$i++) //处理整数部分
{
$bit_num = substr($this->num, $i, 1); //逐字读取 左->右
if($bit_num!=0 || substr($this->num, $i+1, 1)!=0) //当前是零 下一位还是零的话 就不显示
@$low2chinses = $low2chinses.$this->d[$bit_num];
if($bit_num || $i==$c-1) 
@$low2chinses = $low2chinses.$this->e[$c-$i-1];
} 
for($j=$len_pointdigit; $j>=1; $j--) //处理小数部分
{
$this->point_num = substr($this->num, strlen($this->num)-$j, 1); //逐字读取 左->右
if($this->point_num != 0)
@$low2chinses = $low2chinses.$this->d[$this->point_num].$this->p[$j-1];
if(substr($this->num, strlen($this->num)-2, 1)==0 && substr($this->num, strlen($this->num)-1, 1)==0)
$this->zheng = '整';
}


$chinses = str_split($low2chinses,2); //字符串转换成数组


for($x=sizeof($chinses)-1;$x>=0;$x--) //过滤无效的信息
{
if($inwan==0&&$chinses[$x]==$this->e[4]) //过滤重复的"万"
{
$this->final[$y++] = $chinses[$x];
$inwan=1;
}
if($inyi==0&&$chinses[$x]==$this->e[8]) //过滤重复的"亿"
{
$this->final[$y++] = $chinses[$x];
$inyi=1;
$inwan=0;
}
if($chinses[$x]!=$this->e[4]&&$chinses[$x]!=$this->e[8])
$this->final[$y++] = $chinses[$x];
}



} 

public function huey_print()
{
for($y=sizeof($this->final)-1; $y>=0; $y--) //打印出结果
{
echo $this->final[$y];
} 
echo $this->zheng; 
}
} 

/*
 * $test = new digit2chinese;
$test->num = '3305';
$test->chuli();
$test->huey_print();
 * 
 */
?>
