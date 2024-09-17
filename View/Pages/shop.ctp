<?php echo $this->Html->css(
    array('pages/shop.css?' . date('YmdHis')
    )
); ?>

<?php echo $this->Html->script(
    array(
        'pages/shop.js?' . date('YmdHis')
    )
); ?>
<script type="text/javascript">
    var system_button_default = '<?= $system_button_default ?>';
</script>
<article>
    <header class="jumbotron">
        <h1><?= $title ?></h1>
        <p>For OpenCart lovers, we create extensions with a high level of acceptance.</p>
    </header>

    <?php /*if(!empty($black_friday)) { ?>
        <script type="text/javascript">
            $(function(){
                black_friday_timedown('<?= $black_friday['to_formatted'] ?>');
            });
        </script>
        <div class="black_friday">
            <b>BLACK FRIDAY</b> IS HERE! GET <b class="discount"><?= $black_friday['discount']; ?>% DISCOUNT</b> USING COUPON <u><b><?= $black_friday['coupon']; ?></b></u>
            <br>
            <b class="counter">...</b> HURRY UP!</span>
        </div>
    <?php }  */?>
    <div class="container theme-showcase" role="main">

        <div class="filter system bar-emphasis-filter" style="margin-bottom: 20px;">
            <a href="javascript:{}" data-system="opencart" onclick="show_extensions_system('opencart', $(this));"><?= __('Opencart'); ?></a>
            <?php /*<a href="javascript:{}" data-system="woocommerce" onclick="show_extensions_system('woocommerce', $(this));"><?= __('Woocommerce'); ?></a>
            <a href="javascript:{}" data-system="cs-cart" onclick="show_extensions_system('cs-cart', $(this));"><?= __('CS-Cart'); ?></a>*/ ?>
        </div>


        <div class="filter type bar-emphasis-filter">
            <a href="javascript:{}" data-type="all" onclick="show_extensions('', $(this));"><?= __('All'); ?></a>
            <a href="javascript:{}" data-type="module" onclick="show_extensions('module', $(this));"><?= __('Modules'); ?></a>
            <a href="javascript:{}" data-type="analytics" onclick="show_extensions('analytics', $(this));"><?= __('Marketing'); ?></a>
            <a href="javascript:{}" data-type="template" onclick="show_extensions('template', $(this));"><?= __('Themes'); ?></a>
        </div>
        <div style="clear:both;"></div>

        <div class="card-deck card-deck-fr">
            <?php foreach ($extensions as $key => $ext) : ?>
                <div style="display: none;" class="card card-extension type_<?= $ext['type'] ?> system_<?= $ext['system'] ?>" data-name="<?= $ext['name_formatted'] ?>">
                    <div class="card-body">
                        <a class="description" title="<?= $ext['title_main'] ?>" href="<?= $ext['seo_url'] ?>">
                            <?= $ext['description'] ?>
                            <br>
                            <span class="know_more"><?= __('Know more'); ?></span>
                        </a>
                        <h2 class="card-title"><?= $ext['title_main'] ?></h2>
                        <p class="card-subtitle"><?= $ext['title_sub'] ?></p>
                        <p class="logo text-center">
                            <?= $this->Html->image('pages/shop/' . $ext['system'] . '/' . $ext['name_formatted'] . '/logo.png', array('title' => $ext['title_main'] . ' - ' . $ext['system'])) ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="users pull-left"><span class="users"><?= __('Active users') ?></span><br><span
                                    class="active_users"><?= $ext['num_clients'] ?></span></div>

						<?php if (!empty($ext['special']) && $ext['special'] < $ext['old_price']) { ?>
							<div class="price-old pull-right"><span class="old_price" style="text-decoration: line-through;">$<?= $ext['old_price'] ?></span> <span class="new_price" style="color: #ff0d4d; font-weight: bold; font-size: 26px;">$<?= $ext['special'] ?></span></div>
						<?php } else { ?>
                        	<div class="price pull-right">$<?= $ext['price'] ?></div>
						<?php } ?>
                    </div>
                </div>
                <?php if (($key + 1) % 3 === 0): ?>
                    <div class="w-100"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>


    </div>
</article>
