<?php echo $this->Html->css('home.css'); ?>
<?php echo $this->Html->script('home.js'); ?>
<div class="hero">
    <section class="container">
        <div class="row d-flex flex-md-row flex-column">
            <div class="col-md-5-custom z-index-fix order-md-0 order-1">
                <h1 class="display-6 fw-bold text-uppercase text-start compressed-text main-text">Cutting-Edge</h1>
                <h1 class="display-6 fw-bold text-uppercase text-start compressed-text main-text">Extensions &</h1>
                <h1 class="display-6 fw-bold text-uppercase text-start compressed-text main-text">Expert Services</h1>
                <h1 class="display-6 text-start stretched-text main-text">For OpenCart Stores</h1>
                <div class="specs">
                    <p class="main-text lead text-start">Optimize your e-commerce with our <span class="fw-bold">extensions</span>
                        and <span class="fw-bold">modules</span>, enhancing every
                        aspect of
                        your store.</p>
                    <p class="main-text lead text-start">With over <span class="fw-bold">a decade of experience</span> and
                        <span class="fw-bold">trusted OpenCart partners</span>
                        since 2012. we offer unique <span class="fw-bold">customizations, full-scale
                            developments</span> tailored to your needs and seamless <span
                            class="fw-bold">migrations</span>.
                    </p>
                    <p class="main-text lead text-start"><span class="fw-bold">From start to finish</span>, we support your
                        online journey.</p>
                    <p class="main-text lead text-start"><span class="fw-bold">Get in touch today and transform your online
                            store! </span></p>
                </div>
                <a href="open_ticket?option=Personal%20develop" class="d-flex align-items-center justify-content-center btn btn-danger mt-5 fw-bold px-4">Contact us <i class="icon-phone"></i></a>

            </div>
            <div class="col-md-7-custom order-md-1 order-0">
                <?php echo $this->Html->image('computer_pc_market_connection.svg', ['alt' => 'My Logo', 'class' => 'opencart-cellphone-image']); ?>
            </div>
        </div>
        <div class="expertise-summary rom text-center d-flex flex-md-row flex-column mt-sm-5 gap-5 justify-content-center">
            <?= $this->element('infoBadge', [
                'iconHtml' => '<i class="icon-client-communication"><i class="path1"></i><i class="path2"></i><i class="path3"></i><i class="path4"></i></i>',
                'title' => '+ 16.500',
                'description' => 'clients'
            ]);
            ?>
            <?= $this->element('infoBadge', [
                'iconHtml' => '<i class="icon-badge"><i class="path1"></i><i class="path2"></i><i class="path3"></i><i class="path4"></i></i>',
                'title' => '+ 10 years',
                'description' => 'of experience'
            ]);
            ?>
            <?= $this->element('infoBadge', [
                'iconHtml' => '<i class="icon-cog-edit"><i class="path1"></i><i class="path2"></i><i class="path3"></i></i>',
                'title' => '+ 7.600',
                'description' => 'custom develops'
            ]);
            ?>

        </div>
    </section>
</div>
<br>
<div class="top-extentions">
    <div class="container  mb-5 d-flex justify-content-center flex-column">
        <h2 class="fw-bold text-center">Discover Our Best Rated Extensions</h2>
        <p class="main-text lead mx-auto text-center">Explore our top-rated and best-selling tools, trusted by thousands of users. Join the community that relies on our solutions to enhance their growth and productivity.</p>
    </div>
    <section class="container">
        <div class="row justify-content-center gap-5">
            <?php for ($i = 0; $i < 3; $i++) { ?>

                <?php $data = [
                    'icon' => $star_products[$i]['id'],
                    'name' => $star_products[$i]['title_main'],
                    'users' => $star_products[$i]['num_clients'],
                    'description' => $star_products[$i]['description'],
                    'price' => '$' . number_format($star_products[$i]['price'], 2)
                ]; ?>
                <div class="col-12 col-md-3 flex-fill">
                    <?= $this->element('productCard', $data); ?>
                </div>
            <?php } ?>
        </div>

    </section>

