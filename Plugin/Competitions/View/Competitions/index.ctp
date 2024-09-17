<h1>Competition</h1>
<?php $link_index = Router::url(['plugin' => 'competitions', 'controller' => 'competitions', 'action' => 'index']); ?>
<form action="<?= $link_index ?>" role="form" id="search" class="form-horizontal" method="post" accept-charset="utf-8">  
  <div class="row">
    <div class="col-md-4">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Date from" name="search[from]" value="<?= !empty($this->request->data['search']['from']) ? $this->request->data['search']['from'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Date to" name="search[to]" value="<?= !empty($this->request->data['search']['to']) ? $this->request->data['search']['to'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <a href="javascript:{}" onclick="jQuery(this).closest('form').submit();" class="btn btn-lg btn-success save" style="float: right;"><i class="fa fa-search"></i>Filter</a>
    </div>
  </div>
</form>
<br><br>
<table class="table">
	<thead>
		<tr>
			<td>Developer</td>
			<td>Extension</td>
			<td>Current price</td>
			<td>Num sales</td>
			<td>Total</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($sales as $key => $sa) { ?>
			<tr>
				<td><?= $sa['Competition']['developer']; ?></td>
				<td><a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=<?= $sa['CompetitionSale']['id_extension']; ?>" target="_blank"><?= $sa['CompetitionExtension']['name']; ?></a></td>
				<td>$<?= $sa['CompetitionSale']['current_price']; ?></td>
				<td><?= $sa[0]['total_sales']; ?></td>
				<td><?= number_format($sa[0]['total'],2); ?>€</td>
			</tr>
		<?php } ?>
	</tbody>
</table>