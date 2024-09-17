<?php if (!empty($changelogs)){ ?>
	</ul>
		<?php foreach ($changelogs as $key => $cl) {
			$lines = explode(PHP_EOL, $cl['Changelog']['text']);

			echo '<li style="list-style:none;"><b><u>Version '.$cl['Changelog']['version'].' - '.date('d/m/Y', strtotime($cl['Changelog']['created'])).'</u></b>';
				echo '<ul>';
					foreach ($lines as $key2 => $line) {
						echo '<li>'.$line.'</li>';
					}
				echo '</ul>';
			echo '</li>';
		} ?>
	</ul>
<?php } else{ ?>
	No changelogs.
<?php } ?> 