</div>
<section class="extesion-shop text-center py-5 position-relative bg-cover d-flex justify-content-center align-items-center">
    <div class="container">
        <h2 class="fw-bold text-white">Don’t Miss Out on Our Full Range of Solutions!</h2>
        <p class="lead text-white">While our top-rated extensions are a great start, we offer a whole array of modules designed to transform every part of your OpenCart store. Whether you need advanced features or customized solutions, we've got you covered.</p>
        <p class="lead text-white">Explore Our Shop Now</p>
        <a href="products" class="btn btn-light btn-lg mt-3 fw-bold">Visit The ExtensionShop Now!</a>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col">
                <h2 class="fw-bold main-text">Discover Our Services</h2>
                <p class="lead main-text">Unlock the potential of your OpenCart store with our services. From <strong>tailored solutions</strong> and <strong>advanced customizations</strong> to <strong>seamless migrations</strong> and <strong>comprehensive development</strong>.</p>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <?= $this->Html->image('custom-modules-browser-icon.svg', ['class' => 'img-fluid']); ?>
            </div>
            <div class="col-md-6">
                <h3 class="fw-bold main-text mb-3">Custom Modules</h3>
                <p class="lead main-text mb-3">With over 17 years of experience, we specialize in creating bespoke <strong>modules tailored precisely to your needs</strong>. Our custom solutions ensure exceptional quality and performance, perfectly adapted to enhance your e-commerce operations.</p>
                <a href="open_ticket?option=Personal%20develop" class="btn btn-primary btn-lg px-4 btn-shadow">Tailor my Module</a>
            </div>
        </div>
        <div class="row align-items-center mt-5">
            <div class="col-md-6">
                <h3 class="fw-bold main-text mb-3">Full Project Development</h3>
                <p class="lead main-text mb-3">Transform your e-commerce vision into reality with our <strong>comprehensive development services</strong>. We build <strong>scalable</strong> and <strong>easy-to-maintain</strong> online solutions using cutting-edge technologies to ensure your site evolves with your business. From websites to mobile applications and software, just tell us what you need and we do it for you.</p>
                <a href="open_ticket?option=Personal%20develop" class="btn btn-primary btn-lg px-4 btn-shadow">Tailor my Module</a>
            </div>
            <div class="col-md-6">
                <?= $this->Html->image('full-project-development-icon.svg', ['class' => 'img-fluid']); ?>
            </div>
        </div>
        <div class="row align-items-center mt-5">
            <div class="col-md-6">
                <?= $this->Html->image('seo-marketing-icon.svg', ['class' => 'img-fluid']); ?>
            </div>
            <div class="col-md-6">
                <h3 class="fw-bold main-text mb-3">Marketing Services:</h3>
                <p class="lead main-text mb-3">We craft digital marketing strategies that deliver real results and business achieve its full potential online. From optimizing Google Ads and Meta Ads campaigns to enhancing your online visibility with SEO, our comprehensive approach ensures your brand stands out in the digital world.</p>
                <a href="open_ticket?option=Personal%20develop" class="btn btn-primary btn-lg px-4 btn-shadow">Tailor my Module</a>
            </div>
        </div>
        <div class="row align-items-center mt-5">
            <div class="col-md-6">
                <h3 class="fw-bold main-text mb-3">Opencart Version Migrations</h3>
                <p class="lead main-text mb-3">Seamlessly transition between OpenCart versions. We ensure a smooth upgrade with no data loss, handling every detail—from extensions and themes to your entire database—so you can continue focusing on growing your business without interruptions.</p>
                <a href="open_ticket?option=Personal%20develop" class="btn btn-primary btn-lg px-4 btn-shadow">Tailor my Module</a>
            </div>
            <div class="col-md-6 text-center">
                <?= $this->Html->image('opencart-migration-icon.svg', ['class' => 'img-fluid']); ?>
            </div>
        </div>
    </div>
</section>

