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

<h1 class="main"><?= __('Cart') ?></h1>

<div class="container theme-showcase" role="main">
	<?php if(!empty($products)) { ?>
		<table class="table cart">
			<thead>
				<tr>
					<td><b><?= __('Extension') ?></b></td>
                    <td><b><?= __('Price') ?></b></td>
					<td class="text-center"><b><?= __('Quantity') ?></b></td>
					<td><b><?= __('Discount code') ?></b></td>
                    <td><b><?= __('Discount') ?></b></td>
					<td><b><?= __('Total') ?></b></td>
					<td class="text-center"><b><?= __('Remove') ?></b></td>
				</tr>
				<?php foreach ($products as $key => $prod) { ?>

					<?php $exist_discount = array_key_exists($prod['id'], $discounts) ? $discounts[$prod['id']]['code'] : false ?>
					<tr>
						<td><?= $prod['name'] ?></td>
                        <td>$<?= $prod['price'] ?></td>
						<td class="text-center"><input type="text" onchange="update_cart('table.cart')" class="form-control quantity" name="products[<?= $prod['id'] ?>]" value="<?= $prod['quantity'] ?>"></td>
						<td><input name="discount_code" type="text" <?= !empty($exist_discount) ? 'disabled="disabled" value="'.$exist_discount.'"' : '' ?> class="form-control discount_code" data-extension_id="<?= $prod['id'] ?>"><?php if (empty($exist_discount)) { ?><i title="<?= __('Apply discount'); ?>" class="fa fa-check apply_discount" onclick="apply_discount($(this));"></i><?php } ?></td>
						<td><b><?= $exist_discount ? $discounts[$prod['id']]['discount'].'%' : '' ?></b></td>
						<td><b>$<?= $prod['total'] ?></b></td>
						<td class="text-center"><i class="fa fa-times delete_product" onclick="remove_from_cart('<?= $prod['id'] ?>')"></i></td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="4"></td>
					<td class="total">Total: </td>
					<td class="total">$<?= $cart_total ?></td>
					<td></td>
				</tr>
			</thead>
		</table>
		<div class="row">
			<div class="col-md-12 text-right"><a class="btn btn-lg btn-primary" href="invoices/opencart/new_invoice?type=license"><?= __('Checkout process') ?></a></div>
		</div>
	<?php } else { ?>
		<h2><?= __('Your cart is empty<br><a href="/shop">Go to shop</a>') ?></h2>
	<?php } ?>
</div>