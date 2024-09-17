<div class="card testimonial-card">
    <div class="card-body text-center">
        <div class="top d-flex justify-content-between align-items-center">
            <?= $this->Html->image('open-quote.svg', ['class' => 'img-fluid']) ?>
            <?= $this->Html->image($image, ['class' => 'img-fluid logo']) ?>
            <?= $this->Html->image('close-quote.svg', ['class' => 'img-fluid']) ?>
        </div>
        <h5 class="fw-bold"><?= h($name) ?></h5>
        <h6 class=""><?= h($position) ?></h6>
        <a class="text-danger lead" href="<?= h($url) ?>" style="font-weight: 500;"><?= h($url) ?></a>
        <p class="text-muted font-italic lead mt-4">"<?= h($testimonial) ?>"</p>
        <div class="testimonial-footer">
            <div class="testimonial-rating d-flex  text-center row mt-4">
                <span class="text-muted col-4 fw-bold"><i class="icon-location mx-2"></i><?= h($country) ?></span>
                <div class="score col-4">
                    <?php for ($x = 0; $x < $rate; $x++): ?>
                        <i class="icon-star "></i>
                    <?php endfor ?>
                </div>
                <small class="text-muted col-4"><i class="icon-calendar mx-2"></i><?= h($date) ?></small>
            </div>
        </div>
    </div>
</div>