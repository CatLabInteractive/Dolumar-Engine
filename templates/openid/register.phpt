<?php $this->setTextSection ('register', 'openid'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html>
	<head>
	
		<title>Dolumar</title>
		<link href="<?=$static_client_url?>javascript/windowjs/themes/default.css" rel="stylesheet" type="text/css" >  

		<?=$header?>

	</head>
	
	<body>
		<div id="container" class="dialog">
			<table class="table_window">
				<tr>
					<td class="dolumar_nw">&nbsp;</td>
					<td class="dolumar_n">&nbsp;</td>
					<td class="dolumar_ne">&nbsp;</td>
				</tr>
				
				<tr>
					<td class="dolumar_w">&nbsp;</td>
					<td class="dolumar_content">
			
						<h1>Dolumar <?=$name?></h1>
						
						<?php if ($showDetails) { ?>
							<div id="register">
								<h2><?=$this->getText ('register')?></h2>
								<p>
									<?=$this->getText ('aboutRegister'); ?>
								</p>
				
								<p>
									<a href="<?=ABSOLUTE_URL?>openid/register?do=register&session_id=<?=$session_id?>"><?=$this->getText ('register')?></a>
								</p>
							</div>

							<div id="login">
		
								<h2><?=$this->getText ('login'); ?></h2>
								<?php if (isset ($error)) { ?>
									<p class="false">
										<?=$this->getText ('wrongLogin'); ?>
									</p>
								<?php } ?>

								<form action="<?=ABSOLUTE_URL?>openid/register?session_id=<?=$session_id?>&details=true" method="post">
									<fieldset>
										<ol>
											<li>
												<label><?=$this->getText ('username'); ?></label>
												<input type="text" name="username" />
											</li>
											<li>
												<label><?=$this->getText ('password'); ?></label>
												<input type="password" name="password" />
											</li>
											<li>
												<button type="submit"><?=$this->getText ('doLogin')?></button>
											</li>
										</ol>
									</fieldset>
								</form>
							</div>
						<?php } else { ?>
						
							<p><?=$this->getText ('about'); ?></p>
						
							<p id="welcome-choise">
								<a href="<?=ABSOLUTE_URL?>openid/register?do=register&session_id=<?=$session_id?>" class="left"><span><?=$this->getText ('btnRegister')?></span></a>
								<a href="<?=ABSOLUTE_URL?>openid/register?session_id=<?=$session_id?>&details=true" class="right"><span><?=$this->getText ('btnLogin'); ?></span></a>
								<div class="clear"></div>
							</p>
						
						<?php } ?>
			
						<div class="clear"></div>
					</td>
						
					<td class="dolumar_e">&nbsp;</td>
				</tr>
				
				<tr>
					<td class="dolumar_sw">&nbsp;</td>
					<td class="dolumar_s">&nbsp;</td>
					<td class="dolumar_se">&nbsp;</td>
				</tr>
			</table>
		</div>
	</body>
</html>
