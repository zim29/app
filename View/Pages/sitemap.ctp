<h1 class="main"><?= __('Sitemap') ?></h1>
<div class="container theme-showcase" role="main">
    <?php
        $website_links = array(
           array(
               'name' => __('Home'),
                'params' => '/',
           ),
            array(
                'name' => __('Shop - Opencart extensions Shop'),
                'params' => array(
                    'plugin' => false,
                    'controller' => 'pages',
                    'action' => 'display',
                    'shop-opencart'
                ),
            ),
            array(
                'name' => __('Shop - Prestashop addons Shop'),
                'params' => array(
                    'plugin' => false,
                    'controller' => 'pages',
                    'action' => 'display',
                    'shop-prestashop'
                )
            ),
            array(
                'name' => __('Shop - Woocommerce plugins Shop'),
                'params' => array(
                    'plugin' => false,
                    'controller' => 'pages',
                    'action' => 'display',
                    'shop-woocommerce'
                )
            ),
        );

        foreach ($extensions as $key => $ext) {
            $website_links[] = array(
                'name' => ucfirst($ext['Extension']['system']).' - '.$ext['Extension']['name'],
                'params' => '/'.$ext['Extension']['seo_url']
            );

        }
        $website_links[] = array(
            'name' => __('Services - Custom modules development'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'opencart-prestashop-woocommerce-custom-module-development'
            ),
        );
        $website_links[] = array(
            'name' => __('Services - Customized quick develops'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'opencart-prestashop-woocommerce-customized-quick-develops'
            ),
        );
        $website_links[] = array(
            'name' => __('Services - Full projects development'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'full-projects-development'
            ),
        );
        $website_links[] = array(
            'name' => __('Licenses terms, payments and refunds'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'payment-and-refunds'
            ),
        );
        $website_links[] = array(
            'name' => __('Terms and Conditions'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'terms-and-conditions'
            ),
        );
        $website_links[] = array(
            'name' => __('Privacy Policy'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'privacy-policy'
            ),
        );
        $website_links[] = array(
            'name' => __('Data protection'),
            'params' => array(
                'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'data-protection'
            ),
        );
    ?>
    <ul>
        <?php foreach ($website_links as $key => $link) { ?>
            <li><?= $this->Html->link($link['name'], $link['params']); ?></li>
        <?php } ?>
    </ul>
</div>