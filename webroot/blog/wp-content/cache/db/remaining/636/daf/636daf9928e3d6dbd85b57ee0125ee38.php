a��f<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:254:"SELECT  wp_comments.comment_ID
			 FROM wp_comments JOIN wp_posts ON wp_posts.ID = wp_comments.comment_post_ID
			 WHERE ( comment_approved = '1' ) AND  wp_posts.post_status IN ('publish')
			 
			 ORDER BY wp_comments.comment_date_gmt DESC
			 LIMIT 0,5";s:11:"last_result";a:2:{i:0;O:8:"stdClass":1:{s:10:"comment_ID";s:1:"7";}i:1;O:8:"stdClass":1:{s:10:"comment_ID";s:1:"6";}}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:10:"comment_ID";s:7:"orgname";s:10:"comment_ID";s:5:"table";s:11:"wp_comments";s:8:"orgtable";s:11:"wp_comments";s:3:"def";s:0:"";s:2:"db";s:14:"blog_wordpress";s:7:"catalog";s:3:"def";s:10:"max_length";i:1;s:6:"length";i:20;s:9:"charsetnr";i:63;s:5:"flags";i:49699;s:4:"type";i:8;s:8:"decimals";i:0;}}s:8:"num_rows";i:2;s:10:"return_val";i:2;}