<?php echo $this->Html->css(
    array( 
    'pages/our_star_products.css?'.date('YmdHis')
    )
); ?>

<h1 class="main"><?= __('Our star products') ?></h1>

<div class="container theme-showcase" role="main">
	<div style="clear:both;"></div>
	<?php foreach ($star_products as $key => $ext) { ?>
		<?php $is_par = ($key+1)%2 == 0; ?>
		<?php $is_last_row = ($key+1) == count($star_products) ?>

		<?= $key == 0 ? '<div class="row">' : '' ?>
			<div class="col-md-6">
				<div class="start_product <?= $ext['type'] ?> <?= strtolower(str_replace(array(' ', '/', '-', '.'), '_', $ext['title_main'])) ?>">
					<h2 class="title"><?= $ext['title_main'] ?></h2>
					<div style="clear:both;"></div>
					<h3 class="sub_title"><?= $ext['title_sub'] ?></h3>
					<div class="description"><?= $ext['description'] ?></div>
					<div class="features"><?= $ext['features_formatted'] ?></div>
					<div class="panel_statistics">
						<div class="col-md-5 col-xs-5 col-xxs-5 rating"><?= __('Rating') ?><div class="separator"></div>
							<?php for ($i=1; $i <= $ext['rate']; $i++) { ?>
								<?= $this->Html->image('pages/our_star_products/star.png', array('class' => 'star img-responsive')); ?>
							<?php } ?>
						</div>
						<div class="col-md-3 col-xs-3 col-xxs-3 active_users"><?= __('Active users') ?><div class="separator"></div><span class="active_users"><?= $ext['num_clients'] ?></span></div>
						<div class="col-md-4 col-xs-4 col-xxs-4 buy_it text-right"><a href="/shop" class="btn btn-primary"><?= __('Buy it!') ?></a></div>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		<?= $is_par && !$is_last_row ? '</div><div class="row">' : '' ?>
		<?= $is_last_row ? '</div>' : '' ?>
	<?php } ?>
</div>