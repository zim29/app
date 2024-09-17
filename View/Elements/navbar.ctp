<?php echo $this->Html->css('navbar.css'); ?>
<nav id="navbar" class="navbar navbar-expand-lg fixed-top">
    <div class="navbar-content container-fluid d-flex justify-content-center">
        <a class="navbar-brand" href="/"><i id="logo-icon" class="icon-vertical-logo circle-shadow"></i></a>
        <button class="navbar-toggler flex-shrink-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse justify-content-center navbar-collapse" id="navbarNav">
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/products')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/products'); ?>">Products</a>
                </li>
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/services')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/services'); ?>">Services</a>
                </li>
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/blog')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/blog'); ?>">Blog</a>
                </li>
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/open_ticket')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/open_ticket'); ?>">Contact</a>
                </li>
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/my-account')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/my-account'); ?>">My Account</a>
                </li>
                <li class="nav-item <?php echo ($this->request->here == $this->Html->url('/cart')) ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $this->Html->url('/cart'); ?>">
                        <i class="icon-shopping-cart"></i>
                        <?php if($cart_count > 0) { ?>
                            <span class="badge bg-danger"><?= $cart_count; ?></span>
                        <?php } ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php echo $this->Html->script(['navbar.js']) ?>
