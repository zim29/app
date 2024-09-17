<?php echo $this->Html->script(
		array(
				'Accounts.Account/country_zone.js?'.date('YmdHis')
		)
); ?>

<?php if (!empty($current_zone_id)) { ?>
	<script>var current_zone_id = <?= $current_zone_id ?>; </script>
<?php } ?>
<style type="text/css">
	div.form-group label {
		max-width:  100% !important;
	}
</style>
<?php
	$page_title = !empty($this->request->data['Account']['id']) ? __('Edit account') : __('Register');
	$button_title = !empty($this->request->data['Account']['id']) ? __('Save') : __('Register');
?>
<article>
	<header class="jumbotron">
		<h1><?= $page_title; ?></h1>
	</header>
	<div class="container theme-showcase" role="main">
		<?php
		echo $this->Form->create('Account', array('action' => 'register', 'id'=>'register', 'class' => 'form-horizontal label_full_width'));
		$label_password = !empty($this->request->data['Account']['id']) ? 'New password' : 'Password *';

		$inputs = array(
			$this->Form->input('name', array('label' => 'Name / Company *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['name']) ? $this->request->data['Account']['name'] : '')),
			$this->Form->input('vat', array('label' => 'VAT (Europe - Fill to remove taxes)', 'type' => 'text', 'value' => !empty($this->request->data['Account']['vat']) ? $this->request->data['Account']['vat'] : '')),
			$this->Form->input('email', array('label' => 'Email *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['email']) ? $this->request->data['Account']['email'] : '')),
			$this->Form->input('email_confirm', array('label' => 'Email confirm *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['email_confirm']) ? $this->request->data['Account']['email_confirm'] : '')),
			$this->Form->input('country_id', array('label' => 'Country *', 'type' => 'select', 'options' => $countries, 'value' => !empty($this->request->data['Account']['country_id']) ? $this->request->data['Account']['country_id'] : '')),
			$this->Form->input('zone_id', array('label' => 'Region / State *', 'type' => 'select', 'options' => $zones, 'value' => !empty($this->request->data['Account']['zone_id']) ? $this->request->data['Account']['zone_id'] : '')),
			$this->Form->input('city', array('label' => 'City *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['city']) ? $this->request->data['Account']['city'] : '')),
			$this->Form->input('address', array('label' => 'Address *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['address']) ? $this->request->data['Account']['address'] : '')),
			$this->Form->input('post_code', array('label' => 'Post code *', 'type' => 'text', 'value' => !empty($this->request->data['Account']['post_code']) ? $this->request->data['Account']['post_code'] : '')),
			$this->Form->input('password', array('label' => $label_password, 'type' => 'text', 'value' => !empty($this->request->data['Account']['password']) ? $this->request->data['Account']['password'] : '')),
		);

		if(empty($this->request->data['Account']['id_klaviyo']) || (!empty($this->request->data['Account']['id_klaviyo']) && empty($this->request->data['Account']['newsletter'])))
			$inputs[] = $this->Form->input('newsletter', array('label' => 'I allow DevmanExtensions to contact me on email', 'type' => 'checkbox', 'value' => !empty($this->request->data['Account']['newsletter'])));
		elseif(!empty($this->request->data['Account']['id_klaviyo']) && !empty($this->request->data['Account']['newsletter']))
			$inputs[] = $this->Form->input('newsletter', array('label' => 'ID', 'type' => 'hidden', 'value' => 1));


		if(!empty($this->request->data['Account']['id'])) {
			unset($inputs[3]);
			$inputs[] = $this->Form->input('id', array('label' => 'ID', 'type' => 'hidden', 'value' => !empty($this->request->data['Account']['id']) ? $this->request->data['Account']['id'] : ''));
		}

		$this->FormTool->fieldset(
				array(
						'title' => false,
						'columns' => 3,
						'inputs' => $inputs
				)
		);
		?>
		<div style="clear:both;"></div>
		<div class="row">
			<div class="col-md-12 send_ticket">
				<div class="form-group text-right">
					<div class="g-recaptcha" style="position: relative; float: left;" data-sitekey="6LeNxKAUAAAAAGHlDeqliG7-9wDsvvQhv8a6i3Cw"></div>
					<a href="javascript:{}" onclick="$(this).closest('form').submit();" class="btn btn-lg btn-primary ticket"><?= $button_title ?></a>
				</div>
			</div>
		</div>
	</div>
</article>
