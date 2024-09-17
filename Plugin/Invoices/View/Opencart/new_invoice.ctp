<article role="main">
	<header class="jumbotron text-center">
	  <h1 class="main"><?= $title ?></h1>
	 </header>

	<?php if (!empty($this->request->data['Invoice']['customer_zone_id'])) { ?>
		<script>var current_zone_id = <?= !empty($this->request->data['Invoice']['customer_zone_id']) ? $this->request->data['Invoice']['customer_zone_id'] : 0 ?>; </script>
	<?php } ?>

    <?php echo $this->Html->script(
        array(
            'Invoices.Opencart/new_invoice.js?'.date('YmdHis')
        )
    ); ?>

    <style type="text/css">
        div.form-group label {
            max-width:  100% !important;
        }
    </style>

    <script type="text/javascript">
        var eur_currency_value = <?= $this->request->data['eur_currency_value']; ?>;
        var dollar_currency_value = <?= $this->request->data['dollar_currency_value']; ?>;
        var base_price = <?= $this->request->data['Invoice']['price']; ?>;
        var quantity = <?= $this->request->data['Invoice']['quantity']; ?>;
        var paypal_fee = <?= $this->request->data['paypal_fee']; ?>;
        var stripe_fee = <?= $this->request->data['stripe_fee']; ?>;
    </script>

    <style>
        fieldset {
            width: 100% !important;
        }
    </style>

    <div class="container theme-showcase" role="main">
        <?php
            echo $this->Form->create('Invoice', array('full_action' => Router::url(['controller' => 'opencart', 'action' => 'new_invoice']), 'id'=>'increaseLicense', 'class' => 'form-horizontal label_full_width'));

            echo $this->Form->input('license_id', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['license_id']) ? $this->request->data['Invoice']['license_id'] : '' ));
            echo $this->Form->input('system', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['system']) ? $this->request->data['Invoice']['system'] : '' ));
            echo $this->Form->input('is_eu', array('type' => 'hidden', 'type' => 'hidden', 'disabled' => true, 'value' => !empty($is_eu) ? 1 : 0 ));
            echo $this->Form->input('type', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['type']) ? $this->request->data['Invoice']['type'] : '' ));
            echo $this->Form->input('total', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['total']) ? $this->request->data['Invoice']['total'] : '' ));
            echo $this->Form->input('description', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['description']) ? $this->request->data['Invoice']['description'] : '' ));
            echo $this->Form->input('price', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['price']) ? $this->request->data['Invoice']['price'] : '' ));
            echo $this->Form->input('tax', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['tax']) ? $this->request->data['Invoice']['tax'] : '0' ));
            echo $this->Form->input('discount', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['discount']) ? $this->request->data['Invoice']['discount'] : '0' ));
            echo $this->Form->input('new_domain', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['new_domain']) ? $this->request->data['Invoice']['new_domain'] : '' ));
            echo $this->Form->input('quantity', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['quantity']) ? $this->request->data['Invoice']['quantity'] : 1 ));
            echo $this->Form->input('licenses', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['licenses']) ? $this->request->data['Invoice']['licenses'] : 1 ));
            echo $this->Form->input('description_avanced', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['description_avanced']) ? $this->request->data['Invoice']['description_avanced'] : 1 ));

            $payment_methods = array(
                //'Credit Card' => 'Credit card (VISA, Mastercard, Maestro) <i class="fa fa-question-circle animated infinite pulse" style="color:#ff0d4d" data-toggle="tooltip" data-html="true" title="If you experiment problems paying your invoice, use next payment methods <b>Stripe</b> or <b>Paypal</b>, our national TPV present problems with some credit card types."></i>',
                'Credit Card' => 'Credit card (Only UE credit cards EUROS €)',
                'Stripe' => 'Credit card (Stripe, 2% fee)',
                'Paypal' => 'Paypal (3.7% fee)'
            );

            $inputs = array(
                $this->Form->input('customer_name', array('label' => 'Name / Company *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_name']) ? $this->request->data['Invoice']['customer_name'] : '')),
                $this->Form->input('customer_vat', array('label' => 'VAT (Europe - Fill to remove taxes)', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_vat']) ? $this->request->data['Invoice']['customer_vat'] : '')),
                $this->Form->input('customer_email', array('label' => 'Email *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_email']) ? $this->request->data['Invoice']['customer_email'] : '')),
                $this->Form->input('customer_country_id', array('label' => 'Country *', 'type' => 'select', 'options' => $countries, 'value' => !empty($this->request->data['Invoice']['customer_country_id']) ? $this->request->data['Invoice']['customer_country_id'] : '')),
                $this->Form->input('customer_zone_id', array('label' => 'Region / State *', 'type' => 'select', 'options' => $zones, 'value' => !empty($this->request->data['Invoice']['customer_zone_id']) ? $this->request->data['Invoice']['customer_zone_id'] : '')),
                $this->Form->input('customer_city', array('label' => 'City *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_city']) ? $this->request->data['Invoice']['customer_city'] : '')),
                $this->Form->input('customer_address', array('label' => 'Address *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_address']) ? $this->request->data['Invoice']['customer_address'] : '')),
                $this->Form->input('customer_post_code', array('label' => 'Post code *', 'type' => 'text', 'value' => !empty($this->request->data['Invoice']['customer_post_code']) ? $this->request->data['Invoice']['customer_post_code'] : '')),
                $this->Form->input('payment_method', array('label' => 'Payment method *', 'type' => 'radio', 'options' => $payment_methods, 'value' => !empty($this->request->data['Invoice']['payment_method']) ? $this->request->data['Invoice']['payment_method'] : 'Credit Card')),
            );

            $this->FormTool->fieldset(
                array(
                    'title' => 'Invoice data:',
                    'columns' => 3,
                    'inputs' => $inputs
                )
            );
        ?>
            <div style="clear:both;"></div>
            <table class="table" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <td><b>Description</b></td>
                        <td><b>Quantity</b></td>
                        <td><b>Unit price</b></td>
                        <td><b>% discount</b></td>
                        <td><b>VAT (Taxes)</b></td>
                        <td><b>Total</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $this->request->data['Invoice']['description'] ?><br><?= $this->request->data['Invoice']['description_avanced']; ?></td>
                        <td><?= !empty($this->request->data['Invoice']['quantity']) ? $this->request->data['Invoice']['quantity'] : 1 ?></td>
                        <td class="price"><?= $this->request->data['Invoice']['price'] ?></td>
                        <td><?= !empty($this->request->data['Invoice']['discount']) ? $this->request->data['Invoice']['discount'].'%' : '0%' ?></td>
                        <td class="tax"><?= $this->request->data['Invoice']['tax'] ?>%</td>
                        <td class="total"><?= $this->request->data['Invoice']['total'] ?></td>
                    </tr>
                </tbody>
            </table>
        <?php
            $this->FormTool->button('Finish and pay', 'credit_card');
            //$this->FormTool->button($button_title, $button_icon);
        ?>
    </div>
</article>
