<article>
    <header class="jumbotron">
        <h1><?= __('Install tutorial Import/Export PRO'); ?></h1>
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

<h2>SERVER REQUIREMENTS</h2>
<ul>
    <li>PHP <b>5.5.0</b> or higher</li>
    <li>PHP extension <b>php_zip</b> enabled</li>
    <li>PHP extension <b>php_xmlreader</b> enabled</li>
    <li>PHP extension <b>php_simplexml</b> enabled</li>
</ul>


<h2>Installation for Opencart 2.X - 3.X</h2>

<ol>
    <li>Unzip the main file “<b>Import Export Pro V.X.X.X.zip</b>”, which contains individual ZIP files for each version, as indicated by the file names.</li>
    <li>Perform the installation through “<b>Extensions > Installer</b>”, uploading ZIP.</li>
    <li><b>Refresh OCMOD changes</b>.</li>
    <li><b><u>IMPORTANT Opencart 3 users:</u></b> if you are using Opencart 3, reset your cache from Dashboard. If you have an external cache generator, refresh it too.</li>
    <li>Can see the whole process in the <a target="_blank" href="https://www.youtube.com/watch?v=41L0ZW0i2r8">next video</a>.</li>
</ol>

<h2>Installation for Opencart 1.5.X</h2>

<ol>
	<li><b><u>Install VQMod:</u></b> If you didn't install VQMod in your shop install it:
		<ul>
			<li><a href="https://github.com/vqmod/vqmod/releases" target="_blank">Download</a></li>
			<li><a href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart" target="_blank">Install tutorial</a></li>
		</ul>
	</li>
	<li><b><u>Upload vqmod file:</u></b> Open main ZIP file, extract XML file called "<b>import_export_pro_oc15x.xml</b>" and upload it to "<b>/vqmod/xml/</b>"</li>
	<li><b><u>Upload extension files:</u></b> Extract "<b>1.5.X-to-2.2.0.0-import-xls-pro.ocmod.zip</b>" and upload all content inside folder "<b>upload</b>" to your root path site.</li>
	<li><b><u>IMPORTANT:</u></b> Make sure that all files was uploaded correctly or otherwise you may see "require" PHP warning errors when you access this module.<br>
	</li>
</ol>

<?php /*<h2>COMMON PROBLEMS DURING OR AFTER THE INSTALLATION (Opencart 2.X and 3.X):</h2>
<div class="content_inner">
    <b><i>I receive errors from “Opencart Installer” when I try to install the file as “Invalid file type”, “File could not be uploaded”:</i></b>
    <p>The zip file that you are trying to install exceeds the upload limit on your server. To resolve this problem, you can either increase this parameter or perform a  <a href="#install_manually">manual installation</a>.</p>
</div>

<div class="content_inner">
    <b><i>It seems that everything went well in the installation process but i can not see the extension in “Extensions > Extensions”:</i></b>
    <p>This can be mainly due to two reasons:
        <ol>
            <li><b>You may be using a cache system on your website.</b> Opencart 3 has a default cache system, this cache can be refreshed from the Dashboard, by clicking on the blue button located in top / right (icon with a gear inside). If you use another external means of cache, refresh this cache.</li>
            <li>ome files were not uploaded. It may happen that some files did not upload properly in spite of the success message at the end of the process. We recommend to review the directives of the previous point and if not possible, proceed to the <a href="#install_manually">manual installation</a>.</li>
        </ol>
    </p>
</div>
 */ ?>

<?php /*<h2 id="actions_old_users">Actions required to old users<br>before update to version 7.6.0 or more</h2>

<ol>
	<li><b>Delete OCMod/VQMod files:</b>
		<ul>
			<li>If you are using OCMod:
				<ul>
			        <li>/system/import_xls_products_tool.ocmod.xml</li>
			    </ul>
	        </li>
	    	<li>If you are using VQMod:
	    		<ul>
	        		<li>/vqmod/xml/import_xls_products_tool_oc15x.xml</li>
	        	</ul>
	        </li>
		</ul>
	</li>
	<li><b>Delete next files:</b>
		<ul>
            <li>/admin/controller/tool/import_xls.php</li>
            <li>/admin/model/tool/import_xls.php</li>
            <li>/admin/model/tool/export_xls.php</li>
            <li>/admin/view/template/tool/import_xls.tpl</li>
            <li>/admin/view/template/tool/import_xls.twig</li>
            <li>/admin/view/template/tool/import_xls_files  (full folder)</li>
		</ul>
	</li>
</ol>*/ ?>

<?php /*
<h2 id="install_manually">Manual installation</h2>
    <ol>
        <li>Unzip the main file “<b>Import Export Pro V.X.X.X.zip</b>”</li>
        <li>Unzip the file <b>1.5.X-to-2.2.0.0-import-xls-pro.ocmod.zip</b></li>
        <li>Upload the contents of the “upload” folder to the root of your server. <b><u>MAKE SURE THAT ALL FILES HAVE BEEN UPLOADED CORRECTLY</u></b>.</li>
        <li>Install the file “<b>manual_install.ocmod.zip</b>” from “<b>Extensions > Installer</b>”.</li>
        <li><b>Refresh OCMOD changes</b>.</li>
        <li><b><u>IMPORTANT Opencart 3 users:</u></b> if you are using Opencart 3, reset your cache from Dashboard. If you have an external cache generator, refresh it too.</li>
    </ol>
</div>
 */ ?>
</article>
