<html>
    <head>
        <title>Videos</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript">
            var yt_api_key = 'AIzaSyDaq7YP-CdW7B3vDurbBcYI82dn-PLF6mg';
            var yt_video_id = 'PLCnohRczJgCspM7ZsCt2wq7ckqM0Skr90';
            var part = 'snippet,contentDetails';
            var yt_snippet_endpoint = "https://www.googleapis.com/youtube/v3/playlistItems?part=" + part + "&playlistId=" + yt_video_id + "&key=" + yt_api_key + "&maxResults=50";
            var embed_video = '<iframe width="1265" height="720" src="https://www.youtube.com/embed/VIDEO_ID" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            var jqxhr = $.getJSON(yt_snippet_endpoint)
                .done(function(data) {
                    $.each(data.items, function (index, value) {
                        var video_id = value.contentDetails.videoId;
                        var res = embed_video.replace("VIDEO_ID", video_id);
                        $('div.videos').append(res);
                    });
                })
                .fail(function() {
                    console.log("error, see network tab for response details");
                });

        </script>
    </head>
    <body>
    <div class="videos">

    </div>
    </body>
</html>