<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- 引入 Bootstrap -->
     <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	 <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
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
				
			</div>
		</div>
	</div>




<div class="row">
<div class="col-md-12">
<div class="col-md-4">
	<blockquote class="blockquote">
		<p class="mb-0">
			本网站为试用版，目前仅支持中英和英中方向，任何文档仅支持前十个句子的机器翻译对比。
		</p>
		<footer class="blockquote-footer">
			文档解析API由Tmxmall提供。
		</footer>
	</blockquote>
</div>

<div class="col-md-8">
	<div class="panel panel-info">

		<div class="panel-heading">
			<form class="form-inline">
				<div class="row">
					<div class="col-xs-6">
					
						<a href="javascript:void(0);" class="btn btn-info" role="button">上传文件</a>  
					
					</div>
					<div class="col-xs-6 text-right">
						
					</div>
				</div>	
			</form>
		</div>
		
		<div class="panel-body">
			<form action="upload.php" method="post" enctype="multipart/form-data">
				<div class="col-xs-12 col-sm-4 col-md-4">
					<div class="file-container" style="display:inline-block;position:relative;overflow: hidden;vertical-align:middle">
						<button class="btn btn-success fileinput-button" type="button">选择文件</button>
						<input type="file" name="file" id="jobData" onchange="loadFile(this.files[0])" style="position:absolute;top:0;left:0;font-size:34px; opacity:0">
					</div>
					<br>
					<span id="filename" style="vertical-align: middle">未上传文件</span>
				</div>
				
				<div class="col-xs-12 col-sm-4 col-md-4">
					<label class="radio-inline">
							<input type="radio" name="language"  value="1" checked>中英
						</label>
						<label class="radio-inline">
							<input type="radio" name="language"  value="2"> 英中
						</label>
				</div>
				<script>
				function loadFile(file){
					$("#filename").html(file.name);
				}
				</script>
		
				<button class="btn btn-default fileinput-button" type="submit">开始PK</button>										
			</form>
		</div>
	</div>
</div>
</div>
</div>
</body>
</html>