<style>
	table ul {
		padding-left: 17px;
	}
	table a.action i {
		font-size: 20px;
	}
</style>
<script>
	var link_add_domain = '<?= Router::url('/', true) ?>invoices/opencart/new_invoice?';
	function add_domain(license_id) {
		var temp_link = link_add_domain+"type=add_domain&license_id="+license_id+'&domain='+$("input#add-domain-"+license_id).val();
		window.open(temp_link, '_blank');
	}
</script>
<article>
	<header class="jumbotron">
		<h1><?= __('Licenses'); ?></h1>
	</header>
	<div class="container theme-showcase" role="main">
		<?php if(empty($licenses)) { ?>
			Not licenses found
		<?php } else { ?>
			<?php foreach ($licenses as $key => $license_by_extension) { ?>
				<br>
				<h3><?= $license_by_extension[0]['extension_name']; ?></h3>

				<table class="table table-striped table-bordered">
					<thead class="thead-light">
						<tr>
							<th>Order ID</th>
							<th>Date added</th>
							<th>Date renew</th>
							<th>Domains assigned</th>
							<th colspan="3">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($license_by_extension as $key2 => $ext) { ?>
							<?php $domains = explode('|',$ext["domain"]); ?>
							<tr>
								<td><?= $ext["order_id"] ?></td>
								<td><?= $ext["date_added"] ?></td>
								<td><?= $ext["date_increase"] ?></td>
								<td>
									<ul>
										<?php foreach ($domains as $dom) { ?>
											<?php if (!empty($dom)) { ?>
												<li><a href="<?= 'https://'.$dom ?>" target="_blank"><?= $dom ?></a></li>
											<?php } ?>
										<?php } ?>
									</ul>
								</td>
								<td><a title="Download" class="action download" href="<?= Router::url('/', true) ?>download-center?download_id=<?= $ext['download_id'] ?>" target="_blank"><i class="retina-gadgets-1497"></i></a></td>
								<td>
									<?php if (!empty($ext['sale_status']['link_renew'])) { ?>
										<a title="Support expired at <?= $ext['sale_status']['expired_date'] ?>, click to renew." class="action renew" href="<?= $ext['sale_status']['link_renew'] ?>" target="_blank"><i class="retina-theessentials-2565"></i></a>
									<?php } else if(!empty($ext['sale_status']['support_end_date'])) { ?>
										<i style="color: #82c95b; font-weight: bold" class="retina-theessentials-2565" title="Support valid until <?= $ext['sale_status']['support_end_date'] ?>"></i>
									<?php } ?>
								</td>
								<td><input type="text" id="add-domain-<?= $ext['order_id'] ?>"name="new_domain" placeholder="Add new domain"><br><a href="javascript:{}" onclick="add_domain('<?= $ext['order_id'] ?>')">New domain</a> </td>
							</tr>
						<?php } ?>
					</tbody>
				</table>


			<?php } ?>
		<?php } ?>
	</div>
</article>
