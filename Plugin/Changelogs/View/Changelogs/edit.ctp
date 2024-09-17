<h1>Edit changelog - <?php echo $extension['Extension']['name']; ?></h1>
<?php 
	echo $this->Form->create('Changelog', array('action' => 'save', 'class' => 'form-horizontal'));
        echo '<input type="hidden" name="data[Changelog][id_extension]" value="'.$extension['Extension']['id'].'">';
    	$inputs = array(
            $this->Form->input('version', array('label' => 'Version')),
            $this->Form->input('text', array('label' => 'Text', 'type' => 'textarea'))
        );

        $this->FormTool->fieldset(
    		array(
    			'inputs' => $inputs
    		)
        );
    $this->FormTool->button('Save', 'save');
?>

<table class="table default">
    <thead>
        <tr>
            <th>Date</th>
            <th>Version</th>
            <th>Text</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($extension['Changelog'] as $key => $changelog) { ?>
            <tr>
                <td><?php echo $changelog['created']; ?></td>
                <td><?php echo $changelog['version']; ?></td>
                <td><?php echo $changelog['text']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>