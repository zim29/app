<?php
echo $this->Html->css([
    '/v2/css/testimonials.css',
]);
echo $this->Html->script(
    array(
    'pages/testimonials.js?'.date('YmdHis')
    )
);

$labelsGenerator = function ($testimonial) {
    $flag = $this->Html->image('pages/testimonials/flags/' . strtolower($testimonial['TestimonialByCountry']['iso_code_2']) . '.png', array('alt' => 'Flag ' . $testimonial['TestimonialByCountry']['name']));
    $country = $testimonial['TestimonialByCountry']['name'];
    $count = $testimonial['TestimonialByCountry']['number'];

    $label = "{flag} {country} ({count})";
    $label = str_replace('{flag}', $flag, $label);
    $label = str_replace('{country}', $country, $label);
    $label = str_replace('{count}', $count, $label);
    return $label;
};

$ratingGenerator = function ($rate) {
    $stars_full = array_fill(0, $rate, '<i class="fa fa-star"></i>');
    $stars_empty = array_fill(0, 5 - $rate, '<i class="fa fa-star-o"></i>');
    return implode('', array_merge($stars_full, $stars_empty));
}

?>

<article role="main">

    <header class="jumbotron text-center">
        <h1>
            <?= __('Testimonials') ?><br/>
            <small><?= __('100% real cases') ?></small>
        </h1>
    </header>

    <div class="container">
        <ul class="nav nav-pills nav-categories countries">
            <li class="nav-item active"  onclick="show_testimonials('', $(this))">
                <a href="javascript:{}" class="nav-link">
                    All testimonials (<?= count($testimonials) ?>)
                </a>
            </li>
            <?php foreach ($testimonials_by_country as $key => $testimonial): ?>
                <li class="nav-item" onclick="show_testimonials('<?= $testimonial['TestimonialByCountry']['iso_code_2'] ?>', $(this))">
                    <?= $this->Html->link($labelsGenerator($testimonial), 'javascript:{}', [
                        'escape' => false,
                        'class' => 'nav-link',
                        'data-country' => $testimonial['TestimonialByCountry']['iso_code_2'],
                    ]) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <ul class="nav nav-pills nav-categories">
            <li class="nav-item sort_date active"  onclick="sort_testimonials('best', $(this))">
                <a href="javascript:{}" class="nav-link"><i class="fa-heart fa"></i>&nbsp;&nbsp;The best</a>
            </li>
            <li class="nav-item sort_date"  onclick="sort_testimonials('desc', $(this))">
                <a href="javascript:{}" class="nav-link"><i class="fa-calendar-o fa"></i>&nbsp;&nbsp;Most recent</a>
            </li>
            <li class="nav-item sort_date"  onclick="sort_testimonials('asc', $(this))">
                <a href="javascript:{}" class="nav-link"><i class="fa-calendar-o fa"></i>&nbsp;&nbsp;Oldest</a>
            </li>
        </ul>
    </div>

    <div class="testimonials-container">
        <?php foreach ($testimonials as $key => $testimonial): ?>
            <section class="testimonial <?= ($key%2 == 0 ? 'par' : '') ?>" data-sort_best="<?= $testimonial['Testimonial']['order']; ?>" data-sort_date="<?= strtotime($testimonial['Testimonial']['created']); ?>" data-country_iso_code="<?= $testimonial['Country']['iso_code_2'] ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-3">
                            <?= $this->Html->image('/images/testimonials/' . $testimonial['Testimonial']['image'], ['class' => 'img-responsive']); ?>
                        </div>
                        <div class="col-9">
                            <h2>
                                <?= $testimonial['Testimonial']['name'] ?><br>
                                <?= $testimonial['Testimonial']['position'] ?><br>
                            </h2>
                            <?php if ($testimonial['Testimonial']['url'] != 'http://') { ?>
                            <a rel="nofollow" class="website" href="<?= $testimonial['Testimonial']['url'] ?>" target="_new"><?= $testimonial['Testimonial']['url'] ?></a>
                            <?php } ?>

                            <p><?= nl2br($testimonial['Testimonial']['testimonial']); ?></p>
                            <p>
                                <?= __('From:') ?> <strong><?= $testimonial['Country']['name'] ?></strong><br>
                                <?= __('Rating:') ?> <span><?= $ratingGenerator($testimonial['Testimonial']['rate']) ?></span>
                                <?php if (!empty($testimonial['Extension']['name'])) { ?>
                                    <br><?= __('Extension:') ?> <span><?= $testimonial['Extension']['name'] ?></span>
                                <?php } ?>
                                <br><?= __('Date:') ?> <span><?= date("F j, Y", strtotime($testimonial['Testimonial']['created'])); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

</article>