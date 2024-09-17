<?php echo $this->Html->script(
    array( 
    'pages/download_center.js?'.date('YmdHis')
    )
); ?>

<article>
    <header class="jumbotron">
        <h1><?= __('Download center'); ?></h1>
    </header>

    <script type="text/javascript">
        var url_download = '<?= Router::url("/", false) ?>opencart/ajax_get_extension_download_links';
    </script>
    <?php
        $item = array(
            'name' => __('open a ticket'),
            'params' => array(
            'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'open_ticket'
            ),
            'extra_params' => array(
                'class' => '',
                'target' => '_new'
            )
        );
        $contact_link = $this->Html->link($item['name'], $item['params'], $item['extra_params']);


        $item = array(
            'name' => __('Identifier Recovery'),
            'params' => array(
            'plugin' => false,
                'controller' => 'pages',
                'action' => 'display',
                'download-center-recover'
            ),
            'extra_params' => array(
                'class' => '',
                'target' => '_new'
            )
        );
        $recover_link2 = $this->Html->link($item['name'], $item['params'], $item['extra_params']);

        $download_id = array_key_exists('download_id', $_GET) && !empty($_GET['download_id']) ? $_GET['download_id'] : '';
    ?>

    <?php if(!empty($download_id)) { ?>
        <script type="text/javascript">
            $(document).ready(function(){
                get_downloads();
            });
        </script>
    <?php } ?>

    <div class="container theme-showcase" role="main">
        <?php if(empty($download_id)) { ?>
            <p><?= __('In this section you can download different extension versions with your <b>Download Identifier</b>.'); ?></p>
            <p><?= __('You can find your <b>Download Identifier</b> in two places:'); ?></p>
            <ol>
                <li><?= __('The <b>Download Identifier</b> will have been sent in the email following your purchase.') ?></li>
                <li><?= __('In the Admin section of you shop navigate to Extensions > Your extension. Open the <b>Changlog - Downloads</b> tab.') ?></li>
            </ol>
            <p><?= __('If you are unable to find the <b>Download Identifier</b> use the <b>Identifier Recovery</b> link under the <b>Get Download Links</b> button.'); ?></p>
        <?php } ?>
        <h2><span><?= __('Download center form') ?>:</span></h2>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Enter your Download Identifier') ?> *</label>
                    <input name="download_id" id="downloadID" class="form-control" value="<?= $download_id ?>">
                </div>
                <div class="form-group text-right">
                    <a href="javascript:{}" onclick="get_downloads();" class="btn btn-lg btn-primary ticket"><?= __('Get Download links') ?></a>
                    <br><?= $recover_link2; ?>
                </div>
            </div>
            <div class="col-md-8 download_results"></div>
        </div>
    </div>
</article>