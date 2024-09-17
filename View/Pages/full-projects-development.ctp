<?php echo $this->Html->css(
    array( 
//    'pages/services.css?'.date('YmdHis')
    )
); ?>

<article>
    <header class="jumbotron">
        <h1><?= __('Opencart Prestashop Wordpress Full Projects') ?></h1>
    </header>

    <?php
    $item = array(
        'name' => __('Contact us!'),
        'params' => array(
            'plugin' => false,
            'controller' => 'pages',
            'action' => 'display',
            'open_ticket'
        ),
        'extra_params' => array(
            'class' => 'btn btn-lg btn-primary'
        )
    );
    $contact_link = $this->Html->link($item['name'], $item['params'], $item['extra_params']);

    $experience_years = (int)date('Y')-2007;
    ?>

    <div class="container theme-showcase" role="main">
        <div class="devman_image_container"><?= $this->Html->image('pages/services/devman.jpg', array('class' => 'img-fluid')); ?></div>
        <div class="service full_projects">
            <h2 class="title"><?= __('FULL PROJECT DEVELOPMENT') ?></h2>
            <span class="icon"><?= $this->Html->image('pages/services/icon_full_projects.jpg', array('class' => 'full_projects animated img-responsive')); ?></span>
            <span class="title_secondary"><?= sprintf(__('Professional projects, easy to maintain and scalables.<br>Trust our %s years of experience.'), $experience_years) ?></span>
            <span class="text"><?= sprintf(__('We have <b>developed and participated</b> in numerous projects of the most advanced and innovative technologies, such as <b>Opencart</b>, <b>Woocommerce</b>, <b>Prestashop</b>, <b>Wordpress</b>, <b>Laravel</b>, <b>Cakephp</b>, <b>Joomla!</b>, <b>Angular</b>, <b>Ionic</b>.')) ?></span>
            <?= $contact_link ?>
        </div>
    </div>
</article>