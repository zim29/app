<h1>View ticket</h1>
<?php 

	$data_to_view = array(
            'Id:' => $this->data['Ticket']['id'],
            'Date:' => date('d-m-Y H:i:s', strtotime($this->data['Ticket']['created'])),
            'Customer email:' => '<a href="mailto:"'.$this->data['Ticket']['email'].'">'.$this->data['Ticket']['email'].'</a>',
            'Type:' => $this->data['Ticket']['type']
    );

    if (!empty($this->data['Extension']))
        $data_to_view['Extension:'] = $this->data['Extension']['name'];

    $data_to_view['Subject:'] = $this->data['Ticket']['subject'];
    $data_to_view['Text:'] = $this->data['Ticket']['text'];
    $data_to_view['Conections:'] = $this->data['Ticket']['conections'];

    $this->FormTool->viewTable($data_to_view);
    $this->FormTool->button('Cancel', 'cancel');
?>