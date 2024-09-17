<article>
    <header class="jumbotron">
        <h1><?= __('Insert testimonial'); ?></h1>
    </header>

<div class="container theme-showcase" role="main">
    <p>Dear customer, I hope you are doing well!</p>

    <p>I need your sincere collaboration to appear in the "<a href="https://devmanextensions.com/testimonials" target="_blank">Testimonials</a>" section completing this form.</p>

    <p>Remember that also your business link will be published, being able to reach more peoples for you!</p>

    <p>Thanks you so much!! I hope your sincerely opinion!</p>

    <p><b>ps: You can use your region language, not necessary english</b>.</p>

    <br><br>

    <form action="<?= Router::url("/", false); ?>testimonials/insert" role="form" id="createTestimonial" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your name / Business') ?> *</label>
                    <input type="text" name="data[Testimonial][name]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your position (owner, IT, president...)') ?> *</label>
                    <input type="text" name="data[Testimonial][position]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your website url') ?> *</label>
                    <input type="text" name="data[Testimonial][url]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your country') ?> *</label>
                    <select name="data[Testimonial][country_id]" class="form-control selectpicker bs-select-hidden" data-live-search="true">
                        <?php foreach ($countries as $key => $country) { ?>
                            <option value="<?= $key ?>"><?= $country ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your email (won\'t be published)') ?> *</label>
                    <input type="text" name="data[Testimonial][email]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="form-group">
                    <label><?= __('Rate my service/products') ?> *</label>
                    <select name="data[Testimonial][rate]" class="form-control selectpicker bs-select-hidden" data-live-search="true">
                        <?php for ($i=5; $i >= 1 ; $i--) {  ?>
                        	<option value="<?= $i ?>"><?= $i ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= __('Your photo or brand') ?> *</label>
                    <input type="file" name="data[Testimonial][file]" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label><?= __('Your testimonial') ?> *</label>
                    <textarea class="form-control" name="data[Testimonial][testimonial]" id="" cols="30" rows="10"></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group text-right">
                    <a href="javascript:{}" onclick="validate_form();" class="btn btn-lg btn-primary ticket">Send testimonial</a>
                </div>
            </div>
        </div>
    </form>

    <script>
    	function validate_form()
    	{
    		var form_container = $('form#createTestimonial');
    		var error = false;
    		form_container.find('div.form-group > input.form-control, div.form-group select.form-control').each(function(){
    			if($(this).val() == '')
    				error = true;
    		});

    		if(!error && form_container.find('textarea').val() == '')
    			error = true;

    		if(error)
    			alert('Please, fill all inputs, thanks!');
    		else
    			form_container.submit();
    	}
    </script>
</div>
</article>