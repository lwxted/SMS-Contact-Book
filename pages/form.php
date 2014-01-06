<?php 
	global $ver;
	$ver = 2; 
	require_once('../initialize.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<html>
	<head>
		<?php include (CONTENTS_PATH . '/header.php'); ?>
		<?php use_js('common.js');?>
		<?php use_js('all.js');?>
	</head>
	
	<body>
		<div class="footerfix clearfix">
			<?php include (CONTENTS_PATH . '/navigation.php') ?>
			<div class="content clearfix">
				<div class="row">
					<div class="col-md-5">
						<div class="animation clearfix">
							<h1 class="animate1">Unique 7</h1>
							<h2 class="animate2">分享你的联系方式，</h2>
							<h2 class="animate3">让我们保持联系。</h2>
							<div class="animate4">
								<form role="form" class="form clearfix" id="contact-form">
									<div class="form-title">通讯录</div>

									<div class="form-group">
										<label class="control-label" for="name">姓名 </label>
										<input class="form-control" id="name" name="name" type="text">
									</div>
									
									<label class="control-label" for="phone">手机 </label>
									<div class="input-group">
										<span class="input-group-addon">+</span>
										<input class="form-control" id="phone" name="phone" type="text" placeholder="86 13012345678">
									</div>
									
									<div class="form-group">
										<label class="control-label" for="email">邮箱 </label>
										<input class="form-control" id="email" name="email" type="email">
									</div>

									<div class="form-group">
										<label class="control-label" for="qq">QQ </label>
										<input class="form-control" id="qq" name="qq" type="text">
									</div>

									<div class="form-group">
										<label class="control-label" for="major">专业 </label>
										<input class="form-control" id="major" name="major" type="text" placeholder="计算机科学">
									</div>

									<input type="hidden" id="query" name="query" value="add_contact" />
									<p class="text-error form-error"></p>
									<p class="text-success form-success"></p>
									<button type="submit" class="btn btn-default submit pull-right">提交<div class="loading" style="display: none;"></div></button>
								</form>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="table-container">
							<h3>请分享你的联系方式，填好后将在下表中出现。</h3>
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>姓名</th>
										<th>手机</th>
										<th>邮箱</th>
										<th>QQ</th>
										<th>专业</th>
									</tr>
								</thead>
								<tbody class="nodes">
									
								</tbody>
							</table>
							<div class="loading" style="display:none;"></div>
						</div>
					</div>
				</div>
			</div>
			 <div class="push"></div>
		</div>
		<?php include (CONTENTS_PATH . '/footer.php'); ?>
	</body>
</html>



