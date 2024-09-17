<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<h1 class="main"><?php echo $message; ?></h1>
<div class="container theme-showcase" role="main">
	<p class="error">
		<strong><?php echo __d('cake', 'Error'); ?>: </strong>
		<?php printf(
			__d('cake', 'The requested address %s was not found on this server.'),
			"<strong>'{$url}'</strong>"
		); ?>
	</p>
	<?php
	if (Configure::read('debug') > 0):
		echo $this->element('exception_stack_trace');
	endif;
	?>
	<br><br>
	<div class="row">
	  <div class="col-md-12">
	    <center><?php echo $this->Html->image('layouts/frontend/404/link_not_found.png', array('alt' => '404 not found', 'class' => 'img-responsive')); ?> </center>
	  </div>
	</div>
</div>