<h1>Extensions</h1>

<?php $this->FormTool->toolbar($buttons); ?>

<?php if ( count($extensions) >= 1 ){ ?>
    <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo $this->Paginator->sort('Extension.name', 'Name'); ?></th>
              <th><?php echo $this->Paginator->sort('Extension.system', 'System'); ?></th>
            <th><?php echo $this->Paginator->sort('Extension.created', 'Date added'); ?></th>
            <th class="actions">Edit</th>
            <!--<th class="actions">Delete</th>-->
          </tr>
        </thead>
        <tbody>
        	<?php foreach ($extensions as $key => $ext) {
        		echo '<tr>';
        			echo '<td>'.($key+1).'</td>';
        			echo '<td>'.$ext['Extension']['name'].'</td>';
        			echo '<td>'.$ext['Extension']['system'].'</td>';
        			echo '<td>'.date('d-m-Y H:i:s', strtotime($ext['Extension']['created'])).'</td>';
        			echo '<td class="actions">'.$this->FormTool->buttonIcon('edit', $ext['Extension']['id']).'</td>';
        			//echo '<td class="actions">'.$this->FormTool->buttonIcon('delete', $ext['Extension']['id']).'</td>';
        		echo '</tr>';
        	} ?>
        </tbody>
    </table>
<?php }
else
{
	echo 'No extension founds';
}
?>