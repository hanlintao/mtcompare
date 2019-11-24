<?php
/***************************************************************************

 * Copyright (c) 2015 Baidu.com, Inc. All Rights Reserved
 * 
**************************************************************************/



/**
 * @file baidu_transapi.php 
 * @author mouyantao(mouyantao@baidu.com)
 * @date 2015/06/23 14:32:18
 * @brief 
 *  
 **/

//define("CURL_TIMEOUT",   10); 
define("baidu_URL",            "http://api.fanyi.baidu.com/api/trans/vip/translate"); 
define("baidu_APP_ID",         "******"); //替换为您的百度应用ID
define("baidu_SEC_KEY",        "******");//替换为您的百度应用密钥

define("youdao_URL",            "http://openapi.youdao.com/api"); 
define("youdao_APP_ID",         "******"); //替换为您的有道应用ID
define("youdao_SEC_KEY",        "******");//替换为您的有道应用密钥

define("sogou_URL",        "http://fanyi.sogou.com/reventondc/api/sogouTranslate"); 
define("sogou_APP_ID",         "******"); //替换为您的搜狗应用ID
define("sogou_SEC_KEY",        "******");//替换为您的搜狗应用密钥

define("tencentai_URL",        "https://api.ai.qq.com/fcgi-bin/nlp/nlp_texttrans"); 
define("tencentai_APP_ID",         "******"); //替换为您的腾讯应用ID
define("tencentai_SEC_KEY",        "******");//替换为您的腾讯应用密钥


class paraTrans{
	
	var $timeout = 10;
	var $APP_ID;
	var $SEC_KEY;
	var $URL;
	
	function __construct($APP_ID,$SEC_KEY,$URL,$idtype,$headers)
	{
		$this->APP_ID = $APP_ID;
		$this->SEC_KEY = $SEC_KEY;
		$this->URL = $URL;
		$this->idtype = $idtype;
		$this->headers = $headers;
	}
	
	//翻译入口
	function translate($query,$from,$to)
	{
		$query = str_replace(PHP_EOL, '', $query); 
		
		$APP_ID = $this->APP_ID;
		$SEC_KEY = $this->SEC_KEY;
		$URL = $this->URL;
		$idtype = $this->idtype;
		
		$args = array(
			'q' => $query,
			$idtype => $APP_ID,//三个API的idtype不一样，百度的是appid，有道的是appKey，搜狗的是pid
			'salt' => rand(10000,99999),
			'from' => $from,
			'to' => $to,
	
		);

		$args['sign'] = $this->buildSign($query, $APP_ID, $args['salt'], $SEC_KEY);
		$method = "POST";
		$testflag = 0;
		$timeout = $this->timeout;
		$headers = $this->headers;
		$ret = $this->call($URL, $args, $method,$testflag,$timeout,$headers);
		$ret = json_decode($ret, true);
		return $ret; 
	}
	
	//加密
	function buildSign($query, $APP_ID, $salt, $SEC_KEY)
	{/*{{{*/
		$APP_ID = $this->APP_ID;
		$SEC_KEY = $this->SEC_KEY;
		$str = $APP_ID . $query . $salt . $SEC_KEY;
		$ret = md5($str);
		return $ret;
		
	}/*}}}*/

	//发起网络请求
	function call($URL, $args=null, $method, $testflag, $timeout, $headers)
	{/*{{{*/
		
		$ret = false;
		$i = 0; 
		while($ret === false) 
		{
			if($i > 1)
				break;
			if($i > 0) 
			{
				sleep(1);
			}
			$ret = $this->callOnce($URL, $args, $method, false, $timeout, $headers);
			$i++;
		}
		return $ret;
	}/*}}}*/
	
	function callOnce($URL, $args=null, $method="post", $withCookie = false, $timeout, $headers=array())
	{/*{{{*/
		$ch = curl_init();

		$data = $this->convert($args);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($headers)) 
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		if($withCookie)
		{
			curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
		}
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
		
	}/*}}}*/
	
	function convert(&$args)
	{/*{{{*/
		$data = '';
		if (is_array($args))
		{
			foreach ($args as $key=>$val)
			{
				if (is_array($val))
				{
					foreach ($val as $k=>$v)
					{
						$data .= $key.'['.$k.']='.rawurlencode($v).'&';
					}
				}
				else
				{
					$data .="$key=".rawurlencode($val)."&";
				}
			}
			return trim($data, "&");
			
		}
		return $args;
	}/*}}}*/
}


//$baidu = new paraTrans(baidu_APP_ID,baidu_SEC_KEY,baidu_URL,"appid",array());
//
//$baidu_result = $baidu->translate("中华人民共和国","zh","en");
//echo $baidu_result["trans_result"][0]["dst"];
//
//$youdao = new paraTrans(youdao_APP_ID,youdao_SEC_KEY,youdao_URL,"appKey",array());
//
//$youdao_result = $youdao->translate("中华人民共和国","zh-CHS","en");
//
//
//echo $youdao_result["translation"][0];
//
//$sogou = new paraTrans(sogou_APP_ID,sogou_SEC_KEY,sogou_URL,"pid",array("Content-Type:application/x-www-form-urlencoded","Accept:application/json"));
//
//$sogou_result = $sogou->translate("中华人民共和国","zh-CHS","en");
//
//echo $sogou_result["translation"];
?>
