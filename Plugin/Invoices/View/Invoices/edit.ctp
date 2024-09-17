<?php echo $this->Html->script(
    array( 
    'Invoices.Invoice/edit.js?'.date('YmdHis')
    )
); ?>

<?php
$user = $this->Session->read('Auth.User');
$role = $user['role'];
?>

<h1>Edit / Create invoice</h1>
<?php
$controller_invoice = empty($this->request->data['Invoice']['system']) || $this->request->data['Invoice']['system'] == 'Opencart' ? 'opencart' : 'cscart';
?>
<?php if(!empty($this->request->data['Invoice']['id'])) { ?>
	<?php $link_invoice = Router::url(['plugin' => 'invoices', 'controller' => $controller_invoice, 'action' => 'validate_invoice/'.$this->request->data['Invoice']['id']]); ?>
    <?php $link_pay = Router::url(['plugin' => 'invoices', 'controller' => 'invoices', 'action' => 'pay_invoice/'.$this->request->data['Invoice']['id']]); ?>
	<div class="row">
        <div class="col-md-12"><b>$1 = <?= $this->request->data['eur_currency_value'] ?>€</div>
		<div class="col-md-12"><b>Link to complete invoice: <a href="<?= $link_invoice ?>"><?= $link_invoice ?></a></div>
        <?php if($role != 'marketing') { ?>
        <div class="col-md-12"><b>Link to pay invoice: <a href="<?= $link_pay ?>"><?= $link_pay ?></a></div>
        <?php } ?>
	</div>
<?php } ?>
<script type="text/javascript">
	var paypal_fee = <?= $this->request->data['paypal_fee']; ?>;
	var stripe_fee = <?= $this->request->data['stripe_fee']; ?>;
</script>
<?php 
    echo $this->Form->create('Invoice', array('full_action' => Router::url(['controller' => 'invoices', 'action' => 'edit']), 'id'=>'Invoice', 'class' => 'form-horizontal'));

    echo $this->Form->input('is_eu', array('type' => 'hidden', 'type' => 'hidden', 'disabled' => true, 'value' => !empty($is_eu) ? 1 : 0 ));
    echo $this->Form->input('id', array('type' => 'hidden',  'value' => !empty($this->request->data['Invoice']['id']) ? $this->request->data['Invoice']['id'] : ''));
    
	$inputs = array(
		$this->Form->input('number', array('label' => 'Number', 'type' => 'text', 'disabled' => true, 'value' => !empty($this->request->data['Invoice']['number']) ? $this->request->data['Invoice']['number'] : '')),
		$this->Form->input('state', array('label' => 'State', 'type' => 'select', 'readonly' => $role == 'marketing', 'options' => $statuses, 'value' => !empty($this->request->data['Invoice']['state']) ? $this->request->data['Invoice']['state'] : '')),
		$this->Form->input('type', array('label' => 'Type', 'type' => 'select', 'readonly' => $role == 'marketing', 'options' => $types, 'value' => !empty($this->request->data['Invoice']['type']) ? $this->request->data['Invoice']['type'] : '')),
		$this->Form->input('system', array('label' => 'System', 'type' => 'select', 'readonly' => $role == 'marketing', 'options' => $systems, 'value' => !empty($this->request->data['Invoice']['system']) ? $this->request->data['Invoice']['system'] : '')),
		$this->Form->input('license_id', array('label' => 'License id', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['license_id']) ? $this->request->data['Invoice']['license_id'] : '')),
		$this->Form->input('payment_method', array('label' => 'Payment method', 'readonly' => $role == 'marketing', 'type' => 'select', 'options' => $payment_methods, 'value' => !empty($this->request->data['Invoice']['payment_method']) ? $this->request->data['Invoice']['payment_method'] : '')),
		$this->Form->input('paypal_id_transaction', array('label' => 'Paypal transaction', 'readonly' => $role == 'marketing', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['paypal_id_transaction']) ? $this->request->data['Invoice']['paypal_id_transaction'] : '')),
		$this->Form->input('description', array('label' => 'Description', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['description']) ? $this->request->data['Invoice']['description'] : '')),
		$this->Form->input('description_avanced', array('label' => 'Detailed description', 'type' => 'textarea', 'value' => !empty($this->request->data['Invoice']['description_avanced']) ? $this->request->data['Invoice']['description_avanced'] : '')),
		$this->Form->input('connections', array('label' => 'Connections', 'type' => 'textarea', 'value' => !empty($this->request->data['Invoice']['connections']) ? $this->request->data['Invoice']['connections'] : '')),

		$this->Form->input('price', array('label' => 'Price', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['price']) ? $this->request->data['Invoice']['price'] : '')),
		$this->Form->input('quantity', array('label' => 'Quantity', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['quantity']) ? $this->request->data['Invoice']['quantity'] : 1)),
		$this->Form->input('discount', array('label' => 'Discount', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['discount']) ? $this->request->data['Invoice']['discount'] : 0)),
		$this->Form->input('tax', array('label' => 'Tax', 'readonly' => true, 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['tax']) ? $this->request->data['Invoice']['tax'] : 0)),
		$this->Form->input('total', array('label' => 'Total', 'readonly' => true, 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['total']) ? $this->request->data['Invoice']['total'] : '')),

    	$this->Form->input('customer_name', array('label' => 'Name / Company *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_name']) ? $this->request->data['Invoice']['customer_name'] : '')),
    	$this->Form->input('customer_email', array('label' => 'Email *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_email']) ? $this->request->data['Invoice']['customer_email'] : '')),
    	$this->Form->input('customer_phone', array('label' => 'Phone', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_phone']) ? $this->request->data['Invoice']['customer_phone'] : '')),
    	$this->Form->input('customer_country_id', array('label' => 'Country *', 'type' => 'select', 'options' => $countries, 'value' => !empty($this->request->data['Invoice']['customer_country_id']) ? $this->request->data['Invoice']['customer_country_id'] : '')),
    	$this->Form->input('customer_zone_id', array('label' => 'Region / State *', 'type' => 'select', 'options' => $zones, 'value' => !empty($this->request->data['Invoice']['customer_zone_id']) ? $this->request->data['Invoice']['customer_zone_id'] : '')),
    	$this->Form->input('customer_city', array('label' => 'City *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_city']) ? $this->request->data['Invoice']['customer_city'] : '')),
    	$this->Form->input('customer_address', array('label' => 'Address *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_address']) ? $this->request->data['Invoice']['customer_address'] : '')),
    	$this->Form->input('customer_post_code', array('label' => 'Post code *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_post_code']) ? $this->request->data['Invoice']['customer_post_code'] : '')),
		$this->Form->input('customer_vat', array('label' => 'VAT (Europe - Fill to remove taxes)', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_vat']) ? $this->request->data['Invoice']['customer_vat'] : '')),

		$this->Form->input('new_domain', array('label' => 'New domain', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['new_domain']) ? $this->request->data['Invoice']['new_domain'] : '')),
  	);  

	$this->FormTool->fieldset(
		array(
			'title' => 'Invoice datas:',
			'columns' => 1,
			'inputs' => $inputs
		)
	);
?>
<?php
	$this->FormTool->button('Save', 'save');
?>