<?php  
$params = $this->paginator->params();
$count = $params['pageCount'];
if($count > 1) {
?>
	<div class="row">	
		<div class="col-sm-12">
			<?php
			echo 'Pages: ';
			echo $this->paginator->first($this->html->image('generico/first.png',array('border' => 0)),array('escape'=>false));  
			echo $this->paginator->prev($this->html->image('generico/prev.png',array('border' => 0)),array('escape'=>false));

			echo "&nbsp;&nbsp;";
		    
			echo $this->paginator->numbers(array('separator'=>' - ')); 
			
			echo "&nbsp;&nbsp;";
		    
			echo $this->paginator->next($this->html->image('generico/next.png',array('border' => 0)),array('escape'=>false));
			echo $this->paginator->last($this->html->image('generico/last.png',array('border' => 0)),array('escape'=>false));
			
			echo '&nbsp;&nbsp;'.$this->paginator->counter(array('format' => '%page% of %pages%'));
				
			?>
		</div>
		<div class="col-sm-12">
			<?php
			
			echo $this->paginator->counter(array('format' => 'Total: %count% records'));
	        
			?>
		</div>
		
	</div>

<?php 
}
?>