<center><h2>Execute your Import/Export profiles automatically when you want!</h2></center>

<style>
    @import url('https://fonts.googleapis.com/css?family=Bangers');
    a.btn.btn-primary.button_add_to_cart
    {
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
    a.btn.btn-primary.button_add_to_cart:hover
    {
        background: #98ca00; /* Old browsers */
        background: -moz-linear-gradient(top, #98ca00 0%, #b0eb00 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(top, #98ca00 0%,#b0eb00 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to bottom, #98ca00 0%,#b0eb00 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#98ca00', endColorstr='#b0eb00',GradientType=0 ); /* IE6-9 */
    }

    a.btn.btn-primary{
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
            <select class="form-control quantity_con_jobs" name="units">
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
            <a href="javascript:{}" onclick="purchase($('select.quantity_con_jobs').val(), 'cron_purchase');" class="btn btn-primary button_add_to_cart"><i class="fa fa-shopping-basket"></i><?= __('Purchase now!'); ?></a>
        </div>
    </div>
    <div class="col-md-8">
        <p>With this <b>extra complement</b> you will can create <b>CRON Jobs</b> to put these in your server configuration, by this way, your server auto launches Import/Export profiles when you want. (All days at 00:00, all days at 00:00, 06:00, 12:00..., One time per week....)</p>
        <p>This can be useful when:
            <ol>
                <li>You are <b>updating your shop</b> connecting with <b>external vendor</b> that is giving his products in a file/url.</li>
                <li>You are <b>sending your products</b> from your shop to another extenal plataform or vendor.</li>
                <li>You want <b>save automatically backups</b> of your products, categories, orders, options, attributes...</li>
            </ol>
        </p>
    </div>
</div>