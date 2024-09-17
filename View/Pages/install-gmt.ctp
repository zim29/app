<article>
    <header class="jumbotron">
        <h1><?= __('Install tutorial Google Marketing Tools'); ?></h1>
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

<b>IF YOU COME FROM GOOGLE MARKETING TOOLS VERSION LESS THAN 10.0, <a href="#actions_old_users">CLICK HERE, YOU NEED DO THESE ACTIONS BEFORE START INSTALL PROCESS</a></b>

<h2>OPENCART 2.X - 3.X Install process</h2>

<ol>
<li><b>Install extension with Opencart extension installer:</b> Install zip "<b><i>YOUR-OC-RANGE-google-marketing-tools.ocmod.zip</i></b>" with extension installer.
<br>
<i>* If you recived the next error "File could not be uploaded!" you need increase "max_upload_size" value in your server configuration, maybe is possible only editing your php.ini</i></li>
<li><b>Refresh OCMod changes:</b> Go to "<b>Extensions > Modifications</b>" and press blue refresh button.</li>
<li><b>Install module:</b> Go to "<b>Extensions > Extensions > Filter by 'Modules'</b>" and install Google Marketing Tools module. Full process <a href="https://youtu.be/m4nc7VSfxJQ?list=PLCnohRczJgCsHW8RG1o9L_6OW0AjReAdE" target="_blank">this video</a>.</li>
<li><b>ONLY USER WITH OC 2.0.0.0 TO 2.1.0.2:</b> Open your file "/index.php" and add follow code just before "<b><i>// Front Controller</i></b>"<br>
<br><pre>require_once(DIR_SYSTEM.'library/google_marketing_tools/includes/startup_libraries.php');</pre></li>
</ol>

<h2>OPENCART 1.5.X INSTALL PROCESS</h2>

<ol>
	<li><b><u>Install VQMod:</u></b> If you didn't install VQMod in your shop install it:
		<ul>
			<li><a href="https://github.com/vqmod/vqmod/releases" target="_blank">Download</a></li>
			<li><a href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart" target="_blank">Install tutorial</a></li>
		</ul>
	</li>
	<li><b><u>Upload vqmod file:</u></b> Open main ZIP file, extract XML file called "<b>devmanextensions_google_marketing_tools_oc15x.xml</b>" and upload it to "<b>/vqmod/xml/</b>"</li>
	<li><b><u>Upload extension files:</u></b> Extract "<b>1.5.X-to-2.2.0.0-google-marketing-tools.ocmod.zip</b>" and upload all content inside folder "<b>upload</b>" to your root path site.</li>
	<li><b><u>Add libraries manually:</u></b> Open your main shop ifle "<b>/index.php</b>" and add follow code just before "<b><i>// Front Controller</i></b>"<br>
<br><pre>require_once(DIR_SYSTEM.'library/google_marketing_tools/includes/startup_libraries.php');</pre>
	</li>
</ol>

<h2 id="actions_old_users">Actions required to old users<br>before update to version 10.0 or more</h2>

<ol>
	<li><b>Save in your computer your feed backups files (if you are using feeds):</b></li>
	<ul>
	    <li>/catalog/controller/feed/criteo_base_pro_configurations_backup.json</li>
	    <li>/catalog/controller/feed/facebook_base_pro_configurations_backup.json</li>
	    <li>/catalog/controller/feed/google_base_pro_configurations_backup.json</li>
	    <li>/catalog/controller/feed/google_business_base_pro_configurations_backup.json</li>
	    <li>/catalog/controller/feed/twenga_base_pro_configurations_backup.json</li>
	</ul>

	<li><b>Delete OCMod/VQMod files:</b>
		<ul>
			<li>If you are using OCMod:
				<ul>
			        <li>/system/devmanextensions_oc23x_compatibility.ocmod.xml</li>
			        <li>/system/devmanextensions_google_marketing_tools.ocmod.xml</li>
			    </ul>
	        </li>
	    	<li>If you are using VQMod:
	    		<ul>
	        		<li>/vqmod/xml/devmanextensions_google_marketing_tools.xml</li>
	        	</ul>
	        </li>
		</ul>
	</li>
	<li><b>Delete next files:</b>
		<ul>
		    <li>admin/controller/module/google_all.php</li>
		    <li>admin/language/english/module/google_all.php</li>
		    <li>admin/language/en-gb/module/google_all.php</li>
		    <li>admin/view/template/module/google_all.tpl</li>
		    <li>admin/view/template/module/google_all.twig</li>
		    <li>catalog/controller/feed/criteo_base_pro.php and .json files that start with same name</li>
		    <li>catalog/controller/feed/facebook_base_pro.php and .json files that start with same name</li>
		    <li>catalog/controller/feed/google_base_pro.php and .json files that start with same name</li>
		    <li>catalog/controller/feed/gooble_business_base_pro.php and .json files that start with same name</li>
		    <li>catalog/controller/feed/twenga_base_pro.php and .json files that start with same name</li>
		    <li>catalog/controller/feed/google_all_txt_merchantcenter_categories  (delete folder).</li>
		    <li>catalog/model/feed/google_base_pro.php</li>
		</ul>
	</li>

	<li><b>Upload your backups saved in your computer:</b> Dont forget, after follow the next install tutorial, upload again your backups .json files to not loose your feed configurations in "catalog/controller/extension/module/feed"</li>
</ol>
</div>
</article>