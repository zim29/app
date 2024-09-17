<script type="text/javascript">
    jQuery(document).on('ready', function(){
        jQuery('input#UserPassword').on("keypress", function(event)  
        {             
          if (event.keyCode === 13) {
            jQuery('form#UserLoginForm').submit();
          }
        });
    });
</script>
<?php 
	echo $this->Form->create('User', array('class' => 'form-horizontal'));

    	$inputs = array(
            $this->Form->input('username', array('label' => 'Username')),
            $this->Form->input('password', array('label' => 'Password'))
        );

        $this->FormTool->fieldset(
    		array(
    			'title' => 'Please enter your username and password', 
    			'inputs' => $inputs
    		)
        );

    $this->FormTool->button('Login', 'login');
?>