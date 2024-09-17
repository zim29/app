<?php echo $this->Html->css(
    array( 
    'Invoices.Invoice/index'
    )
); ?>

<h1>Invoices</h1>

<?php
$user = $this->Session->read('Auth.User');
$role = $user['role'];
?>
<?= $role != 'marketing' ? $this->FormTool->toolbar($buttons) : ''; ?>

<?php $link_excel_generator = Router::url(['plugin' => 'invoices', 'controller' => 'invoices', 'action' => 'generate_excel']); ?>

<?php
if($role != 'marketing') {
?>
<form action="<?= $link_excel_generator ?>" role="form" id="generate_excel" class="form-horizontal" method="post" accept-charset="utf-8">
  <div class="row">
    <div class="col-md-7"><h3>Genate excel</h3></div>
    <div class="col-md-2"><input class="form-control" value="<?= date('Y-m') ?>" name="date"></div>
    <div class="col-md-2"><?= $this->FormTool->button('Generate excel', 'save'); ?></div>
  </div>
</form>
<?php } ?>
<?php $link_index = Router::url(['plugin' => 'invoices', 'controller' => 'invoices', 'action' => 'index']); ?>

<form action="<?= $link_index ?>" role="form" id="search" class="form-horizontal" method="post" accept-charset="utf-8">
  <div class="row">
    <div class="col-md-12"><h3>Filter - Total filter: <b><?= $total ?></b>€</h3></div>
    <div class="col-md-2">
      <select class="form-control selectpicker" name="search[payment_method][]" multiple>
        <?php foreach ($payment_methods as $key => $value) { ?>
          <option <?= !empty($this->request->data['search']['payment_method']) && in_array($key, $this->request->data['search']['payment_method']) ? 'selected="selected" ': '';?> value="<?= $key ?>"><?= $value ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-control selectpicker" name="search[type][]" multiple>
        <?php foreach ($types as $key => $value) { ?>
          <option <?= !empty($this->request->data['search']['type']) && in_array($key, $this->request->data['search']['type']) ? 'selected="selected" ': '';?> value="<?= $key ?>"><?= $value ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control" placeholder="Description" name="search[description]" value="<?= !empty($this->request->data['search']['description']) ? $this->request->data['search']['description'] : ''; ?>">
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control" placeholder="Email" name="search[customer_email]" value="<?= !empty($this->request->data['search']['customer_email']) ? $this->request->data['search']['customer_email'] : ''; ?>">
    </div>
    <div class="col-md-1">
      <input type="text" class="form-control" placeholder="$from" name="search[total_from]" value="<?= !empty($this->request->data['search']['total_from']) ? $this->request->data['search']['total_from'] : ''; ?>">
    </div>
    <div class="col-md-1">
      <input type="text" class="form-control" placeholder="$to" name="search[total_to]" value="<?= !empty($this->request->data['search']['total_to']) ? $this->request->data['search']['total_to'] : ''; ?>">
    </div>
    <div class="col-md-1">
      <input type="text" class="form-control" placeholder="Number" name="search[number]" value="<?= !empty($this->request->data['search']['number']) ? $this->request->data['search']['number'] : ''; ?>">
    </div>
    <div class="col-md-1">
      <input type="text" class="form-control" placeholder="Li.ID" name="search[license_id]" value="<?= !empty($this->request->data['search']['license_id']) ? $this->request->data['search']['license_id'] : ''; ?>">
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-2">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Payed from" name="search[date_payed_from]" value="<?= !empty($this->request->data['search']['date_payed_from']) ? $this->request->data['search']['date_payed_from'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Payed to" name="search[date_payed_to]" value="<?= !empty($this->request->data['search']['date_payed_to']) ? $this->request->data['search']['date_payed_to'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Created from" name="search[created_from]" value="<?= !empty($this->request->data['search']['created_from']) ? $this->request->data['search']['created_from'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group date">
        <input type="text" class="form-control datepicker" placeholder="Created to" name="search[created_to]" value="<?= !empty($this->request->data['search']['created_to']) ? $this->request->data['search']['created_to'] : '';?>">
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-th"></span>
        </div>
      </div>
    </div>
    <div class="col-md-1">
      <div class="checkbox">
        <label><input type="checkbox" name="search[unpayed]" <?= !empty($this->request->data['search']['unpayed']) ? 'checked="checked" ': '';?>>Unpayed</label>
      </div>
    </div>
    <div class="col-md-1">
      <div class="checkbox">
        <label><input type="checkbox" name="search[payed]" <?= !empty($this->request->data['search']['payed']) ? 'checked="checked" ': '';?>>Payed</label>
      </div>
    </div>
    <div class="col-md-1">
      <div class="checkbox">
        <label><input type="checkbox" name="search[bank_transfer_waiting]" <?= !empty($this->request->data['search']['bank_transfer_waiting']) ? 'checked="checked" ': '';?>>Bank confirm</label>
      </div>
    </div>
    <div class="col-md-1">
      <div class="checkbox">
        <label><input type="checkbox" name="search[todo]" <?= !empty($this->request->data['search']['todo']) ? 'checked="checked" ': '';?>>To do</label>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <a href="javascript:{}" onclick="jQuery(this).closest('form').submit();" class="btn btn-lg btn-success save" style="float: right;"><i class="fa fa-search"></i>Filter</a>
    </div>
  </div>
</form>

<?php if ( count($invoices) >= 1 ){ ?>
    <table class="table table-striped">
		<thead>
			<tr>
        <th><?php echo $this->Paginator->sort('Invoice.created', 'Date'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.number', 'Number'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.payment_method', 'P. Method'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.type', 'Type'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.description', 'Description'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.total', 'Total'); ?></th>
        <th><?php echo $this->Paginator->sort('Invoice.payed_date', 'Pay Date'); ?></th>
        <th class="actions">Send invoice</th>
        <th class="actions">PDF</th>
				<th class="actions">Payed</th>
        <th class="actions">Solved</th>
        <th class="actions">Clone</th>
				<th class="actions">Edit</th>
				<th class="actions">Delete</th>
			</tr>
		</thead>
        <tbody>
        	<?php foreach ($invoices as $key => $inv) {
        		echo '<tr>';
              echo '<td>'.$inv['Invoice']['created'].'</td>';
              echo '<td>'.$inv['Invoice']['number'].'</td>';
              echo '<td>'.$inv['Invoice']['payment_method'].'</td>';
              echo '<td>'.$inv['Invoice']['type'].'</td>';
              echo '<td>'.$inv['Invoice']['description'].'</td>';
              echo '<td>$'.$inv['Invoice']['total'].'<br>('.(number_format($inv['Invoice']['total']*$inv['Invoice']['currency_euro_value'], 2)).'€)</td>';
              echo '<td>'.$inv['Invoice']['payed_date'].'</td>';
              echo '<td class="actions ajax">'.($this->FormTool->buttonIcon($inv['Invoice']['pdf_send_date'] == '0000-00-00 00:00:00' ? 'pdf_send' : 'pdf_resend', $inv['Invoice']['id'])).'</td>';
              echo '<td class="actions">'.($this->FormTool->buttonIcon('pdf_download', $inv['Invoice']['id'])).'</td>';
          		echo '<td class="actions ajax">'.($this->FormTool->buttonIcon($inv['Invoice']['state'] == 'Payed' ? 'nopayed' : 'payed', $inv['Invoice']['id'])).'</td>';
              echo '<td class="actions ajax">'.($this->FormTool->buttonIcon($inv['Invoice']['solved'] ? 'solve' : 'nosolve', $inv['Invoice']['id'])).'</td>';
              echo '<td class="actions">'.$this->FormTool->buttonIcon('clone_invoice', $inv['Invoice']['id']).'</td>';
          		echo '<td class="actions">'.$this->FormTool->buttonIcon('edit', $inv['Invoice']['id']).'</td>';
        			echo '<td class="actions">'.$this->FormTool->buttonIcon('delete', $inv['Invoice']['id']).'</td>';
        		echo '</tr>';
        	} ?>
        </tbody>
    </table>
<?php }
else
{
	echo 'No invoices founds';
}
?>