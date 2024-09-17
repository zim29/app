<?php if (!empty($id)) {
    echo $this->Html->css(
        array(
            'pages/video-tutorials.css?' . date('YmdHis')
        )
    );
    echo $this->Html->script(
        array(
            'pages/video-tutorials.js?' . date('YmdHis')
        )
    );
    echo '<script>
            var id = "'.$id.'";
            var language = "'.$language.'";
            get_videos(id, language);
     </script>';
} ?>
<article>
    <header class="jumbotron">
        <h1><?= __('Video tutorials'); ?></h1>
    </header>
<div class="col-md-6 video model" style="display:none;">
    <span class="title">VIDEO_TITLE</span>
    <iframe width="555" height="388" src="https://www.youtube.com/embed/VIDEO_ID?hl=VIDEO_LANGUAGE&cc_load_policy=1&persist_hl=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>
<div class="container theme-showcase" role="main">
    <?php if (empty($id)) echo 'YOUTUBE VIDEOS NOT FOUND'; ?>
    <div style="clear: both;"></div>
    <div class="row language_selector">
        <div class="col-md-4"></div>
        <div class="col-md-4 container_language">
            <div class="form-group">
                <label class="col-sm-2 control-label"><i class="fa fa-language" aria-hidden="true"></i></label>
                <div class="col-sm-10">
                    <select name="language" class="form-control">
                        <option value="en_BG" <?= $language == 'en_BG' ? 'selected="selected"':''; ?>>English</option>
                        <option value="ru_RU" <?= $language == 'ru_RU' ? 'selected="selected"':''; ?>>русский</option>
                        <option value="es_ES" <?= $language == 'es_ES' ? 'selected="selected"':''; ?>>Español</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row videos">
    </div>
</div>
</article>