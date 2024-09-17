<?php echo $this->Html->css(
    array(
        'pages/shop-extension.css?'.date('YmdHis'),
        '/libraries/owl-carousel/owl.carousel.min.css',
        '/libraries/owl-carousel/owl.carousel.min.css',
        'pages/shop-extension-ie-pro.css?'.date('YmdHis'),
    )
); ?>

<?php echo $this->Html->script(
    array(
    'cart.js?'.date('YmdHis'),
     '/libraries/owl-carousel/owl.carousel.min.js',
     'pages/shop/shop-extension-ie-pro.js'
    )
); ?>
<div class="main-container">
    <div class="row alert_container">
        <div class="container"></div>
    </div>
    <section class="main-banner banner-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="banner-text">
                        <p>Do you need an all-in-one <span class="col-yellow">Import/Export</span> tool that meets all the requirements of your online store?</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="cart">
                        <label><?= __('What do you need?'); ?></label>
                        <select class="form-control quantity" name="units">
                            <?php foreach ($ext['Extension']['prices'] as $unit => $price) { ?>
                                <?php if($unit == 1) { ?>
                                    <?php $main_price = $price ?>
                                    <option value="<?= $unit ?>"><?= sprintf(__('Single domain - $%s'), $price) ?></option>
                                <?php } else { ?>
                                    <?php $total = number_format($price*$unit, 2); ?>
                                    <?php $discount = 100 - round(($price*100) / $main_price); ?>
                                    <option value="<?= $unit ?>"><?= sprintf(__('%s Domains - %s%% discount $%s - Total: $%s'), $unit, $discount, $price, $total) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <a href="javascript:{}" onclick="add_to_cart('<?= $ext['Extension']['id'] ?>', $('select.quantity').val());" class="btn btn-primary button_add_to_cart"><i class="fa fa-shopping-basket"></i><?= __('Add to cart'); ?>
                        <i class="retina-financee-commerce-1190"></i>
                        </a>
                        <a target="_blank" class="license_rights" href="payment-and-refunds"><?= __('Know about license rights'); ?></a>
                        <?php if (!empty($ext['Extension']['demo_backend'])) { ?>
                            <a href="<?= $ext['Extension']['demo_backend']; ?>" target="_blank" class="btn btn-primary demo"><i class="fa fa-cog"></i><?= __('Backend Demo'); ?></a>
                        <?php } ?>
                        <?php if (!empty($ext['Extension']['demo_frontend'])) { ?>
                            <a href="<?= $ext['Extension']['demo_frontend']; ?>" target="_blank" class="btn btn-primary demo"><i class="fa fa-user"></i><?= __('Frontend Demo'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="aboutus-section pad-t-30 banner-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="main-details clearfix">
                        <p>Many of the import and export extensions currently on the market are difficult to install; they lack many functions that you need in your online store; It takes a lot of time and effort to learn to manage them; and they are not user-friendly.</p>
                        <h4 class="t4">Does this sound familiar?</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="smile-dott">
                                    <li>You purchased an extension to import and export the products of your online store and as soon as you have updated to the latest version. it has stopped working.</li>
                                    <li>You have exported the entire catalog of your online store and the tool has run out of memory due to an excess of data and information.</li>
                                    <li>If you have an online store in several languages ​​or you use multiple platforms, the extension stops working.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="smile-dott">
                                    <li>You have tried to solve your issues but support ignored you or took too long to reply.</li>
                                    <li>During a full import, you received an error and the extension made you lose a lot of data.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="blockquote-area clearfix">
                        <div class="blockquote-box">
                            <p class="t2">DO NOT WORRY: THE SOLUTION TO ALL OF THESE PROBLEMS IS <span class="extrabold">"IMPORT / EXPORT PRO"</span>, THE MOST COMPLETE, POWERFUL AND STABLE OPENCART IMPORT / EXPORT TOOL</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
    <section class="pad-30">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="main-details clearfix">
                        <p>At first sight, all import / export extensions look the same, but when you start using them you realize that almost none of them are useful for everything you want, because they are difficult to manage or because they don't have the functionality you need.</p>
                        <h3 class="t2 text-center col-orange">DIFFERENCES BETWEEN <span class="extrabold">"IMPORT / EXPORT PRO"</span> AND OTHER IMPORT/EXPORT OPENCART EXTENSIONS:</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="correct-dott">
                                    <li>Our extension is the most powerful <span class="bold">and the one with the most features.</span></li>
                                    <li>It is the tool that allows you to import / export more data from your online store (products, images, categories, orders, coupons, customers ...). In addition, it is the one that accepts <span class="bold">the most input formats (XLS, CSV, ODS, XML, Google Spreadsheet and more),</span> which facilitates the import of most files your supplier may send you.</li>
                                    <li>It is the <span class="bold">safest extension,</span> because even if an error occurs at any time of the process, it is able to return to the state immediately before the failure. You will never lose any data!</li>
                                    <li>Only this tool allows you <span class="bold">to schedule tasks</span> that consume many resources in times of less traffic, when the server has more memory (CRON jobs).</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="correct-dott">
                                    <li>You can create <span class="bold">unlimited import / export filters</span> so that the extension adapts to the specific needs of your online store.</li>
                                    <li>While the rest of Opencart's extensions offer you only written documentation to answer your questions, this tool is the only one that <span class="bold">provides hands-on video tutorials</span> to learn how to use the tool or to resolve the most common issues.</li>
                                    <li>"IMPORT / EXPORT PRO" is the only extension that offers you the possibility of <span class="bold">automatically adding the profit margin to the prices,</span> as they are imported from the supplier's catalog. This function prevents you from making profitability calculations manually, product by product.</li>
                                </ul>
                            </div>
                        </div>
                        <!-- Testimonials :: Start -->
                        <div class="testimonials-area pad-t-30">
                            <h3 class="t2 text-center col-orange">SEE WHAT SOME CLIENTS THINK HAVE ALREADY<br>TESTED "<b>IMPORT / EXPORT PRO</b>":</h3>
                            <div class="owl-carousel owl-theme">
                                <div class="testimonials-box">
                                    <div class="testimonials-desc">
                                        <p>"IMPORT / EXPORT PRO is probably the best export / import module for Opencart 3. Constant updates are very useful and the developer always seems to listen to the wishes of his customers. Highly recommended."</p>
                                    </div>
                                    <div class="testimonials-details">
                                        <div class="testimonials-img">
                                            <?= $this->Html->image('/images/testimonials/alex_mogutenko_owner.jpg', array('class' => 'img-responsive')); ?>
                                        </div>
                                        <div class="testimonials-name">Alex Mogutenko</div>
                                        <div class="testimonials-position">Owner of Web-diesel.ru</div>
                                    </div>
                                </div>
                                <div class="testimonials-box">
                                    <div class="testimonials-desc">
                                        <p>"The IMPORT / EXPORT PRO extension is essential for anyone who has an online store. It is a fantastic extension that saves time in Opencart. Thanks to it, I was able to import 2,000 categories in a just few seconds! Also, when I had to contact support, the response has always been quick, in less than an hour. I highly recommend this extension."</p>
                                    </div>
                                    <div class="testimonials-details">
                                        <div class="testimonials-img">
                                            <?= $this->Html->image('/images/testimonials/rayconda_owner.jpg', array('class' => 'img-responsive')); ?>
                                        </div>
                                        <div class="testimonials-name">Rayconda</div>
                                        <div class="testimonials-position">Owner of Rayconda.com</div>
                                    </div>
                                </div>
                                <div class="testimonials-box">
                                    <div class="testimonials-desc">
                                        <p>"We are setting up an online store to sell electronic products in Central Europe. Our supplier only sent us an XML file with all its product catalog. If it had not been for the IMPORT / EXPORT PRO extension, it would have taken us more than two weeks to synchronize the entire catalog with our ecommerce platform."</p>
                                    </div>
                                    <div class="testimonials-details">
                                        <div class="testimonials-img">
                                            <?= $this->Html->image('/images/testimonials/leoelektro_owner.jpg', array('class' => 'img-responsive')); ?>
                                        </div>
                                        <div class="testimonials-name">Leoelektro</div>
                                        <div class="testimonials-position">Owner of Leoelektro.sk.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonials :: End -->
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
    <section class="video-section pad-30 banner-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <h4 class="t4">The operation of the extension is very simple.</h4>
                    <p>The operation of the extension is very simple. In a few minutes, you will become familiar with it.</p>
                    <p>And then you will be creating export and import profiles, to customize the use of the tool to your specific needs.</p>
                    <p>With this extension you can create and edit products, manage categories, delete items you do not need or migrate to more modern versions of Opencart, without compatibility problems.</p>
                    <p>And all these tasks are executed with ease, since "IMPORT / EXPORT PRO" is characterized mainly by being an intuitive extension.</p>
                    <p>Even the most inexperienced users learn how to use the extension very quickly!</p>
                    <?php /*<div class="youtube-area">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tgbNymZ7vqY"></iframe>
                        </div>
                    </div> */ ?>
                </div>
            </div>
        </div>
    </section>
    <section class="services-section pad-30 banner-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="t2 text-center col-orange">THE ULTIMATE IMPORTER / EXPORTER DOES ALL YOU WILL EVER NEED:</h3>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/compatible.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Compatible</a>
                                    <div class="service-desc">It works on all versions of Opencart (1.5, x, 2.x and 3.x), as well as its variants MijoShop and ocStore.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/changes.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Massive Changes</a>
                                    <div class="service-desc">Minimal time involved every time you receive the new catalog from a provider, because it synchronizes quickly.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/platforms.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Export to other platforms</a>
                                    <div class="service-desc">The extension allows you to export all the data from your online store to other marketplaces or to other billing platforms.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/backups.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Backups</a>
                                    <div class="service-desc">Make backup copies of all the data in your online store whenever you want.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/memory.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">No memory issues</a>
                                    <div class="service-desc">Large amounts of data can be exported at once. The processes have been optimized to offer the maximum performance.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/import.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Import Profile</a>
                                    <div class="service-desc">You can save different import / export profiles to save time and avoid repetitive tasks.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
    <section class="savetime-section">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="t2 text-center col-orange">YOU WILL NOT BELIEVE HOW MUCH TIME YOU WILL SAVE</h3>
                    <p>Using the importer "IMPORT / EXPORT PRO" you will save a lot of time. Monotonous and repetitive tasks, which do not add any value to your business, will be a thing of the past.</p>
                    <p>Think for a moment:</p>
                    <p>How much is your free time worth?</p>
                    <p>How much do you think it is worth the comfort and tranquility of having a tool that solves all problems at once?</p>
                    <p class="semibold" style="font-size: 20px;">I'm asking you because the price of this extension is $69.99. Same as 14 Big Macs or 17 beers.</p>
                    <p>With each month you stop taking a Big Mac or a beer, practically in a year you will have paid for the tool and you will have hundreds of hours that you previously dedicated to useless, unproductive jobs that do not generate any profit.</p>
                    <p>What are you waiting for! Your diet and your business are begging for it! .. ;)</p>
                    <div class="blockquote-area clearfix">
                        <div class="blockquote-box">
                            <p class="t2 mb-30">IT'S A NO-BRAINER! BUY NOW THE EXTENSION "IMPORT / EXPORT PRO" FOR A PRICE OF <span class="col-yellow">$69,99</span> DOLLARS</p>
                            <a href="javascript:{}" onclick="$('div.main-container div.cart a').trigger('click');" class="btn btn-primary button_add_to_cart"><i class="fa fa-shopping-basket"></i>Add To Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
    <section class="price-section pad-30">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="t2 text-center col-orange">THIS PRICE INCLUDES:</h3>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/satisfaction.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Satisfaction Guarantee</a>
                                    <div class="service-desc">If for any reason, you are not fully satisfied with our extension within the first 15 days, we will issue a full refund, no explanation required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/support.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">12 Months of Premium Support</a>
                                    <div class="service-desc">If you have any questions or problems, you may visit Devman Extensions technical support website. You will receive a prompt reply during our business hours (9AM-9PM) or within 24 hours outside of those hours.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="service-box">
                                <div class="service-img">
                                    <?= $this->Html->image('/img/pages/shop/opencart/import_export_pro/updates.png', array('class' => 'img-responsive')); ?>
                                </div>
                                <div class="service-details">
                                    <a href="javascript:{}" class="no_link_hand service-name">Unlimited Updates</a>
                                    <div class="service-desc">With your license you have unlimited access to download all versions prior to the date of purchase and all new versions released during the 6 months after the date of purchase or renewal.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>
    <section class="faq-section pad-30" id="faq">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="t3 text-center col-orange">Faq</h3>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
        <ul class="faq-list">
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Does the extension work for all templates/themes, including Journal 2 and Journal?</div>
                            <div class="faq-ans">Yes, it works with all Opencart themes.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I have a test domain test and an official domain, will it work on both?</div>
                            <div class="faq-ans">Yes. If you have a domain (or several) for testing, we will add those domains to your license at no additional cost.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">My store is multi-language. Will it be compatible with export and import in multi-language?</div>
                            <div class="faq-ans">It is fully compatible with multi-language and multi-shop.</div>
                        </div>
                        <div class="col-md-3"></DIV>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Can the extension update the data automatically every day?</div>
                            <div class="faq-ans">Yes. With "CRON Jobs" you can run your import and export profiles every day depending on the configuration you select.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I have multi-shop enabled. Can I edit the products of a specific store?</div>
                            <div class="faq-ans">Yes. You can create a filter in your export profiles to export the products of a particular store.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I want to move the data from my old Opencart store 1.5.4 to version 3, can I do that?</div>
                            <div class="faq-ans">Yes. It is possible to migrate data between all versions of Opencart, even from newer versions to older ones.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I'm not sure if I'll be able to learn how to use the extension.</div>
                            <div class="faq-ans">Do not worry, the "IMPORT / EXPORT PRO" extension is very intuitive and easy to use. In addition, we have a section of video tutorials that explain the most common questions, as well as advanced tutorials that explain more complex operations to take full advantage of the importer.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Besides technical support, can you help us import an existing online store?</div>
                            <div class="faq-ans">The use of this extension is very simple and you learn quickly to use it. But if you do not have the time or desire to import / export all the data of your online store, we can do it for you for a reasonable fee. Feel free to ask about our rates for this service.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Can I edit my current products?</div>
                            <div class="faq-ans">Effectively, you can edit your products, categories, options, attributes, customers, orders, etc. as well as add new elements.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">What formats does the importer support?</div>
                            <div class="faq-ans">XLSX, XML, ODS, CSV and Google Spreadsheets.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Is it possible to add fields that have been created with other extensions (from other developers) to the import and export profiles made with "IMPORT / EXPORT PRO"?</div>
                            <div class="faq-ans">Yes, it is possible to do it. But you need the "Custom Fields" add-on that you can buy when placing your order for the "IMPORT / EXPORT PRO" extension.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I work with several suppliers. Can I import all the files even if they are in different formats?</div>
                            <div class="faq-ans">Yes. You can create several import profiles, each one with the precise configuration to process the different files of each supplier.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">If I import related data - that are not registered in my store (such as the manufacturer) - will they be created or will the import fail?</div>
                            <div class="faq-ans">That's not a problem. All items that are not included in the platform will be removed without the need to make previous imports.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I have a store with a large number of products. Will this importer work with all this data?</div>
                            <div class="faq-ans">Yes. We have optimized the import and export processes to the maximum. Unlike other Opencart extensions, "IMPORT / EXPORT PRO" includes the "CRON Jobs" add-on, which eliminates any memory limit.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">Can I delete products in batch?</div>
                            <div class="faq-ans">Yes. Just put the true value (by default, number 1) in the "Delete" column, and then import the file that contains the data to be deleted. This operation can be performed on all other elements (categories, manufacturers, options).</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">I just want to update the price and stock of my products. But instead of the product ID, I have nothing but the model. Is it possible to do it even like this?</div>
                            <div class="faq-ans">Yes. You can update individual data of each element (products, categories, options, attributes ...) by establishing an element identifier for cases in which the main ID is not available (model, sku, ean, isbn, etc.)</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">My provider provides me with a file on his server. Can I import this data directly?</div>
                            <div class="faq-ans">Yes. You can configure your import profile in FTP mode to upload the product data through an external remote server. In addition you can also run this every day in an automated way, thanks to the module "CRON Jobs" that is included as standard in this extension.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">My supplier provides me with a URL from which to download their products. Can I import this data?</div>
                            <div class="faq-ans">Yes. In the same way as in the previous question. You can configure an import profile to tell it to download the file through a URL.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="faq-que">My supplier provides the images of the products in links. Will the importer be able to download them without having to do it manually, one at a time?</div>
                            <div class="faq-ans">Yes. When the import system detects a URL in the image columns, it will try to download them and assign them to the element in question.</div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </li>
        </ul>
    </section>
    <section class="footer footer-area banner-bg">
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="t2 text-center col-orange">DOWNLOAD <span class="extrabold">"IMPORT-EXPORT PRO",</span> THE MOST POWERFUL, COMPLETE AND STABLE OPENCART IMPORT / EXPORT TOOL, FOR ONLY $69.99 INCLUDING:</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="dott-list">
                                    <li>Satisfaction guarantee.</li>
                                    <li>12 months of premium support.</li>
                                    <li>Unlimited updates for 6 months.</li>
                                    <li>Programming tasks through the CRON jobs module.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="dott-list">
                                    <li>Compatible with all versions of Opencart and its variants (MijoShop and ocStore).</li>
                                    <li>Massive export and import functionality.</li>
                                    <li>No memory problems.</li>
                                </ul>
                            </div>
                        </div>
                        <a href="javascript:{}" onclick="$('div.main-container div.cart a').trigger('click');" class="btn btn-primary button_add_to_cart btn-foot"><i class="fa fa-shopping-basket"></i>BUY "IMPORT-EXPORT PRO"</a>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
    </section>
</div>
