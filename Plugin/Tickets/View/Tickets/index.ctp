<h1>Tickets</h1>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
<?php if ( count($tickets) >= 1 ){ ?>
  <?php echo $this->element('pagination'); ?>

  <table class="table table-striped">
      <thead>
        <tr>
          <th><?php echo $this->Paginator->sort('Ticket.id', '#'); ?></th>
          <th><?php echo $this->Paginator->sort('Ticket.type', 'Type'); ?></th>
          <th><?php echo $this->Paginator->sort('Sale.extension_name', 'Extension'); ?></th>
			<th class="actions">OC Version</th>
          <th><?php echo $this->Paginator->sort('Ticket.email', 'Email'); ?></th>
          <th><?php echo $this->Paginator->sort('Ticket.created', 'Date added'); ?></th>
            <TH>*</TH>
            <th>Responded</th>
            <th class="actions"><?php echo $this->Paginator->sort('User.id', 'Employ'); ?></th>
          <th class="actions">Answered</th>
          <th class="actions">State</th>
          <th class="actions">View</th>
          <th class="actions">Resend</th>
        </tr>
      </thead>
      <tbody>
      	<?php foreach ($tickets as $key => $ext) {
      		echo '<tr>';
      			echo '<td>'.$ext['Ticket']['id'].'</td>';
      			echo '<td>'.$ext['Ticket']['type'].'</td>';

            echo '<td >'.(!empty($ext['Extension']['name_initials']) ? '<span data-html="true" data-toggle="tooltip" title="'.$ext['Extension']['name'].'">'.$ext['Extension']['name_initials'].'</span>' : '').'</td>';
			echo '<td >'.(!empty($ext['Sale']['system_version']) ? implode("<br>", explode("|", $ext['Sale']['system_version'])) : '').'</td>';
            echo '<td>'.(!empty($ext['Ticket']['email']) ? '<a title="'.$ext['Ticket']['email'].'" href="mailto:'.$ext['Ticket']['email'].'">'.$ext['Ticket']['email'].'</a>' : '').'</td>';

      			echo '<td>'.date('d/m/Y H:i:s', strtotime($ext['Ticket']['created'])).'</td>';
      			echo '<td>'.$ext['Ticket']['number_of_tickets'].'</td>';
      			echo '<td>'.$ext['Ticket']['responded_in'].'</td>';
            echo '<td'.(!empty($ext['User']['username']) ? '>'.$ext['User']['username'] : ' class="actions ajax">'.$this->FormTool->buttonIcon('assign', $ext['Ticket']['id'])).'</td>';
                  if ($ext['Ticket']['answered'])
                  {
                      echo '<td class="actions ajax">'.$this->FormTool->buttonIcon('answered', $ext['Ticket']['id']).'</td>';
                  }
                  else
                  {
                      echo '<td class="actions ajax">'.$this->FormTool->buttonIcon('noanswered', $ext['Ticket']['id']).'</td>';
                  }

                  if ($ext['Ticket']['solved'])
                  {
                      echo '<td class="actions ajax">'.$this->FormTool->buttonIcon('solve', $ext['Ticket']['id']).'</td>';
                  }
                  else
                  {
                      echo '<td class="actions ajax">'.$this->FormTool->buttonIcon('nosolve', $ext['Ticket']['id']).'</td>';
                  }


                  echo '<td class="actions">'.$this->FormTool->buttonIcon('view', $ext['Ticket']['id']).'</td>';
                  echo '<td class="actions ajax">'.$this->FormTool->buttonIcon('resend', $ext['Ticket']['id']).'</td>';
      		echo '</tr>';
      	} ?>
      </tbody>
  </table>

  <?php echo $this->element('pagination'); ?>

<?php }
else
{
	echo 'No Tickets found';
}
?>
