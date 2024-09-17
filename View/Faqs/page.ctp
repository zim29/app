<h1 class="main"><?= __('FAQ').' - '.$extension['Extension']['name']; ?></h1>

<script type="text/javascript">
	$(function(){
	   $('div.faq').hide();
	});

	function offsetAnchor() {
	  if (location.hash.length !== 0) {
	    window.scrollTo(window.scrollX, window.scrollY - 200);
	  }
	}

	$(document).on('click', 'a[href^="#"]', function(event) {
		var selector = $(this).attr('href');
		$('div.faq').hide();
		$('div.faq'+selector).show();
		window.setTimeout(function() {
			offsetAnchor();
		}, 0);
	});

	window.setTimeout(offsetAnchor, 0);
</script>

<div class="container theme-showcase" role="main">
	<ol>
		<?php foreach ($faqs as $key => $faq) { ?>
			<li><a href="#faq-<?= $key ?>"><?= $faq['Faq']['title'] ?></a></li>
		<?php } ?>
	</ol>
	<hr>
	<?php foreach ($faqs as $key => $faq) { ?>
		<div id="faq-<?= $key ?>" class="faq">
			<h3><?= $faq['Faq']['title'] ?></h3>
			<div class="description"><?= nl2br($faq['Faq']['description']) ?></div>
		</div>
	<?php } ?>
</div>
