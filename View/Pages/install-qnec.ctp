<article>
    <header class="jumbotron">
        <h1><?= __('Install tutorial Quick n Easy checkout'); ?></h1>
    </header>

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

<h2 style="color: #f00;">IMPORTANT BEFORE START</h2>

<b>IF YOU COME FROM QUICK N EASY CHECKOUT LESS THAN 5.0.0, <a href="#actions_old_users">CLICK HERE, YOU NEED DO THESE ACTIONS BEFORE START INSTALL PROCESS</a></b>

<h2>OPENCART 2.X - 3.X Install process</h2>

<i>* If you can't complete this install in "Opencart extension installer" by external problems in your shop do a <a href="#install_manually">manual installation</a></i><br><br>
<ol>
    <li><b>Install extension with Opencart extension installer:</b> Install zip "<b><i>YOUR-OC-RANGE-quick-n-easy-checkout.ocmod.zip</i></b>" with extension installer.</li>
    <li><b>Refresh OCMod changes:</b> Go to "<b>Extensions > Modifications</b>" and press blue refresh button.</li>
    <li><b>Install module:</b> Go to "<b>Extensions > Extensions > Filter by 'Modules'</b>" and install module. Full process <a href="https://youtu.be/7yEWjvcLRuI?list=PLCnohRczJgCsCdSvssdLyajVJVuaO3-zN" target="_blank">this video</a>.</li>
</ol>

<h2>OPENCART 1.5.X INSTALL PROCESS</h2>

<ol>
	<li><b><u>Install VQMod:</u></b> If you didn't install VQMod in your shop install it:
		<ul>
			<li><a href="https://github.com/vqmod/vqmod/releases" target="_blank">Download</a></li>
			<li><a href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart" target="_blank">Install tutorial</a></li>
		</ul>
	</li>
	<li><b><u>Upload vqmod file:</u></b> Open main ZIP file, extract XML file called "<b>devmanextensions_quick_n_easy_checkout_oc15x.xml</b>" and upload it to "<b>/vqmod/xml/</b>"</li>
	<li><b><u>Upload extension files:</u></b> Extract "<b>1.5.X-to-2.2.0.0-quick-n-easy-checkout.ocmod.zip</b>" and upload all content inside folder "<b>upload</b>" to your root path site.</li>
</ol>

<h2 id="install_manually">Manual installation</h2>
<ol>
	<li>Extract your .zip file that you can't install by Opencart installer.</li>
	<li>Upload by FTP all content of folder "upload" to your root path site. <b><u>MAKE SURE THAT ALL FILES WAS UPLOADED CORRECTLY</u></b>.</li>
	<li>Compress file "install.xml" + empty folder called "upload" and set name "install.ocmod.zip".</li>
	<li>Use Opencart extension installer to upload this zip file.</li>
	<li><b>Refresh OCMod changes:</b> Go to "<b>Extensions > Modifications</b>" and press blue refresh button.</li>
</ol>


<h2 id="actions_old_users">Actions required to old users<br>before update to version 5.0.0 or more</h2>

<p>Delete all files/folder from old version, is possible that some element in this list doesn't exist in your website, all depends of your Opencart version</p>
<br>
    <p><b>REMOVE OCMOD - Only to Quick n Easy checkout 4.0.0 to 4.0.3</b></p>
    <p>If you have Opencart 2 or 3, go to Extensions > Modifications and delete old mofication "Quick n Easy checkout" or "Perfect One checkout" (if you have old version).</p>
    <br>
    <p><b>ADMIN FOLDER - REMOVING FILES</b></p>
<ul>
    <li>/admin/controller/module/perfect_one_checkout.php</li>
    <li>/admin/controller/extension/module/perfect_one_checkout.php</li>
    <li>/admin/language/english/extension/module/perfect_one_checkout.php</li>
    <li>/admin/language/en-gb/extension/module/perfect_one_checkout.php</li>
    <li>/admin/language/english/module/perfect_one_checkout.php</li>
    <li>/admin/language/en-gb/module/perfect_one_checkout.php</li>
    <li>/admin/view/template/module/perfect_one_checkout.tpl</li>
    <li>/admin/view/template/module/perfect_one_checkout.twig</li>
    <li>/admin/view/template/extension/module/perfect_one_checkout.tpl</li>
    <li>/admin/view/template/extension/module/perfect_one_checkout.twig</li>
    <li>/admin/view/javascript/devmanextensions_perfect_one_checkout.js</li>
    <li>/admin/view/stylesheet/devmanextensions_perfect_one_checkout.css</li>
</ul>
    <br>
<p><b>CATALOG FOLDER - REMOVING FILES</b></p>
<ul>
    <li>/catalog/controller/extension/module/perfect_one_checkout.php</li>
    <li>/catalog/controller/module/perfect_one_checkout.php</li>
    <li>/catalog/model/extension/module/perfect_one_checkout.php</li>
    <li>/catalog/view/theme/perfect_one_checkout (folder)</li>
    <li>/catalog/view/theme/default/template/checkout/perfect_one_checkout (folder)</li>
    <li>/catalog/controller/checkout/perfect_one_checkout.php</li>
    <li>/catalog/model/checkout/perfect_one_checkout.php</li>
    <li>/catalog/view/devmanextensions (folder)</li>
    <li>/catalog/view/perfect_one_checkout (folder)</li>
</ul>
    <br>
<p><b>SYSTEM FOLDER - REMOVING FILES</b></p>
<ul>
    <li>/system/library/devmanextensions_poc (folder)</li>
    <li>/system/devmanextensions_perfect_one_checkout.ocmod.xml (only will be present in QnE checkout versions 1.0.0 to 3.7.2)</li>
</ul>
</div>
</article>