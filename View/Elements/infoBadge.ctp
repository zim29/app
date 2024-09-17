<div class="flex-column">
    <div class="card bg-dark text-light shadow-lg custom-gradient">
        <div class="card-body d-flex flex-row justify-content-center gap-3 mt-2">
            <div>
                <?php if (isset($iconFont)): ?>
                    <i class="<?= h($iconFont); ?>"></i>
                <?php endif; ?>
                <?= isset($iconHtml) ? $iconHtml : ''; ?>
            </div>
            <div>
                <h5 class="fw-bold text-white text-start"><?= h($title); ?></h5>
                <p class="text-white text-start"><?= h($description); ?></p>
            </div>
        </div>
    </div>
</div>
