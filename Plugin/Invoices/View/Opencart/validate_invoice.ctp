<article role="main">
	<header class="jumbotron text-center">
	  <h1 class="main">Validate invoice</h1>
	 </header>

    <?php echo $this->Html->script(
        array(
            'Invoices.Opencart/new_invoice.js?'.date('YmdHis')
        )
    ); ?>

    <style type="text/css">
        div.form-group label {
            max-width:  100% !important;
        }
        fieldset {
            width: 100%;
        }
        fieldset div.col-sm-10 {
            width: 100% !important;
            max-width: 100% !important;
        }
    </style>

    <script type="text/javascript">
        var eur_currency_value = <?= $this->request->data['Invoice']['currency_euro_value']; ?>;
        var dollar_currency_value = <?= $this->request->data['dollar_currency_value']; ?>;
        var base_price = <?= $this->request->data['Invoice']['price']; ?>;
        var quantity = <?= $this->request->data['Invoice']['quantity']; ?>;
        var paypal_fee = <?= $this->request->data['paypal_fee']; ?>;
        var stripe_fee = <?= $this->request->data['stripe_fee']; ?>;
        var zone_id = <?= isset($zone_id) ? $zone_id : 0 ?>;
    </script>

    <div class="container theme-showcase" role="main">
        <?php
            $is_bank_transfer = $this->request->data['Invoice']['payment_method'] == 'Bank Transfer';

            echo $this->Form->create('Invoice', array('full_action' => Router::url(['controller' => 'opencart', 'action' => 'validate_invoice']), 'id'=>'validateInvoice', 'class' => 'form-horizontal label_full_width'));
            echo $this->Form->input('id', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['id']) ? $this->request->data['Invoice']['id'] : '' ));
            echo $this->Form->input('license_id', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['license_id']) ? $this->request->data['Invoice']['license_id'] : '' ));
            echo $this->Form->input('state', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['state']) ? $this->request->data['Invoice']['state'] : '' ));
            echo $this->Form->input('is_eu', array('type' => 'hidden', 'type' => 'hidden', 'disabled' => true, 'value' => !empty($is_eu) ? 1 : 0 ));
            echo $this->Form->input('type', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['type']) ? $this->request->data['Invoice']['type'] : '' ));
            echo $this->Form->input('total', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['total']) ? $this->request->data['Invoice']['total'] : '' ));
            echo $this->Form->input('quantity', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['quantity']) ? $this->request->data['Invoice']['quantity'] : '' ));
            echo $this->Form->input('description', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['description']) ? $this->request->data['Invoice']['description'] : '' ));
            echo $this->Form->input('price', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['price']) ? $this->request->data['Invoice']['price'] : '' ));
            echo $this->Form->input('tax', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['tax']) ? $this->request->data['Invoice']['tax'] : '0' ));
            echo $this->Form->input('discount', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['discount']) ? $this->request->data['Invoice']['discount'] : 0 ));
            echo $this->Form->input('new_domain', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['new_domain']) ? $this->request->data['Invoice']['new_domain'] : '' ));
            echo $this->Form->input('licenses', array('type' => 'hidden', 'type' => 'hidden', 'readonly' => true, 'value' => !empty($this->request->data['Invoice']['licenses']) ? $this->request->data['Invoice']['licenses'] : '' ));

            $payment_methods = array(
                //'Credit Card' => 'Credit card (VISA, Mastercard, Maestro) <i class="fa fa-question-circle animated infinite pulse" style="color:#ff0d4d" data-toggle="tooltip" data-html="true" title="If you experiment problems paying your invoice, use next payment methods <b>Stripe</b> or <b>Paypal</b>, our national TPV present problems with some credit card types."></i>',
                'Credit Card' => 'Credit card (Only UE credit cards EUROS €)',
                'Stripe' => 'Credit card (Stripe, 2% fee)',
                'Paypal' => 'Paypal (3.7% fee)'
            );

            if(array_key_exists('Invoice', $this->request->data) && array_key_exists('payment_method', $this->request->data['Invoice']) && $is_bank_transfer) {
                $payment_methods = array();
                $payment_methods['Bank Transfer'] = 'Bank Transfer';
            }

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

            if(in_array($this->request->data['Invoice']['type'], array('Personal develop', 'GMT Extra service')))
            {
                $inputs = array();

                if(!empty($this->request->data['Invoice']['description_avanced']))
                    array_push($inputs, $this->Form->input('description_avanced', array('label' => 'Detailed description', 'readonly' => true, 'type' => 'textarea', 'value' => !empty($this->request->data['Invoice']['description_avanced']) ? $this->request->data['Invoice']['description_avanced'] : '')));
                array_push($inputs, $this->Form->input('connections', array('label' => 'Connections', 'placeholder' => 'If is a personal develop I will need temporary FTP account and temporary admin Opencart user', 'type' => 'textarea', 'value' => !empty($this->request->data['Invoice']['connections']) ? $this->request->data['Invoice']['connections'] : '')));

                $this->FormTool->fieldset(
                    array(
                        'title' => 'Extra information:',
                        'columns' => 1,
                        'inputs' => $inputs
                    )
                );
            }

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
                        <td><?= $this->request->data['Invoice']['description'] ?><?= $this->request->data['Invoice']['type'] == 'License' ? '<br>'.$this->request->data['Invoice']['description_avanced'] : ''; ?></td>
                        <td><?= $this->request->data['Invoice']['quantity'] ?></td>
                        <td class="price"><?= $this->request->data['Invoice']['price'] ?></td>
                        <td><?= !empty($this->request->data['Invoice']['discount']) ? $this->request->data['Invoice']['discount'].'%' : '0%' ?></td>
                        <td class="tax"><?= $this->request->data['Invoice']['tax'] ?>%</td>
                        <td class="total"><?= $this->request->data['Invoice']['total'] ?></td>
                    </tr>
                </tbody>
            </table>

        <?php
            if(array_key_exists('state', $this->request->data['Invoice']) && $this->request->data['Invoice']['state'] != 'Payed')
                $this->FormTool->button(!$is_bank_transfer ? 'Finish and pay' : 'Download bank transfer instructions', 'credit_card');
        ?>
    </div>
</article>