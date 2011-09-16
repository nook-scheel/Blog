<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head>
		<meta http-equiv='Content-Type' content='application/xhtml+xml; charset=utf-8' /> 
		<title>Вход - PumpkinEngine</title> 
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>content/css/login.css" />
	</head>
	<body>
		<div id="container">
			<div id="login">
				<div class="header">
					<div class="header_text">
						<span class="left"><b>Авторизация</b></span>
					</div>
				</div>
				<div class="login_body">
				<?=form_open(base_url().'login', 'name="form_login"')?>
					<div class="second">
						<div class="text">
							Логин
						</div>
						<div class="input">
							<?=form_input('userName', set_value('userName'))?>
						</div>
					</div>
					<div class="second">
						<div class="text">
							Пароль
						</div>
						<div class="input">
							<?=form_password('userPassword')?>
						</div> 
					</div>
					<div class="second">
						<div class="input">
							<a class="submit" href="javascript:document.form_login.submit()"> </a>
						</div> 
					</div>
				<?=form_close()?>
				</div>
				<div class="footer"></div>
			</div>
		</div>
	</body>
</html>