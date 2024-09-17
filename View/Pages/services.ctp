<?php echo $this->Html->css([
    //    'pages/services.css?'.date('YmdHis')
]);

$title = __('Services');

$item = array(
    'name' => __('know their testimonials'),
    'params' => array(
    'plugin' => false,
        'controller' => 'pages',
        'action' => 'display',
        'testimonials'
    ),
    'extra_params' => array(
        'class' => '',
    )

);
$testimonial_link = $this->Html->link($item['name'], $item['params'], $item['extra_params']);

$testimonial_link_custom_programming = $this->Html->link('our clients', $item['params'], $item['extra_params']);

$experience_years = (int)date('Y')-2007;

$services = [
    [
        'title' => __('Custom modules'),
        'subtitle' => __('For more than '.$experience_years.' years we have developed different customized modules for our customers, adapting 100% to their needs and achieving extreme quality and performance.'),
        'text' => sprintf(__('Currently <b>%s</b> customers have some of our <b>extensions</b> installed in their stores, %s and discover why you have to choose our services and products!'), $num_sales, $testimonial_link)
    ],
    [
        'title' => __('Full project development'),
        'subtitle' => __('Professional projects, easy to maintain and scalables.<br>Trust our '.$experience_years.' years of experience.'),
        'text' => __('We have <b>developed and participated</b> in numeroues projects with the most advanced and innovative technologies, such as <b>OpenCart</b>, <b>Woocommerce</b>, <b>Prestashop</b>, <b>Wordpress</b>, <b>Laravel</b>, <b>CakePHP</b>, <b>Joomla!</b>, <b>Angular</b>, <b>Ionic</b>.'),
    ],
    [
        'title' => __('Custom programming'),
        'subtitle' => __('Trust us if you want a personal develop, modify some of your products to adapt to your necessities, repair your website or simply do a refactor, we will advice!'),
        'text' => sprintf(__('More than <b>%s years of experience</b> in <b>web development</b>. We have done <b>%s personal develops</b> to %s.'), $experience_years, $num_invoices, $testimonial_link_custom_programming)
    ],
    [
        'title' => __('Opencart version migrations'),
        'subtitle' => __('Are you out of date? trust in our experience and let us your shop migration!'),
        'text' => sprintf(__('We realized more than <b>%s Opencart versions migrations</b>, we know perfectly the <b>main points</b> to realize in each type of migration to guarantee 100%% successful!<br>Opencart 1.5.x to Opencart 3.x migration | Opencart 2.x to Opencart 3.x migration | Opencart 1.x to Opencart 2.x migration'), 300)
    ],

];

?>

<style>
    .card-service{
        border-image: radial-gradient(circle at 0 0, rgba(98, 211, 255, .75) 5%, transparent 50%) 100;
        /*border-color: red;*/
        border-width: 0 0 0 2em;
    }
</style>

<article>
    <header class="jumbotron">
        <h1><?= $title ?></h1>
    </header>

    <div class="container" role="main">

        <?php foreach($services as $service): ?>
            <div class="card card-service mb-4">
                <div class="card-body">
                    <h2>
                        <?= $service['title'] ?> <br>
                        <small><?= $service['subtitle'] ?></small>
                    </h2>
                    <p><?= $service['text'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

</article>

