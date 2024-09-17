<?php 
$span = isset($span) ? $span : 8; 
$page = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1; 
?>
<ul class="pagination">
	<?php 
	echo $this->Paginator->prev('&laquo', array('escape' => false, 'tag' => 'li' ), '<a onclick="return false;">&laquo</a>', array('class'=>'disabled prev', 'escape' => false, 'tag' => 'li' ));
	$count = $page + $span;
	$i = $page - $span;
	while ($i < $count) {
		$options = $i == $page ? ' class="active"' : '';
		if ($this->Paginator->hasPage($i) && $i > 0) { ?>
		<li<?= $options; ?>><?= $this->Html->link($i, array("page" => $i)); ?></li>
		<?php };
		$i += 1;
	}
	echo $this->Paginator->next('&raquo', array('escape' => false, 'tag' => 'li'), '<a onclick="return false;">&raquo</a>', array('class' => 'disabled next', 'escape' => false,'tag' => 'li'));
	?>
</ul>