<section class="py-5 why-us">
    <div class="container">
        <div class="row text-center d-flex justify-content-center">
            <div class="col-12 mb-4">
                <h2 class="main-text fw-bold">
                    Why Choose Us?
                </h2>
            </div>
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('opencart-partner-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">Opencart Partner</h4>
                <p class="lead main-text">Since 2012, we've been proud to be an official OpenCart Partner, offering top quality and support.</p>
            </div>
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('satisfaction-guaranteed-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">Satisfaction guaranteed or your money back!</h4>
                <p class="lead main-text">If you're not completely satisfied with our products, we offer a full refund, no questions asked!</p>
            </div>
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('fast-support-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">Fast support</h4>
                <p class="lead main-text">We reply to most inquiries within 24 working hours.</p>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('count-on-us-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">Count on us!</h4>
                <p class="lead main-text">We love what we do and we will stand behind you ready to answer any questions you may have along the way!</p>
            </div>
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('special-requirements-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">Special requirements?</h4>
                <p class="lead main-text">Share your project details with us, and our development team will provide you with the right solution.</p>
            </div>
            <div class="col-md-4 col-sm-12 mb-4">
                <?= $this->Html->image('expert-icon.svg', ['class' => 'img-fluid']); ?>
                <h4 class="fw-bold main-text mt-3">We are expert in...</h4>
                <p class="lead main-text">E-commerce/CMS: OpenCart, CS-Cart, Prestashop, Woocommerce, Wordpress.<br>
                    Frameworks: Angular, Vue.js, Ionic, jQuery.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-3 get-started">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5 col-sm-12 d-flex">
                <?= $this->Html->image('opencart-growth-illustration.svg', ['class' => 'img-fluid text-end']); ?>
            </div>
            <div class="col-md-7 col-sm-12">
                <h2 class="fw-bold text-white">Ready to grow in OpenCart with us?</h2>
                <p class="lead text-white ">Contact us today to discuss how our extensions and services can boost your e-commerce.</p>
                <p class="lead text-white ">Our team of experts is here to answer your questions and provide tailored solutions to meet your unique needs.</p>
                <a href="#" class="btn btn-danger btn-lg px-4 fw-bold mt-3" style="color: rgb(var(--red-color)); background-color: white !important;">Get Started Now!</a>
            </div>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2 class="text-center mb-5 main-text fw-bold">Testimonials</h2>
        <div class="row d-flex flex-row">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-sm-12 mb-4" id="paginationContainer" style="display: none;">
                        <div class="col-12 page" style="display: none;">
                            <?= $this->element('testimonialCard', [
                                'testimonial' => $testimonial['Testimonial']['testimonial'],
                                'name' => $testimonial['Testimonial']['name'],
                                'position' => $testimonial['Testimonial']['position'],
                                'date' => (new DateTime($testimonial['Testimonial']['created']))->format('M d, Y'),
                                'url' => $testimonial['Testimonial']['url'],
                                'image' => $testimonial['Testimonial']['image'],
                                'country' => $testimonial['Country']['name'],
                                'rate' => $testimonial['Testimonial']['rate'],
                            ]); ?>
                        </div>
                </div>
            <?php endforeach ?>
            <!-- Paginador -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3" id="paginator">
                    <!-- Aquí se generarán los botones de paginación -->
                </ul>
            </nav>
        </div>
    </div>
</section>




<?= $this->Html->script('add_to_cart.js'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('#paginationContainer .page');
    const itemsPerPage = 2;
    let currentIndex = 0;
    const paginator = document.getElementById('paginator');
    const totalPages = Math.ceil(elements.length / itemsPerPage);

    // Generar los botones de paginación
    function generatePaginator() {
        for (let i = 0; i < totalPages; i++) {
            const li = document.createElement('li');
            li.classList.add('page-item');
            const a = document.createElement('a');
            a.classList.add('page-link');
            a.href = "#";
            a.textContent = i + 1;
            a.setAttribute('data-page', i);
            li.appendChild(a);
            paginator.appendChild(li);

            a.addEventListener('click', function(e) {
                e.preventDefault();
                clearInterval(autoPagination); // Detener la paginación automática temporalmente
                currentIndex = i * itemsPerPage;
                showPage(currentIndex);
                updatePaginator(i); // Actualizar el estado activo del paginador
                // Reiniciar la paginación automática después de 10 segundos
                autoPagination = setInterval(nextPage, 10000);
            });
        }
    }

    // Mostrar la página actual
    function showPage(index) {
        elements.forEach((el, i) => {
            el.classList.remove('active', 'outgoing');
            if (i >= index && i < index + itemsPerPage) {
                el.classList.add('active');
                el.style.display = 'inline-block';
                el.parentElement.style.display = 'inline-block';
            } else if (i < index || i >= index + itemsPerPage) {
                el.classList.add('outgoing');
            }
        });

        setTimeout(() => {
            elements.forEach(el => {
                if (!el.classList.contains('active')) {
                    el.parentElement.style.display = 'none';
                    el.style.display = 'none';
                }
            });
        }, 500);
    }

    // Actualizar el estado del paginador
    function updatePaginator(activePage) {
        const paginationLinks = paginator.querySelectorAll('.page-item');
        paginationLinks.forEach((li, i) => {
            li.classList.remove('active');
        });
    }

    function hidePaginator(activePage) {
        const paginationLinks = paginator.querySelectorAll('.page-item');
        paginationLinks.forEach((li, i) => {
            if (i === activePage) {
                li.classList.add('active');
            }
        });
    }

    // Función para avanzar a la siguiente página
    function nextPage() {
        currentIndex += itemsPerPage;
        if (currentIndex >= elements.length) {
            currentIndex = 0; // Volver al inicio cuando se hayan mostrado todos
        }
        const activePage = Math.floor(currentIndex / itemsPerPage);
        showPage(currentIndex);
        updatePaginator(activePage);
        hidePaginator(activePage);
    }

    // Generar el paginador
    generatePaginator();

    // Mostrar la primera página al cargar
    showPage(currentIndex);
    updatePaginator(0);
    hidePaginator(0);

    // Configurar paginación automática cada 10 segundos
    let autoPagination = setInterval(nextPage, 1000);
});

</script>