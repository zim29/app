<article>
    <header class="jumbotron">
        <h1><?= __('Download center recover'); ?></h1>
    </header>

    <form action="<?php echo Router::url("/", false); ?>opencart/recover_download_id" role="form" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <div class="container theme-showcase" role="main">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?= __('Enter your license id') ?> *</label>
                        <p class="help-block"><?= __('Your <b>Order ID</b> from opencart.com'); ?></p>
                        <input name="license_id" id="license_id" class="form-control" value="<?= !empty($this->request->data['license_id']) ? $this->request->data['license_id'] : '' ?>">
                    </div>
                    <div class="form-group text-right">
                        <a href="javascript:{}" onclick="$('form').submit();" class="btn btn-lg btn-primary ticket"><?= __('Get your Download Identifier') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</article>