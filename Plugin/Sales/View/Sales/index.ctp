<h1>Sales</h1>
<?php echo $this->Html->script(
    array(
    'Sales.index.js?'.date('YmdHis')
    )
); ?>

<style>
    i.support, i.not_support {
        font-size: 30px;
    }
    i.support, i.fa-save {
        color: #28a745;
    }
    i.not_support, i.fa-trash {
        color: #dc3545;
    }
</style>
<?php

$user = $this->Session->read('Auth.User');
$is_admin = $user['role'] == 'king';

?>

<?php if($is_admin) { ?>
<div class="row">
    <div class="form-group">
        <label class="col-sm-2 control-label">Total in year:</label>
        <div class="col-sm-10"><b><?php echo number_format($total_sum, 2, '.', ''); ?>€</b></div>
    </div>
</div>
<?php } ?>

<?php if($is_admin) { ?>
<div id="char"></div>
<?php } ?>

<?php $link_index = Router::url(['plugin' => 'sales', 'controller' => 'sales', 'action' => 'index']); ?>

<form action="<?= $link_index ?>" role="form" id="search" class="form-horizontal" method="post" accept-charset="utf-8">
  <div class="row">
    <div class="col-md-2">
      <input type="text" class="form-control" placeholder="Search..." name="search[order_id]" value="<?= !empty($this->request->data['search']['order_id']) ? $this->request->data['search']['order_id'] : ''; ?>">
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <a href="javascript:{}" onclick="jQuery(this).closest('form').submit();" class="btn btn-lg btn-success save" style="float: right;"><i class="fa fa-search"></i>Filter</a>
    </div>
  </div>
</form>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
<?php if ( count($sales) >= 1 ){ ?>
    <table class="table table-striped">
        <thead>
          <tr>
            <th><?php echo $this->Paginator->sort('Sale.order_id', 'Order ID'); ?></th>
              <?php /*if($is_admin) { ?>
                    <th><?php echo $this->Paginator->sort('Sale.extension_id', 'Ext. ID'); ?></th>
              <?php }*/ ?>
			  <th>OC.V.</th>
            <th><?php echo $this->Paginator->sort('Sale.extension_name', 'Ext. Name'); ?></th>
              <th><?php echo $this->Paginator->sort('Sale.buyer_username', 'Buyer Username'); ?></th>
              <?php if($is_admin) { ?>
                    <th><?php echo $this->Paginator->sort('Sale.buyer_email', 'Buyer Email'); ?></th>
                    <th><?php echo $this->Paginator->sort('Sale.total', 'Total'); ?></th>
              <?php } ?>
            <th><?php echo $this->Paginator->sort('Sale.date_added', 'Date added'); ?></th>
              <th>Support</th>
              <th>Download</th>
            <th>Add domain</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        	<?php foreach ($sales as $key => $sale) {
        	    $media = $is_admin && array_key_exists('media', $sale['Sale']);
        		echo '<tr>';
        			echo '<td>'.($media ? '<span data-html="true" data-toggle="tooltip" title = "'.$sale['Sale']['media'].'">':'').$sale['Sale']['order_id'].($media ? '</span>' : '').'</td>';

        			/*if($is_admin)
                        echo '<td>'.$sale['Sale']['extension_id'].'</td>';*/
					echo '<td>'.(!empty($sale['Sale']['system_version']) ? implode("\n",explode("|",$sale['Sale']['system_version'])) : '').'</td>';
                    echo '<td><span data-html="true" data-toggle="tooltip" title="'.$sale['Sale']['extension_name'].'">'.substr($sale['Sale']['extension_name'], 0, 12).'</span></td>';
                    echo '<td>'.$sale['Sale']['buyer_username'].'</td>';

                    if($is_admin) {
                        echo '<td><a href="mailto:' . $sale['Sale']['buyer_email'] . '">'.$sale['Sale']['buyer_email'].'</a></td>';
                        echo '<td>$' . number_format($sale['Sale']['total'], 2, '.', '') . '</td>';
                    }
                    echo '<td>' . date('d-m-Y H:i:s', strtotime($sale['Sale']['date_added'])) . '</td>';
                    echo '<td style="text-align: center;"><i data-html="true" data-toggle="tooltip" title="'.$sale['Sale']['support_expired_message'].'" class="fa fa-'.(!$sale['Sale']['support_expired'] ? 'check-circle-o support' : 'times-circle-o not_support').'"></i></td>';
                    echo '<td style="text-align: center;"><i data-html="true" data-toggle="tooltip" title="'.$sale['Sale']['download_expired_message'].'" class="fa fa-'.(!$sale['Sale']['download_expired'] ? 'check-circle-o support' : 'times-circle-o not_support').'"></i></td>';
        			$domains = str_replace('|', '<br>', $sale['Sale']['domain']);
        			echo '<td><input name="new_domain" placeholder="Insert new domain" class="form-control"><span class="domains">'.$domains.'</span></td>';
        			echo '<td>
                            <a href="javascript:{}" class="add_domain" onclick="addDomain(\''.$sale['Sale']['order_id'].'\', $(this))"><i class="fa fa-save" title="Add domain"></i></a>
                            <a href="javascript:{}" class="renew" onclick="renew(\''.$sale['Sale']['order_id'].'\')"><i class="fa fa-refresh" title="Renew"></i></a>
                            <a target="_blank" href="https://devmanextensions.com/download-center?download_id='.$sale['Sale']['download_id'].'"><i class="fa fa-download" title="Get download link"></i></a>
                            <div style="clear: both; height: 10px;"></div><a href="javascript:{}" onclick="clean_domain($(this))"><i class="fa fa-trash" title="WARNING: Clean domains"></i></a>
                    </td>';
        		echo '</tr>';
        	} ?>
        </tbody>
    </table>
    <?php echo $this->element('pager'); ?>
<?php }
else
{
	echo 'No Sales found';
}
?>

<?php if($is_admin) { ?>
<script type="text/javascript">
    $(function () {
        Highcharts.chart('char', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Total sales'
            },
            xAxis: {
                categories: [<?php foreach ($data_to_char['total'] as $date => $total) { echo '"'.$date.'"'.','; } ?>]
            },
            yAxis: {
                title: {
                    text: 'EUROS'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [
                {
                    name: 'Sales <?= $repast_year ?>',
                    data: [<?php foreach ($data_to_char[$repast_year] as $date => $total) { echo $total.','; } ?>]
                },
                {
                    name: 'Sales <?= $past_year ?>',
                    data: [<?php foreach ($data_to_char[$past_year] as $date => $total) { echo $total.','; } ?>]
                },
                {
                    name: 'Sales <?= $current_year ?>',
                    data: [<?php foreach ($data_to_char['total'] as $date => $total) { echo $total.','; } ?>]
                },
                /*{
                    name: 'Sales <?= $current_year; ?>',
                    data: [<?php foreach ($data_to_char['sales'] as $date => $total) { echo $total.','; } ?>]
                },
                {
                    name: 'Custom works <?= $current_year; ?>',
                    data: [<?php foreach ($data_to_char['personal_works'] as $date => $total) { echo $total.','; } ?>]
                }*/
            ]
        });
    });
</script>
<?php } ?>
