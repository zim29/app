<article>
    <header class="jumbotron">
        <h1><?= __('Install tutorial Optional email notification'); ?></h1>
    </header>
<div class="container theme-showcase" role="main">

<h2>Installation for Opencart 2.X - 3.X</h2>

<ol>
    <li>Unzip the main file “<b>Optional Email Notification V.X.X.X.zip</b>”, which contains individual ZIP files for each version, as indicated by the file names.</li>
    <li>Perform the installation through “<b>Extensions > Installer</b>”, uploading ZIP.</li>
    <li><b>Refresh OCMOD changes</b>.</li>
    <li><b><u>IMPORTANT Opencart 3 users:</u></b> if you are using Opencart 3, reset your cache from Dashboard. If you have an external cache generator, refresh it too.</li>
    <li>Go to "Extensions > Extensions" install extension and enable it.</li>
</ol>

<h2>Installation for Opencart 1.5.X</h2>

<ol>
	<li><b><u>Install VQMod:</u></b> If you didn't install VQMod in your shop install it:
		<ul>
			<li><a href="https://github.com/vqmod/vqmod/releases" target="_blank">Download</a></li>
			<li><a href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart" target="_blank">Install tutorial</a></li>
		</ul>
	</li>
	<li><b><u>Upload vqmod file:</u></b> Open main ZIP file, extract XML file called "<b>devmanextensions_order_edit_optional_email_oc15x.xml</b>" and upload it to "<b>/vqmod/xml/</b>"</li>
	<li><b><u>Upload extension files:</u></b> Extract "<b>1.5.X-to-2.2.0.0-optional-email-notification.ocmod.zip</b>" and upload all content inside folder "<b>upload</b>" to your root path site.</li>
    <li>Go to "Extensions > Extensions" install extension and enable it.</li>
</ol>
</div></article>