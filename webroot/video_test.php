<html>
<head>
<title>Video examples</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>
<body>

<label><b>Select your language</b></label>
<select name="language" id="" onchange="$('iframe.video').hide();$('iframe.video.'+$(this).val()).show();">
    <option value="en_GB">English</option>
    <option value="ru_RU">Russian</option>
</select><br><br>
<div style="clear:both"></div>
<iframe class="video en_GB" width="806" height="453" src="https://www.youtube.com/embed/cqwYjoDwgh0?hl=en_GB&cc_load_policy=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<iframe class="video ru_RU" style="display: none;" width="806" height="453" src="https://www.youtube.com/embed/cqwYjoDwgh0?hl=ru_RU&cc_load_policy=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</body>
</html>