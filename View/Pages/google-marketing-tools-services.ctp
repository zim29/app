<?php echo $this->Html->css(
    array(
        'pages/google-marketing-tools-services.css?'.date('YmdHis'),
        'layouts/frontend/tingle.min.css',
    )
); ?>

<script type="text/javascript">
    var text_no_name = 'For estimate a cost, you have to complete field "<b>What is your name? *</b>".';
    var text_no_email = 'For estimate a cost, you have to complete field "<b>What is your email? *</b>".';
    var text_no_url = 'For estimate a cost, you have to complete field "<b>What is your shop url? *</b>".';
    var text_no_url = 'For estimate a cost, you have to complete field "<b>What is your shop url? *</b>".';
    var text_no_services = 'Select some services to know an buget estimation.';
    var text_no_products = 'For estimate a cost, you have to select an option in field "<b>How much products do you have? *</b>".';
    var text_budget_estimation = 'The cost will around <b>$FROM</b> - <b>$TO</b>.';
    var text_budget_estimation_simple = 'The cost will around <b>$FROM</b>.';
    var only_seo_sem_plan = 'For know the  <b>SEO/SEM Audit</b>, without this service, we can\'t know the estimation.';
</script>

<?php echo $this->Html->script(
    array(
        'layouts/frontend/tingle.min.js',
        'pages/google-marketing-tools-services.js',
    )
); ?>

<article role="main">
    <header class="jumbotron text-center">
        <h1>
            <small>OPENCART ONLY<br/></small>
            Google Marketing Tools - Additional services
        </h1>
    </header>
    <div style="clear: both;"></div>
    <div class="container">
        <div class="row intro_banner">
            <div class="col-md-10 extension_information">
                <div class="card card-product bg-primary gradient-background border-0 gradient-fr">
                    <div class="card-body">
                        <div class="row">
                            <?= $this->Html->image('pages/extra_services/gmt_opencart/service_logo.png', array('class' => 'logo_service google_marketing_tools')) ?>
                            <div class="col-md-12">
                                <h2>Connect your e-commerce store with today's digital marketing tools</h2>
                                <p class="logos_inline">Advertise on <b>Google Ads, Facebook, Instagram, Pinterest, Bing</b> and more.</p>
                                <p>Boost your e-commerce sales with the most complete Google Tags solution for e-commerce. Digital marketing is the tool for e-commerce stores to maximize their performance <b>by more than 30%. Guaranteed!</b></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"><span class="title">Why GMT?</span></div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <span class="title_secondary">Easy setup: start advertising fast</span>
                <p>Connecting your e-commerce store through the <b>Google Marketing Tool</b> integration is easy.</p>
                <p>You just have to have the accounts in the different <b>digital marketing tools</b>, configure them correctly and with this, launch your campaigns. <b>GMT</b> helps you now in this initial configuration process so that your entire digital strategy is ready when you want to launch advertising through <b>Google Ads, Facebook Ads, Pinterest Ads, Bing Ads</b> and also the incredible Remarketing or Retargeting that will generate incredible results in their e-commerce sales.</p>
                <p>What are you waiting for? Just tell us what tools you want to configure and we'll do it for you.</p>
            </div>
            <div class="col-md-6">
                <span class="title_secondary">Connect your store, sell more stuff</span>
                <p>When you connect your store through <b>GMT</b>, you can create, manage and optimize ads in the blink of an eye. If you're new to Google Ads, we'll automatically create and synchronize your data stream, create a Google Ads and Merchant Center account, implement HTML checks, set up conversion crawlers, and complete your setup so you only have to worry about your store.</p>
                <p>If you want to create Brand Awareness, Interactions or generate traffic from your social networks, we will create, manage and optimize your <b>Facebook Ads, Instagram Ads and Pinterest Ads accounts.</b></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"><span class="title">Some initial questions</span></div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>What is your name? *</label>
                    <input type="text" name="data[questions][initial][name]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>What is your email? *</label>
                    <input type="text" name="data[questions][initial][email]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>What is your shop url? *</label>
                    <input type="text" name="data[questions][initial][url]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>How much products do you have? *</label>
                    <select name="data[questions][initial][products]" class="form-control selectpicker bs-select-hidden">
                        <option data-price="0" value="" selected="selected">- Select -</option>
                        <option data-price="1" value="1-200">From 1 to 200</option>
                        <option data-price="1.2" value="200-1000">From 200 to 1000</option>
                        <option data-price="1.3" value="1000-5000">From 1000 to 5000</option>
                        <option data-price="1.4" value="+5000">More than 50000</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Are you Google Marketing Tools client?</label>
                    <input type="text" name="data[questions][initial][license_id]" placeholder="In this case put order id here" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="row services">
        <div class="col-md-12"><span class="title">Selection of services to configure</span></div>
        <?php foreach ($services as $key => $service) { ?>
            <div class="col-md-6 service">
                <div class="input checkbox">
                    <input type="checkbox" name="data[services][<?= $service['code'] ?>]" value="<?= $service['title'] ?>" data-price="<?= $service['price'] ?>">
                    <span class="checkmark"></span>
                </div>
                <div class="information">
                    <span class="service_title"><?= $service['title'] ?></span><br>
                    <span class="include"><a href="javascript:{}" onclick="open_modal($(this))">What is it?</a><span style="display: none;"><?= $service['what_is_it'] ?></span> | <a href="javascript:{}" onclick="open_modal($(this))">What include?</a><span style="display: none;"><?= $service['what_include'] ?></span></span>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-12"><span class="title">Extra services</span></div>
        <?php foreach ($extra_services as $key => $service) { ?>
            <div class="col-md-6 service">
                <div class="input checkbox">
                    <input type="checkbox" name="data[services][<?= $service['code'] ?>]" value="<?= $service['title'] ?>" data-price="<?= $service['price'] ?>">
                    <span class="checkmark"></span>
                </div>
                <div class="information">
                    <span class="service_title"><?= $service['title'] ?></span><br>
                    <span class="include"><a href="javascript:{}" onclick="open_modal($(this))">What is it?</a><span style="display: none;"><?= $service['what_is_it'] ?></span> | <a href="javascript:{}" onclick="open_modal($(this))">What include?</a><span style="display: none;"><?= $service['what_include'] ?></span></span>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="container" style="margin-top: 30px;">
        <div class="row">
            <div class="col-md-12"><span class="title">Proposal estimate</span></div>
        </div>
        <div class="row">
            <div class="col-md-8 price_estimation"></div>
            <div class="col-md-4">
                <input type="hidden" name="budget">
                <button class="btn btn-lg btn-primary send_budget" href="javascript:{}" onclick="send_budget()" disabled>Request a proposal</button>
            </div>
        </div>
    </div>
</article>