a��f<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:353:"SELECT   wp_posts.ID
					 FROM wp_posts  LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id)
					 WHERE 1=1  AND ( 
  wp_term_relationships.term_taxonomy_id IN (6)
) AND wp_posts.post_type = 'nav_menu_item' AND ((wp_posts.post_status = 'publish'))
					 GROUP BY wp_posts.ID
					 ORDER BY wp_posts.menu_order ASC
					 ";s:11:"last_result";a:4:{i:0;O:8:"stdClass":1:{s:2:"ID";s:2:"50";}i:1;O:8:"stdClass":1:{s:2:"ID";s:2:"51";}i:2;O:8:"stdClass":1:{s:2:"ID";s:2:"52";}i:3;O:8:"stdClass":1:{s:2:"ID";s:2:"53";}}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:2:"ID";s:7:"orgname";s:2:"ID";s:5:"table";s:8:"wp_posts";s:8:"orgtable";s:8:"wp_posts";s:3:"def";s:0:"";s:2:"db";s:14:"blog_wordpress";s:7:"catalog";s:3:"def";s:10:"max_length";i:2;s:6:"length";i:20;s:9:"charsetnr";i:63;s:5:"flags";i:32801;s:4:"type";i:8;s:8:"decimals";i:0;}}s:8:"num_rows";i:4;s:10:"return_val";i:4;}