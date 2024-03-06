<?php /*a:1:{s:97:"D:\phpStudy\PHPTutorial\WWW\dvvv\vendor\qsnh\think-log-viewer\src\Controllers/../Views/index.html";i:1635833793;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>LogViewer - PowerBy Qsnh - https://github.com/Qsnh/think-log-viewer</title>
	<link href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
	<style>
		a { color: #333 }
		.list-group-item.active { background-color: orange; border-color: orange; }
	</style>
</head>
<body>
	
	<div class="container">
		<div class="row">
			<h2 style="line-height: 120px;">系统日志</h2>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<ul class="list-group">
					<?php if(is_array($files) || $files instanceof \think\Collection || $files instanceof \think\Paginator): $i = 0; $__LIST__ = $files;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$file): $mod = ($i % 2 );++$i;?>
					<li class="list-group-item <?php echo $file['real']==$default ? 'active'  :  ''; ?>">
						<a href="?file=<?php echo htmlentities($file['real']); ?>"><?php echo htmlentities($file['name']); ?></a>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
			<div class="col-sm-10">
				<ul class="list-group">
					<?php if(is_array($data['data']) || $data['data'] instanceof \think\Collection || $data['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$log): $mod = ($i % 2 );++$i;?>
					<li class="list-group-item">
						<h5 class="log-item" style="cursor: pointer"><?php echo htmlentities($log[1]); ?></h5>
						<code style="display: none;">
							<?php echo implode("<br>", $log); ?>
						</code>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 text-right">
				<?php echo htmlspecialchars_decode($paginator); ?>
			</div>
		</div>
	</div>
	
	<script src="https://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
	<script>
		$(function () {
			$('.log-item').click(function () {
				$(this).next().toggle();
			});
		});
	</script>
</body>
</html>