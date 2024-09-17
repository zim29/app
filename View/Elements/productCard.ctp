<div class="card shadow-lg p-4 h-100" style="border-radius: 1rem;">
    <div class="text-center mb-3">

    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?= $this->element($icon); ?>
        <div class="active-users">
            <h3 class="fw-bold mb-0 pt-2"><?= h($users) ?></h3>
            <p class="mb-0 pb-2" style="font-weight: 550;">Active users</p>
        </div>
    </div>
    <div class="text-center mt-3">
        <h4 class="fw-bold"><?= h($name) ?></h4>
        <p class="" style="font-size: 12px;"><?= $description ?></p>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-auto">
        <div class="mx-2 fs-4 fw-bold text-danger"><?= h($price) ?></div>
        <?php if (!in_array($icon, array_column($cart_products, 'id'))) { ?>
            <button class="btn btn-danger text-white d-flex align-items-center gap-2 add-to-cart"
                style="width: 8rem !important;" data-product-id="<?= $icon ?>">
                <span class="text-center text-white fw-bold">Add to cart</span>
                <i class="icon-shopping-cart text-white fw-bold"></i>
            </button>
        <?php } ?>
        <?php if (in_array($icon, array_column($cart_products, 'id'))) { ?>
        <button class="btn btn-danger text-white d-flex align-items-center justify-content-center gap-2 remove-from-cart"
            style="width: 8rem !important;" data-product-id="<?= $icon ?>">
            <span class="text-center text-white fw-bold">Added</span>
            <i class="bi bi-check text-white fw-bold text-center" style="font-size: 1.6rem;"></i>
        </button>
        <?php } ?>
    </div>
</div>