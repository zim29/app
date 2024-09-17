<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title_for_layout; ?></title>
    <?php echo $this->Html->meta('icon'); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <!-- Latest compiled and minified CSS -->
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

  	<!-- Optional theme -->
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

    <!-- Icons -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- This theme -->
    <?php echo $this->Html->css(
        array( 
          'layouts/administration/theme',
          'layouts/administration/navbar',
          'layouts/administration/toolbar',
          'layouts/administration/table_index',
          'layouts/administration/bootstrap-select/bootstrap-select',
          'ajax_loading',
          '/libraries/datepicker/css/bootstrap-datepicker.min',
          '/libraries/selectpicker/css/bootstrap-select.min',
        )
      ); ?>

    <?php echo $this->Html->script(
        array( 
          'layouts/administration/table_index',
          'layouts/administration/forms_configuration',
          'layouts/administration/bootstrap-select/bootstrap-select',
          'forms_general',
          'ajax_loading',
          'layouts/administration/ajax_button_actions',
          '/libraries/datepicker/js/bootstrap-datepicker.min',
          '/libraries/selectpicker/js/bootstrap-select.min',
        )
      ); ?>
    
    <script type="text/javascript">
      $( document ).ready(function() {
        $('.datepicker').datepicker({
          weekStart: 1,
          format: 'yyyy-mm-dd'
        });
        $('.selectpicker').selectpicker({});

      });
    </script>
  	<!-- Latest compiled and minified JavaScript -->
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script type="text/javascript">
      jQuery( document ).ready(function() {
        var button_activate="";
        <?php if($this->params['plugin'] == "tickets") 
          echo 'button_activate="tickets";';
        ?>
        <?php if($this->params['plugin'] == "extensions") 
          echo 'button_activate="extensions";';
        ?>
        <?php if($this->params['plugin'] == "invoices") 
          echo 'button_activate="invoices";';
        ?>
        <?php if($this->params['plugin'] == "sales" && $this->params['controller'] == 'sales') 
          echo 'button_activate="sales";';
        ?>
        <?php if($this->params['plugin'] == "sales" && $this->params['controller'] == 'sales_waiting') 
          echo 'button_activate="sales_waiting";';
        ?>
        <?php if($this->params['plugin'] == "changelogs") 
          echo 'button_activate="changelogs";';
        ?>
        <?php if($this->params['plugin'] == "competitions") 
          echo 'button_activate="competitions";';
        ?>
        if (button_activate != "")
          jQuery('ul.navbar-nav li.'+button_activate).addClass('active');

        jQuery('.selectpicker').selectpicker();
      });
    </script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script type="text/javascript">
        var path = "<?php echo Router::url("/", false); ?>";
        var plugin = "<?php echo $this->params['plugin'] ?>";
        var controller = "<?php echo $this->params['controller'] ?>";
        var action = "<?php echo $this->params['action'] ?>";
    </script> 
  </head>
  <body role="document">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">

        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="#" class="navbar-brand"><i class="fa fa-home"></i>OQE Admin</a>
        </div>

        <div class="navbar-collapse collapse">
          <?php if ($this->Session->read('Auth.User')){
              $user = $this->Session->read('Auth.User');
              $role = $user['role'];
              ?>
            <ul class="nav navbar-nav">
                <?php if(in_array($role, array('king', 'marketing'))) { ?>
                    <li class="invoices"><?php echo $this->Html->link( "Invoices",   array('plugin' => 'invoices', 'controller'=>'invoices', 'action'=>'index') ); ?></li>
                <?php } ?>
                <?php if(!in_array($role, array('marketing'))) { ?>
                    <li class="sales"><?php echo $this->Html->link( "Sales",   array('plugin' => 'sales', 'controller'=>'sales', 'action'=>'index') ); ?></li>
                <?php } ?>

                <?php if(!in_array($role, array('marketing', 'employ-russia'))) { ?>
                    <li class="sales_waiting"><?php echo $this->Html->link( "Sales waiting (".$this->Session->read('waiting_extensions_count').")",   array('plugin' => 'sales', 'controller'=>'sales_waiting', 'action'=>'index') ); ?></li>
                <?php } ?>

                <?php if(!in_array($role, array('marketing'))) { ?>
                    <li class="tickets"><?php echo $this->Html->link( "Tickets",   array('plugin' => 'tickets', 'controller'=>'tickets', 'action'=>'index') ); ?></li>
                <?php } ?>
                <?php if(in_array($role, array('king', 'employ'))) { ?>
                    <li class="extensions"><?php echo $this->Html->link( "Extensions",   array('plugin' => 'extensions', 'controller'=>'extensions', 'action'=>'index') ); ?></li>
                    <li class="changelogs"><?php echo $this->Html->link( "Changelogs",   array('plugin' => 'changelogs', 'controller'=>'changelogs', 'action'=>'index') ); ?></li>
                <?php } ?>
                <?php if(in_array($role, array('king'))) { ?>
                    <li class="competitions"><?php echo $this->Html->link( "* Competitions",   array('plugin' => 'competitions', 'controller'=>'competitions', 'action'=>'index') ); ?></li>
                <?php } ?>
            </ul>
          <?php } ?>
          <ul class="nav navbar-nav navbar-right">
              <?php
                if($this->Session->check('Auth.User')){
                    echo '<li>'.$this->Html->link( "<i class='fa fa-power-off'></i>Logout",   array('plugin' => false, 'controller'=>'users', 'action'=>'logout'), array('escape' => false) ).'</li>';
                }else{
                  echo '<li>'.$this->Html->link( "<i class='fa fa-user'></i>Login",   array('plugin' => false, 'controller'=>'users', 'action'=>'login'), array('escape' => false) ).'</li>';
                }
              ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="container theme-showcase" role="main">
      <?php echo $this->Session->flash(); ?>

      <?php echo $this->fetch('content'); ?>
    </div>
    
  </body>
</html>