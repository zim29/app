<h1 class="main"><?= __('FAQ').' - '.$extension['Extension']['name']; ?></h1>

<script type="text/javascript">
	// The function actually applying the offset
function offsetAnchor() {
  if (location.hash.length !== 0) {
    window.scrollTo(window.scrollX, window.scrollY - 100);
  }
}

// Captures click events of all <a> elements with href starting with #
$(document).on('click', 'a[href^="#"]', function(event) {
  // Click events are captured before hashchanges. Timeout
  // causes offsetAnchor to be called after the page jump.
  window.setTimeout(function() {
    offsetAnchor();
  }, 0);
});

// Set the offset when entering page with hash present in the url
window.setTimeout(offsetAnchor, 0);
</script>

<div class="container theme-showcase" role="main">
	<ol>
		<?php foreach ($faqs as $key => $faq) { ?>
			<a href="#faq-<?= $key ?>"><?= $faq['Faq']['title'] ?></a>
		<?php } ?>
	</ol>
	<hr>
	<?php foreach ($faqs as $key => $faq) { ?>
		<div class="faq" id"#faq-<?= $key ?>">
			<h3><?= $faq['Faq']['title'] ?></h3>
			<div class="description"><?= $faq['Faq']['description'] ?></div>
		</div>
	<?php } ?>
</div>
