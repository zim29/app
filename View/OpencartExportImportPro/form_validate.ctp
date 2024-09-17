<center><h2>Last step, validate your license!</h2></center>

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
    function validate_license_<?= $key_word ?>(domain, license_id) {
        var url = "<?= Router::url('/', true); ?>opencart_export_import_pro/<?= $key_word ?>_validate_license";
        $.ajax({
            url: url,
            dataType: 'json',
            data: {'domain':domain, 'license_id':license_id, 'lang':'<?= $lang ?>'},
            type: "POST",
            beforeSend: function (data) {
                ajax_loading_open();
            },
            success: function (data) {
                ajax_loading_close();
                if(data.error)
                    open_manual_notification(data.message, 'warning', 'exclamation');
                else {
                    open_manual_notification(data.message, 'success');
                    setTimeout( function(){
                        location.reload();
                    }  , 1000 );
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('Internal error validating your license.');
                ajax_loading_close();
            }
        });
    }
</script>

<div style="clear:both; height: 30px;"></div>
<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <label><?= __('Insert your license id'); ?></label>
        <input type="text" class="form-control" name="<?= $key_word ?>_license_id" placeholder="<?= __('Insert your license id'); ?>">
        <a href="javascript:{}" onclick="validate_license_<?= $key_word ?>('<?= $domain ?>', $('input[name=<?= $key_word ?>_license_id]').val());" class="btn btn-primary button_add_to_cart"><?= __('Validate license'); ?></a>
    </div>
    <div class="col-md-4"></div>
</div>


<div style="clear:both; height: 30px;"></div>
<hr>
<div style="clear:both; height: 30px;"></div>
<?= $purchase_form ?>