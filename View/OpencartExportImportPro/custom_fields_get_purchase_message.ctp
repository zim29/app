<center><h2>Add Custom Fields to Import/Export Processes!</h2></center>

<style>
    @import url('https://fonts.googleapis.com/css?family=Bangers');
    div#tab-custom-fields a.btn.btn-primary.button_add_to_cart,
    div#tab-Произвольные-поля a.btn.btn-primary.button_add_to_cart {
        font-family: Bangers;
        color: #000;
        border-color: #97c900;
        background: #b0eb00; /* Old browsers */
        background: -moz-linear-gradient(top, #b0eb00 0%, #98ca00 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top, #b0eb00 0%,#98ca00 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom, #b0eb00 0%,#98ca00 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b0eb00', endColorstr='#98ca00',GradientType=0 ); /* IE6-9 */
        width: 100%;
        height: 62px;
        font-size: 28px;
        line-height: 49px;
        margin-top: 20px;
    }
    div#tab-custom-fields a.btn.btn-primary.button_add_to_cart:hover,
    div#tab-Произвольные-поля a.btn.btn-primary.button_add_to_cart:hover {
        background: #98ca00; /* Old browsers */
        background: -moz-linear-gradient(top, #98ca00 0%, #b0eb00 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top, #98ca00 0%,#b0eb00 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom, #98ca00 0%,#b0eb00 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#98ca00', endColorstr='#b0eb00',GradientType=0 ); /* IE6-9 */
    }

    div#tab-custom-fields a.btn.btn-primary,
    div#tab-Произвольные-поля a.btn.btn-primary {
        width: 100%;
    }
</style>

<script type="text/javascript">
    function purchase(units, link) {
        var url = "<?= Router::url('/', true); ?>opencart_export_import_pro/"+link+"/"+units;
        window.open(url, '_blank');
    }
</script>

<div style="clear:both; height: 30px;"></div>
<div class="row">
    <div class="col-md-4">
        <div class="cart">
            <label><?= __('What do you need?'); ?></label>
            <select class="form-control quantity_custom_fields" name="units">
                <?php foreach ($ext['Extension']['prices'] as $unit => $price) { ?>
                    <?php if($unit == 1) { ?>
                        <?php $main_price = $price ?>
                        <option value="<?= $unit ?>"><?= sprintf(__('Single domain - $%s'), $price) ?></option>
                    <?php } else { ?>
                        <?php $total = number_format($price*$unit, 2); ?>
                        <?php $discount = 100 - round(($price*100) / $main_price); ?>
                        <option value="<?= $unit ?>"><?= sprintf(__('%s Domains - %s%% discount $%s - Total: $%s'), $unit, $discount, $price, $total) ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <a href="javascript:{}" onclick="purchase($('select.quantity_custom_fields').val(), 'custom_fields_purchase');" class="btn btn-primary button_add_to_cart"><i class="fa fa-shopping-basket"></i><?= __('Purchase now!'); ?></a>
        </div>
    </div>
    <div class="col-md-8">
        <p>Do you have <b>external extensions</b> that <b>add custom fields</b> to your products, categories, options? How would you like to edit them in bulk?</p>
        <p>With this add-on you can create as <b>many custom fields as you want</b> When you do, they will appear in the import/export profile configuration <b>as new columns</b>.</p>
        <p>On the left side of this page, choose the number of licenses you want (one per domain) and press the "Purchase Now!"  button to complete your purchase.</p>
        <p><a href="http://opencart.devmanextensions.com/import_export_pro/admin/index.php?route=extension/module/import_xls" target="_blank">Visit our demo store</a> to see this add-on in action!</p>
    </div>
</div>