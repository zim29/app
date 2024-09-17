<h1>Changelogs</h1>

<?php $this->FormTool->toolbar($buttons); ?>

<?php if ( count($extensions) >= 1 ){ ?>
    <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
              <th>System</th>
            <th><?php echo $this->Paginator->sort('Extension.name', 'Extension'); ?></th>
            <th>Last version</th>
            <th class="actions">Edit</th>
          </tr>
        </thead>
        <tbody>
        	<?php foreach ($extensions as $key => $ext) {
        		echo '<tr>';
        			echo '<td>'.($key+1).'</td>';
        			echo '<td>'.$ext['Extension']['system'].'</td>';
        			echo '<td>'.$ext['Extension']['name'].'</td>';
                    echo '<td>'.(!empty($ext['Changelog']) ? $ext['Changelog'][0]['version']: '').'</td>';
        			echo '<td class="actions">'.$this->FormTool->buttonIcon('edit', $ext['Extension']['id']).'</td>';
        		echo '</tr>';
        	} ?>
        </tbody>
    </table>
<?php }
else
{
	echo 'No changelog founds';
}
?>