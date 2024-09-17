<article>
	<header class="jumbotron">
		<h1><?= __('My account'); ?></h1>
	</header>
	<div class="container theme-showcase" role="main">
		<?php if(!empty($is_logged)) { ?>
			<ul>
				<li><a href="register"><?= __('Edit profile information') ?></a></li>
				<li><a href="licenses"><?= __('My licenses') ?></a></li>
				<li><a href="<?= Router::url('/', true) ?>open_ticket"><?= __('Open a ticket') ?></a></li>
				<li><a href="logout"><?= __('Logout') ?></a></li>
			</ul>
		<?php } else { ?>

			<div class="row">
				<div class="col-md-6">
					<h2>Login</h2>
					<p>If you already have an account, click on next button:</p>
					<a href="login" class="btn btn-lg btn-primary"><?= __('Login') ?></a>
					<br>
					<a href="password-recovery"><?= __('I forgot my password') ?></a>
				</div>
				<div class="col-md-6">
					<h2>Register</h2>
					<p>If you haven't an account, click on next button:</p>
					<a href="register" class="btn btn-lg btn-primary"><?= __('Register') ?></a>
				</div>
			</div>
			<br><br><br><br><br><br><br><br><br><br><br><br>

		<?php } ?>
	</div>
</article>
