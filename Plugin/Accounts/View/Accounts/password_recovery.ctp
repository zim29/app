<style type="text/css">
	div.form-group label {
		max-width:  100% !important;
	}
</style>

<article>
	<header class="jumbotron">
		<h1><?= __('Password recovery'); ?></h1>
	</header>
	<div class="container theme-showcase" role="main">
		<?php
		echo $this->Form->create('Account', array('action' => 'password_recovery', 'id'=>'password_recovery', 'class' => 'form-horizontal label_full_width'));

		$inputs = array(
			$this->Form->input('email', array('label' => 'Email', 'type' => 'text', 'value' => !empty($this->request->data['Account']['email']) ? $this->request->data['Account']['email'] : '')),
		);

		$this->FormTool->fieldset(
			array(
				'title' => false,
				'columns' => 1,
				'inputs' => $inputs
			)
		);
		?>
		<div style="clear:both;"></div>
		<div class="row">
			<div class="col-md-12 send_ticket">
				<div class="form-group text-left">
					<a href="javascript:{}" onclick="$(this).closest('form').submit();" class="btn btn-lg btn-primary ticket"><?= __('Recover password') ?></a>
				</div>
			</div>
		</div>
	</div>
</article>
