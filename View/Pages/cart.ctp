<?php echo $this->Html->css(
    array( 
    'pages/cart.css?'.date('YmdHis')
    )
); ?>

<?php echo $this->Html->script(
    array( 
    'cart.js?'.date('YmdHis')
    )
); ?>


<article role="main">
	<header class="jumbotron text-center">
	  <h1 class="main"><?= __('Cart') ?></h1>
	 </header>

	<div class="container theme-showcase" role="main">
		<?php if(!empty($products)) { ?>
			<table class="table cart">
				<thead>
					<tr>
						<td><b><?= __('Extension') ?></b></td>
	                    <td><b><?= __('Price') ?></b></td>
						<td class="text-center"><b><?= __('Quantity') ?></b></td>
	                    <td class="text-center"><b><?= __('Discount') ?></b></td>
						<td><b><?= __('Total') ?></b></td>
						<td class="text-center"><b><?= __('Remove') ?></b></td>
					</tr>
					<?php foreach ($products as $key => $prod) { ?>
	                    <?php $exist_discount = array_key_exists($prod['id'], $discounts) ? $discounts[$prod['id']]['code'] : false ?>
						<tr>
							<td><?= $prod['name'] ?></td>
	                        <td>$<?= $prod['price'] ?></td>
							<td class="text-center">
	                            <select name="products[<?= $prod['id'] ?>]"  onchange="update_cart($(this).closest('table'))">
	                                <?php for ($i = 1; $i <= 10; $i++) { ?>
	                                    <option value="<?= $i ?>" <?= $prod['quantity'] == $i ? 'selected="selected"' : '' ?>><?= $i ?></option>
	                                <?php } ?>
	                            </select>
	                        <td class="text-center"><b><?= !empty($prod['discount_percentage']) ? $prod['discount_percentage'].'%' : ($exist_discount ? $discounts[$prod['id']]['discount'].'%' : '') ?></b></td>
							<td><b>$<?= $prod['total'] ?></b></td>
							<td class="text-center"><i class="fa fa-times delete_product" onclick="remove_from_cart('<?= $prod['id'] ?>')"></i></td>
						</tr>
					<?php } ?>
					<tr>
	                    <td colspan="3"></td>
						<td class="text-right"><?php if (empty($exist_discount)) { ?><a href="javascript:{}" title="<?= __('Apply'); ?>" class="btn btn-primary apply_discount" onclick="apply_discount_general($(this))"><?= __('Apply'); ?></a><?php } ?><input name="coupon_code" placeholder="Coupon" type="text" <?= !empty($exist_discount) ? 'disabled="disabled" value="'.$exist_discount.'"' : '' ?> class="form-control discount_code"></td>
						<td class="total">Total: </td>
						<td class="total">$<?= $cart_total ?></td>
						<td></td>
					</tr>
				</thead>
			</table>
			<div class="row">
				<div class="col-md-12 text-right"><a class="btn btn-lg btn-primary" href="<?= Router::url("/", false) ?>invoices/opencart/new_invoice?type=license"><?= __('Checkout process') ?></a></div>
			</div>
		<?php } else { ?>
			<h2><?= __('Your cart is empty') ?></h2>
		<?php } ?>
	</div>

</article>