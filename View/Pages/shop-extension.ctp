<?php echo $this->Html->css(
    array(
        '/libraries/owl-carousel/owl.carousel.min.css',
        'pages/shop-extension.css?'.date('YmdHis'),
        '/v2/css/product.css',
    )
); ?>

<?php echo $this->Html->script(
    array(
        'cart.js?'.date('YmdHis'),
        '/libraries/owl-carousel/owl.carousel.min.js',
        'pages/shop/shop-extension.js'
    )
); ?>

<style>
	.video-container {
		position: relative;
		padding-bottom: 56.25%; /* 16:9 */
		height: 0;
		overflow: hidden;
		max-width: 100%;
		background: #000;
	}

	.video-container iframe {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: 0;
	}

</style>

<?php $image_path = 'pages/shop/opencart/'.$ext['Extension']['name_formatted']; ?>
<article role="main">
    <header class="jumbotron text-center">
        <h2>
           <?php /* <small><?= ucfirst($ext['Extension']['system']) ?> <br/></small> */ ?>
            <?= $ext['Extension']['title_main'] ?>
        </h2>
        <div class="bg-primary">
            <div class="container">
                <h3 class="lead">
                    <?= $ext['Extension']['title_sub'] ?>
                </h3>
            </div>
        </div>
    </header>
    <div style="clear: both;"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-9 extension_information">
                <div class="card card-product bg-primary gradient-background border-0 gradient-fr">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <?= $this->Html->image('pages/shop/' . $ext['Extension']['system'] . '/' . $ext['Extension']['name_formatted'] . '/logo.png', array('class' => 'imggrdt extension_logo animated bounceInLeft', 'title' => $ext['title_main'] . ' - ' . $ext['system'])) ?>
                            </div>
                            <div class="col-md-10">
                                <header>
                                    <h2 class="card-title"><?= $ext['Extension']['title_main'] ?></h2>
                                    <p class="card-subtitle"><?= $ext['Extension']['title_sub'] ?></p>
                                </header>
                                <!--<ul>
                                    <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A ab accusamus at, </li>
                                    <li>Blanditiis debitis dignissimos earum enim exercitationem hic ipsum</li>
                                    <li>Laboriosam libero minus omnis perspiciatis quibusdam ratione sequi suscipit velit!</li>
                                    <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A ab accusamus at, </li>
                                    <li>Blanditiis debitis dignissimos earum enim exercitationem hic ipsum</li>
                                </ul>-->
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-3">
                                Devman
                            </div>
                            <div class="col-md-9 text-right">
                                <?= __('Solid e-commerce solutions since 2009') ?> | <a href="https://devmanextensions.com">https://<b>devmanextensions</b>.com</a>
                            </div>
                        </div>
                    </div>
                </div>


				<?php if($ext['Extension']['id'] == '542068d4-ed24-47e4-8165-0994fa641b0a') { ?>
				<div class="video-container">
					<iframe width="750" height="450" src="https://www.youtube.com/embed/jOLspiRKeVI" title="Import Export PRO Opencart - Presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				</div>
				<?php } ?>

				<?php if($ext['Extension']['id'] == '5420686f-9450-4afa-a9c1-0994fa641b0a') { ?>
					<div class="video-container">
						<iframe width="750" height="450" src="https://www.youtube.com/embed/L-63RlqD2n4" title="Google Marketing Tools Opencart - Presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
					</div>
				<?php } ?>

				<br>

                <?php if (!empty($ext['Extension']['features_formatted'])) { ?>
                    <div class="main_features">
                        <h3 class="title"><?= __('Main features'); ?></h3>
                        <?= $ext['Extension']['features_formatted'] ?>
                    </div>
                <?php } ?>
                <div class="features">
                    <?php foreach ($ext['ExtensionFeature'] as $key => $extFe) { ?>
                        <div class="row">
                            <?php if (($key % 2 == 0)) { ?>
                                <div class="col-md-4 image"><?= $this->Html->image($image_path.'/features/'.$extFe['image'], array('class' => 'img-fluid', 'title' => $extFe['title'])); ?></div>
                                <div class="col-md-8 description">
                                    <span class="title"><?= $extFe['title'] ?></span>
                                    <span class="description"><?= $extFe['description'] ?></span>
                                </div>
                            <?php } else { ?>
                                <div class="col-md-8 description">
                                    <span class="title"><?= $extFe['title'] ?></span>
                                    <span class="description"><?= $extFe['description'] ?></span>
                                </div>
                                <div class="col-md-4 image"><?= $this->Html->image($image_path.'/features/'.$extFe['image'], array('class' => 'img-fluid', 'title' => $extFe['title'])); ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if(!empty($ext['Extension']['additional_info'])) { ?>
                    <div class="additional_info">
                        <span class="title"><?= __('Additional information'); ?></span>
                        <?= $ext['Extension']['additional_info'] ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-3 extension_cart">
                <div class="card card-buy">
                    <div class="card-body">
                        <h4 class="card-title"><?= __('What do you need?'); ?></h4>
                        <div class="form-group">
                            <select class="form-control quantity" name="units">
                                <?php foreach ($ext['Extension']['prices'] as $unit => $price) { ?>
                                    <?php if($unit == 1) { ?>
                                        <?php $main_price = $price ?>
                                        <option value="<?= $unit ?>"><?= sprintf(__('1 Domain (no discount) - $%s'), $price) ?></option>
                                    <?php } else { ?>
                                        <?php $total = number_format($price*$unit, 2); ?>
                                        <?php $discount = 100 - round(($price*100) / $main_price); ?>
                                        <option value="<?= $unit ?>"><?= sprintf(__('%s Domains - %s%% discount $%s - Total: $%s'), $unit, $discount, $price, $total) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                        <?php if($ext['Extension']['system'] == 'woocommerce') { ?>
                            <a href="https://devmanextensions.com/invoices/opencart/new_invoice?type=license&extension=gmt&platform=woo" class="btn btn-danger btn-block button_add_to_cart"><?= __('Purchase'); ?><i class="retina-financee-commerce-1190 animated"></i></a>
                        <?php } else { ?>
                            <a href="javascript:{}" onclick="add_to_cart('<?= $ext['Extension']['id'] ?>', $('select.quantity').val());" class="btn btn-danger btn-block button_add_to_cart"><?= __('Add to cart'); ?><i class="retina-financee-commerce-1190 animated"></i></a>
                        <?php } ?>
                        <?php /*<p style="margin: 5px 0px 0px 0px;">
                            <i>Premium support during <?= $ext['Extension']['oc_support_months'] ?> month<?= $ext['Extension']['oc_support_months'] > 1 ? 's' : '' ?></i>
                        </p>*/ ?>
                        <p>
                            <a target="_blank" class="license_rights" href="payment-and-refunds"><?= __('Know about license rights'); ?></a>
                        </p>

                        <?php if (!empty($ext['Extension']['demo_backend'])) { ?>
                            <a href="<?= $ext['Extension']['demo_backend']; ?>" target="_blank" class="btn btn-outline-primary btn-block demo"><?= __('Backend Demo'); ?></a>
                        <?php } ?>
                        <?php if (!empty($ext['Extension']['demo_frontend'])) { ?>
                            <a href="<?= $ext['Extension']['demo_frontend']; ?>" target="_blank" class="btn btn-outline-primary btn-block demo"><?= __('Frontend Demo'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($ext['Extension']['id'] == '542068d4-ed24-47e4-8165-0994fa641b0a') { ?>
        <section class="pad-30" id="testimonials">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <div class="main-details clearfix">
                            <!-- Testimonials :: Start -->
                            <div class="testimonials-area pad-t-30">
                                <h3 class="t2 text-center col-orange">SEE WHAT SOME CLIENTS THINK HAVE ALREADY<br>TESTED "<b>IMPORT / EXPORT PRO</b>":</h3>
                                <div class="owl-carousel owl-theme">
                                    <div class="testimonials-box">
                                        <div class="testimonials-desc">
                                            <p>"This is one of the best extensions for opencart, a must-have for any store! You can import and export products from anywhere. I can not imagine what would I do without it! It's definitely worth the money. Support is at a very high level, much better than I would expect. Keep up the good work guys!"</p>
                                        </div>
                                        <div class="testimonials-details">
                                            <div class="testimonials-img">
                                                <?= $this->Html->image($image_path.'/testimonial_matjaz.jpg', array('class' => 'img-fluid')); ?>
                                            </div>
                                            <div class="testimonials-name">Matjaž Krama</div>
                                            <div class="testimonials-position">Owner of toner-kartusa.si</div>
                                        </div>
                                    </div>
                                    <div class="testimonials-box">
                                        <div class="testimonials-desc">
                                            <p>"IMPORT / EXPORT PRO is probably the best export / import module for Opencart 3. Constant updates are very useful and the developer always seems to listen to the wishes of his customers. Highly recommended"</p>
                                        </div>
                                        <div class="testimonials-details">
                                            <div class="testimonials-img">
                                                <?= $this->Html->image($image_path.'/testimonial_martin.jpg', array('class' => 'img-fluid')); ?>
                                            </div>
                                            <div class="testimonials-name">Martin Nemcko</div>
                                            <div class="testimonials-position">Owner of www.Lithogarden.eu</div>
                                        </div>
                                    </div>
                                    <div class="testimonials-box">
                                        <div class="testimonials-desc">
                                            <p>"We are setting up an online store to sell electronic products in Central Europe. Our supplier only sent us an XML file with all its product catalog. If it had not been for the IMPORT / EXPORT PRO extension, it would have taken us more than two weeks to synchronize the entire catalog with our ecommerce platform."</p>
                                        </div>
                                        <div class="testimonials-details">
                                            <div class="testimonials-img">
                                                <?= $this->Html->image('/images/testimonials/leoelektro_owner.jpg', array('class' => 'img-fluid')); ?>
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
                        <div class="faq-ans">Yes. If you have a domain (or several) for testing, you will can use extension here.</div>
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
                    <div class="col-md-3"></div>
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
    <?php } ?>
</article>

