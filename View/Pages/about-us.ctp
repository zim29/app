<?php echo $this->Html->css(
  array(
    '/libraries/owl-carousel/owl.carousel.min.css',
    'pages/about-us.css?' . date('YmdHis')
  )
); ?>

<?php echo $this->Html->script(
  array(
    '/libraries/owl-carousel/owl.carousel.min.js',
    'pages/about-us.js?' . date('YmdHis')
  )
); ?>

<?php
$experience_years = (int)date('Y')-2007;
?>

<style type="text/css">
	.espacio_custom {
		margin-top: 20px;
	}
	.custom_img {
		height: 30px;
		width: 10px;
	}
	.alert_info {
		background-color: #d9edf7;
		border-color: #bce8f1;
		color: #214c61;
	}
	.milestone {
		height: 240px !important;
		display: flex;
		align-items: center;
		justify-content: center;
	}

</style>

<article class="about-us">
  <div class="hero text-center text-black">
    <div class="container-fluid d-flex flex-column justify-content-center align-items-center h-100 text-center">
      <h1 class="mb-3"><?= __('About us?') ?></h1>
      <p class="px-5"><?= __('If you have come this far, it is because you are surely wondering who we are. Well, Devman is like the superhero you\'ve always wanted to have when you were little, capable of solving all your problems.') ?></p>
	  <p class="px-5"><?= __('A little over ten years ago we set out on this path, not exempt from errors and difficulties. Every stone on this path has built the path that has led us to be the leader among OpenCart developers in the world. We have the best extensions for this platform, with simple and adaptable solutions for both programmers and ecommerce owners. Our clients are our friends and we treat them as such. Devman extensions are unique because of you. We constantly listen to you and make improvements. With us you will never feel alone, we offer answers in less than 24 hours before any doubt.') ?></p>
	  <p class="px-5"><?= __('We like to see you happy. We are a group of young entrepreneurs who do not rest until they see a smile on our clients\' faces.') ?></p>

	  <span class="d-block text-black font-weight-bold espacio_custom">David Nieves Coronado</span>
	  <span class="d-block text-pink espacio_custom">Devman Director General</span>

      <div class="image-container mx-auto mt-4 mt-xl-5">
        <?= $this->Html->image('img-worker.png', ['class' => 'mx-auto d-block']) ?>
      </div>
    </div>
  </div>

  <section class="our-team">
    <h2 class="text-center text-black px-5 mb-4"><?= __('Our team') ?></h2>
    <div class="container-fluid">
      <div class="owl-carousel owl-theme">
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/david_nieves_coronado_ceo.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">David Nieves Coronado</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('CEO') ?></p>
          <p class="languages text-pink mb-0">ENG/SPA</p>
        </div>
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/dariel_vicedo_developer.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">Dariel Vicedo</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('Developer / Support team') ?></p>
          <p class="languages text-pink mb-0">ENG/SPA/ITA</p>
        </div>
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/andres_javier_pavon_web_developer.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">Andrés Javier Pavón Fernández</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('Developer') ?></p>
          <p class="languages text-pink mb-0">ENG/SPA</p>
        </div>
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/dairon_ian_developer.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">Dairon Ian García Roque</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('Developer') ?></p>
          <p class="languages text-pink mb-0">ENG/SPA</p>
        </div>
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/denis_russia_support_team.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">Денис Перелыгин (Denis Perelygin)</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('Russia support team') ?></p>
          <p class="languages text-pink mb-0">RUS/ENG</p>
        </div>
        <div class="member text-center mx-auto">
          <div class="image-container mb-4">
            <?= $this->Html->image('/v2/images/employers/marlon_umania_marketing_specialist.jpg', ['class' => 'mx-auto d-block']) ?>
          </div>
          <p class="text-black font-weight-semibold mb-0">Marlon Umaña</p>
          <p class="font-weight-semibold mb-0 occupation text-pink"><?= __('SEO/SEM Specialist') ?></p>
          <p class="languages text-pink mb-0">ENG/SPA</p>
        </div>
      </div>
    </div>
  </section>

  <section class="our-goals">
    <div class="container-fluid">
      <h2 class="text-center text-white px-5 mb-4 px-lg-5"><?= __('Our goals') ?></h2>
      <div class="text-center text-white font-weight-light">
        <p>Maintain our extensions as the best on the market.</p>
        <p>Continue to be a benchmark to follow for OpenCart extensions.</p>
        <p>Continue to provide excellent support service to our customers.</p>
      </div>

      <h2 class="text-center text-white px-5 mb-4 px-lg-5 mt-5"><?= __('Milestones') ?></h2>
      <div class="owl-carousel owl-theme position-relative">
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2011 - First Opencart projects.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2012 - First extension developed.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2013 - Develop multiple extensions and become an expert in Opencart system.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2014 - “Opencart quality extensions” founded.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2016 - Renamed brand to “Devman Extensions” - Devman started to need a team.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2018 - Changed corporate image - Company grows and continues developing best products.
			  </p>
		  </div>
		  <div class="milestone text-center mx-auto bg-white">
			  <p class="text-black font-weight-light">
				  2020 - Best years for Devman team, our team continue grows and our extensions are considered “top” in all Opencart market.
			  </p>
		  </div>
	  </div>
    </div>
  </section>

  <section class="our-products">
    <div class="container-fluid">
      <h2 class="text-center text-black px-5 mb-4 px-lg-5"><?= __('Our products') ?></h2>
      <div class="row">
        <?php foreach ($extensions as $key => $ext) : ?>
          <div class="col-12 col-md-6 col-xl-4">
            <div class="extension text-center mx-auto h-100">
              <a class="d-block" title="<?= $ext['title_main'] ?>" href="<?= $ext['seo_url'] ?>">
                <h3 class="card-title mb-5"><?= $ext['title_main'] ?></h3>
                <?= $this->Html->image('pages/shop/' . $ext['system'] . '/' . $ext['name_formatted'] . '/logo.png', array('title' => $ext['title_main'] . ' - ' . $ext['system'])) ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="our-community text-white text-center">
    <h2 class="mb-4 pb-xl-5 px-5 px-lg-5 text-center"><?= __('Our community') ?></h2>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
          <div class="data">
            <?= $this->Html->image("icon-face-smiling.svg", ["class" => "mb-4 mb-lg-5"]) ?>
            <p class="font-weight-light"><?= __("+").$clients_total_num ?></p>
            <p class="font-weight-light"><?= __("clients") ?></p>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="data">
            <?= $this->Html->image("icon-three-stars.svg", ["class" => "mb-4 mb-lg-5"]) ?>
            <p class="font-weight-light"><?= $experience_years.__(" years")?></p>
            <p class="font-weight-light"><?= __("of experience")?></p>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="data">
            <?= $this->Html->image("icon-grow.svg", ["class" => "mb-4 mb-lg-5"]) ?>
            <p class="font-weight-light"><?= $num_invoices ?></p>
            <p class="font-weight-light"><?= __("personal developments") ?></p>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="data">
            <?= $this->Html->image("icon-opencart-white.svg", ["class" => "mb-4 mb-lg-5"]) ?>
            <p class="font-weight-light"><?= __("300") ?></p>
            <p class="font-weight-light"><?= __("Opencart migrations") ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>
</article>





