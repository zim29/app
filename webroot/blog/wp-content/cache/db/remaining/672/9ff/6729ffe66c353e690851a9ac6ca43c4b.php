`��f<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:337:"SELECT DISTINCT t.term_id, tr.object_id
			 FROM wp_terms AS t  INNER JOIN wp_term_taxonomy AS tt ON t.term_id = tt.term_id INNER JOIN wp_term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
			 WHERE tt.taxonomy IN ('category', 'post_tag', 'post_format') AND tr.object_id IN (233, 433, 923)
			 ORDER BY t.name ASC
			 ";s:11:"last_result";a:5:{i:0;O:8:"stdClass":2:{s:7:"term_id";s:2:"11";s:9:"object_id";s:3:"433";}i:1;O:8:"stdClass":2:{s:7:"term_id";s:1:"1";s:9:"object_id";s:3:"923";}i:2;O:8:"stdClass":2:{s:7:"term_id";s:2:"18";s:9:"object_id";s:3:"433";}i:3;O:8:"stdClass":2:{s:7:"term_id";s:2:"18";s:9:"object_id";s:3:"923";}i:4;O:8:"stdClass":2:{s:7:"term_id";s:2:"18";s:9:"object_id";s:3:"233";}}s:8:"col_info";a:2:{i:0;O:8:"stdClass":13:{s:4:"name";s:7:"term_id";s:7:"orgname";s:7:"term_id";s:5:"table";s:1:"t";s:8:"orgtable";s:8:"wp_terms";s:3:"def";s:0:"";s:2:"db";s:14:"blog_wordpress";s:7:"catalog";s:3:"def";s:10:"max_length";i:2;s:6:"length";i:20;s:9:"charsetnr";i:63;s:5:"flags";i:49185;s:4:"type";i:8;s:8:"decimals";i:0;}i:1;O:8:"stdClass":13:{s:4:"name";s:9:"object_id";s:7:"orgname";s:9:"object_id";s:5:"table";s:2:"tr";s:8:"orgtable";s:21:"wp_term_relationships";s:3:"def";s:0:"";s:2:"db";s:14:"blog_wordpress";s:7:"catalog";s:3:"def";s:10:"max_length";i:3;s:6:"length";i:20;s:9:"charsetnr";i:63;s:5:"flags";i:49185;s:4:"type";i:8;s:8:"decimals";i:0;}}s:8:"num_rows";i:5;s:10:"return_val";i:5;}