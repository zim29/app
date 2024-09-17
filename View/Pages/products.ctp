<?= $this->element('header', [
    'title' => 'Extensions Shop',
    'text' => 'For OpenCart lovers, we create extensions with a high level of acceptance.',
]); ?>
<div class="top-extentions mt-5">
    <section class="container">
        <div class="row justify-content-center">
            <?php foreach ($extensions as $product) : ?>
                <?php
                $data = [
                    'icon' => $product['id'],
                    'name' => $product['title_main'],
                    'users' => $product['num_clients'],
                    'description' => $product['description'],
                    'price' => '$' . number_format($product['price'], 2)
                ];
                ?>
                <div class="col-12 col-md-4 mb-4">
                    <?= $this->element('productCard', $data); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?= $this->Html->script('add_to_cart.js'); ?>