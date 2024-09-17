<h1>Sales "Waiting for Proof of ID"</h1>

<?php echo $this->Html->script(
    array(
    'Sales.index_waiting'
    )
); ?>

<?php if ( count($sales) >= 1 ){ ?>  
  <table class="table table-striped">
      <thead>
        <tr>
          <th><?php echo $this->Paginator->sort('Sale.order_id', 'License id'); ?></th>
          <th><?php echo $this->Paginator->sort('Sale.date_added', 'Date purchase'); ?></th>
          <th><?php echo $this->Paginator->sort('Extension.name', 'Extension'); ?></th>
          <th><?php echo $this->Paginator->sort('Sale.buyer_username', 'Username'); ?></th>
            <th><?php echo $this->Paginator->sort('Sale.sub_total', 'Subtotal'); ?></th>
            <th><?php echo $this->Paginator->sort('Sale.commission', 'Fee'); ?></th>
          <th><?php echo $this->Paginator->sort('Sale.total', 'Total'); ?></th>
          <th class="actions">Complete</th>
        </tr>
      </thead>
      <tbody>
      	<?php foreach ($sales as $key => $sale) {
      		echo '<tr>';
      			echo '<td>'.$sale['Sale']['order_id'].'</td>';
      			echo '<td>'.date('d-m-Y H:i:s', strtotime($sale['Sale']['date_added'])).'</td>';
            echo '<td>'.$sale['Extension']['name'].'</td>';
            echo '<td>'.$sale['Sale']['buyer_username'].'</td>';
            echo '<td>'.($sale['Sale']['order_status'] == 'pending_validate' ? '<input name="sub_total" placeholder="Sub total" value="">' : '').'</td>';
            echo '<td>'.($sale['Sale']['order_status'] == 'pending_validate' ? '<input name="commission" placeholder="Fee" value="">' : '').'</td>';
            echo '<td>'.($sale['Sale']['order_status'] == 'pending_validate' ? '<input name="total" placeholder="Total" value="">' : '$'.number_format($sale['Sale']['total'], 2, '.', '')).'</td>';
            echo '<td class="actions ajax" title="Click to complete">'.($sale['Sale']['order_status'] == 'pending_validate' ? '<a href="javascript:{}" ><i style="font-size: 25px; margin-top: 14px;" onclick="confirm_license(\''.$sale['Sale']['order_id'].'\', $(this))" class="fa fa fa-minus-square no"></i></a>' : $this->FormTool->buttonIcon('complete', $sale['Sale']['order_id'])).'</td>';
      		echo '</tr>';
      	} ?>
      </tbody>
  </table>
<?php }
else
{
	echo 'No sales found';
}
?>