<html>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- 引入 Bootstrap -->
     <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<title>ParaTrans MTCompare</title>
<body>
<div class="container-fluid">
<div class="row">
	<div class="col-md-12"></div>
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-2">
				</div>
				<div class="col-md-2">
				</div>
				<div class="col-md-2">
				</div>
				<div class="col-md-3">
				</div>
				<div class="col-md-1">

					 
		
				</div>
				<div class="col-md-1">
					
				</div>
			</div>
			<div class="jumbotron">
				<h2>
				ParaTrans MTCompare
				</h2>
				<p>
				上传一个文档，同时获得四个机器翻译结果。
				</p>
				<p>
					<a class="btn btn-primary btn-large" href="index.php">返回</a>
				</p>				
				
			</div>
		</div>
	</div>




<?php

	include_once "transapis.php";//引入百度、有道、搜狗API
	require('tencent/include.php'); //引入腾讯AI LabAPI
	
	if($_POST["language"] == 1)
	{
		$source = "zh-CHS";
		$yuanwen = "zh";
		$target = "en";
		$yiwen = "en";
	}
	else
	{
		$source = "en";
		$yuanwen = "en";
		$target = "zh-CHS";
		$yiwen = "zh";
		
	}
echo '<blockquote class="blockquote">';
if ($_FILES["file"]["error"] > 0)
  {
  echo "上传错误: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {
  echo "上传文件名称: " . $_FILES["file"]["name"] . "<br />";
  echo "上传文件类型: " . $_FILES["file"]["type"] . "<br />";
  echo "上传文件大小: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
  echo "文件临时地址: " . $_FILES["file"]["tmp_name"];
  }
  
//if (file_exists("upload/" . $_FILES["file"]["name"]))
//   {
//       //echo $_FILES["file"]["name"] . " 文件已经存在。 "."<br>";
//   }
//   else
//   {
//       // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
//       move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" .$_FILES["file"]["name"]);
//       echo "文件临时存储在: " . "upload/" . $_FILES["file"]["name"]."<br>";
//   }
echo '</blockquote>';

$url = "http://api.tmxmall.com/v1/http/parseFile";

$data = array(
	"file" => curl_file_create($_FILES["file"]["tmp_name"],$_FILES["file"]["type"],$_FILES["file"]["name"]),
	"user_name" =>"******",  //你的tmxmall账号
	"client_id" =>"******", //你的tmxmall文档解析API
	"to" => "en-US",
	"from" => "zh-CN",
	"de" =>""
);

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);

curl_setopt($curl, CURLOPT_POST, 1);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$output = curl_exec($curl);

curl_close($curl);



$array = json_decode($output,true);

echo '<table class="table table-striped">
		<tr>
			<th>原文</th>
			<th>百度翻译</th>
			<th>有道翻译</th>
			<th>搜狗翻译</th>
			<th>腾讯AI Lab</th>
		</tr>
';

		$num = 0;
		$percent_sum = array();
		$percent_youdao_sum = array();
		$percent_sougou_sum = array();
	
foreach($array["segments"] as $segment)
{
	foreach($segment["srcSegmentAtoms"] as $Atom)
	{
		if($num < 10)
		{
		if($Atom["textStyle"]!= "tag")
		{
			$query = $Atom["data"];
			$Baidu = new paraTrans(baidu_APP_ID,baidu_SEC_KEY,baidu_URL,"appid",array());
			$baidu_output = $Baidu->translate($query, $yuanwen, $yiwen);
			$baidu_reverse_output=$Baidu->translate($baidu_output["trans_result"][0]["dst"],$yiwen,$yuanwen);
			similar_text($query,$baidu_reverse_output["trans_result"][0]["dst"],$percent);
			
			
			$Youdao = new paraTrans(youdao_APP_ID,youdao_SEC_KEY,youdao_URL,"appKey",array());
			$youdao_output = $Youdao->translate($query, $source, $target);
			$youdao_reverse_output=$Youdao->translate($youdao_output["translation"][0],$target,$source);
			similar_text($query,$youdao_reverse_output["translation"][0],$percent_youdao);
			
			
			$Sogou = new paraTrans(sogou_APP_ID,sogou_SEC_KEY,sogou_URL,"pid",array("Content-Type:application/x-www-form-urlencoded","Accept:application/json"));
			$sogou_output = $Sogou->translate($query, $source, $target);
			$sogou_reverse_output=$Sogou->translate($sogou_output["translation"],$target,$source);
			similar_text($query,$sogou_reverse_output["translation"],$percent_sogou);
			
			
			//引入腾讯AI Lab机器翻译
			
			//设置AppID与AppKey
			Configer::setAppInfo(tencentai_APP_ID, tencentai_SEC_KEY);
			
			if($_POST["language"] == 1) 
			{
				$pair = 1;
				$reverse = 0;
			}
			else{
				$pair = 0;
				$reverse = 1;
			}
			$params = array(
				'app_id'     => tencentai_APP_ID,
				'type'       => $pair,
				'text'       => $query,
				'time_stamp' => strval(time()),
				'nonce_str'  => strval(rand()),
				'sign'       => '',
			);
			
			// 执行API调用
			$response = API::texttrans($params);
			$tencent_array = json_decode($response,true);
			$tencent_output = $tencent_array["data"]["trans_text"];
			
			$param = array(
				'app_id'     => tencentai_APP_ID,
				'type'       => $reverse,
				'text'       => $tencent_output,
				'time_stamp' => strval(time()),
				'nonce_str'  => strval(rand()),
				'sign'       => '',
			);
			
			$respons = API::texttrans($param);
			$tencent_re_array = json_decode($respons,true);
			$tencent_re_output = $tencent_re_array["data"]["trans_text"];	
			similar_text($query,$tencent_re_output,$percent_tencent);
			
			echo '<tr><td>'.$query.'</td>';
			echo '<td>'.$baidu_output["trans_result"][0]["dst"].'['.round($percent).'%]</td>';
			echo '<td>'.$youdao_output["translation"][0].'['.round($percent_youdao).'%]</td>';
			echo '<td>'.$sogou_output["translation"].'['.round($percent_sogou).'%]</td>';
			echo '<td>'.$tencent_output.'['.round($percent_tencent).'%]</td>';
			
			$num++;
			$percent_sum[] = $percent;
			$percent_youdao_sum[] = $percent_youdao;
			$percent_sogou_sum[] = $percent_sogou;
			$percent_tencent_sum[] = $percent_tencent;
		}
		}
		

	}
}


$baidu_average = array_sum($percent_sum)/$num;
$sogou_average = array_sum($percent_sogou_sum)/$num;
$youdao_average = array_sum($percent_youdao_sum)/$num;
$tencent_average = array_sum($percent_tencent_sum)/$num;

echo '<tr><td>平均回译率</td><td>'.round($baidu_average).'%<td>'.round($youdao_average).'%</td><td>'.round($sogou_average).'%</td><td>'.round($tencent_average).'%</td></tr>
	</table>';
?>
</div>
</body>
</html>