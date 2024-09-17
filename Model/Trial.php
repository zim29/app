<?php
    class Trial extends AppModel
    {
        var $name = 'Trial';
        var $useTable = 'trials';

        var $belongsTo = array(
			'Extension' => array(
				'className' => 'Extensions.Extension',
				'foreignKey' => false,
				'conditions' => array('Trial.extension_id = Extension.id'),
			)
		);

        function check($extension_id, $domain) {
            $result = $this->find('first', array('conditions' => array('Trial.extension_id' => $extension_id, 'Trial.domain' => $domain), 'order' => array('Trial.created DESC')));

            if(empty($result)) {
                throw new Exception('not_found');
            }else {
                if(!$result['Trial']['activated'])
                    throw new Exception('Waiting validate, email with link validate sent to <b>'.$result['Trial']['customer_email'].'</b>. Check SPAM folder.');
                $days_trial = $result['Extension']['trial'];
                $date_trial_expired = strtotime(date('Y-m-d H:i:s', strtotime($result['Trial']['created']. ' + '.$days_trial.' days')));

                $today = strtotime(date('Y-m-d H:i:s'));

                $datediff = $date_trial_expired - $today;
                $days = round($datediff / (60 * 60 * 24));

                $this->query('UPDATE intranet_trials SET form_recovered = form_recovered+1 WHERE extension_id = "'.$extension_id.'" AND domain = "'.$domain.'"');

                return $days;
            }
        }
    }
?>