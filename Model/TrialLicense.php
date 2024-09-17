<?php
    class TrialLicense extends AppModel
    {
        var $name = 'TrialLicense';
        var $useTable = 'trial_licenses';

        var $belongsTo = array(
			'Extension' => array(
				'className' => 'Extensions.Extension',
				'foreignKey' => false,
				'conditions' => array('TrialLicense.extension_id = Extension.id'),
			)
		);

        function check($license_id, $extension_id, $domain) {
            //throw new Exception($domain); die;
            $conditions = array('TrialLicense.license_id' => $license_id, 'TrialLicense.extension_id' => $extension_id, 'TrialLicense.domain' => $domain);
            $result = $this->find('first', array('conditions' => $conditions, 'order' => array('TrialLicense.created DESC')));

            if(empty($result)) {
                throw new Exception('Trial not found');
            }else {
                if(!$result['TrialLicense']['activated'])
                    throw new Exception('Waiting trial validate, email with link validate sent to <b>'.$result['TrialLicense']['customer_email'].'</b>. Check <b>SPAM</b> folder.');

                $days_trial = $result['Extension']['trial'];
                $end_date = date('Y-m-d H:i:s', strtotime($result['TrialLicense']['modified']. ' + '.$days_trial.' days'));
                $date_trial_expired = strtotime($end_date);
                $end_date_legible = date('d/m/Y H:i:s', strtotime($end_date));
                $today = strtotime(date('Y-m-d H:i:s'));

                $datediff = $date_trial_expired - $today;
                $days = $datediff / (60 * 60 * 24);
                if($days >= 0) {
                    $this->query('UPDATE intranet_trial_licenses SET form_recovered = form_recovered+1 WHERE extension_id = "'.$extension_id.'" AND domain = "'.$this->get_domain($domain).'"');
                    return ceil($days);
                    //return $end_date_legible;
                } else {
                    throw new Exception(sprintf(_('Trial expired on <b>%s</b>. Get a <b>FULL</b> licese <a target="_blank" href="%s"><b>in this link</b></a>!'), $end_date_legible, 'https://devmanextensions.com/'.$result['Extension']['seo_url']));
                }
            }
        }

        function create_trial($extension_id, $domain, $customer_name, $customer_email) {
            $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';

            $result = $this->find('first', array('conditions' => array('TrialLicense.extension_id' => $extension_id, 'TrialLicense.domain' => $domain), 'order' => array('TrialLicense.created DESC')));

            if(empty($result)) {
                $this->Extension = ClassRegistry::init('Extensions.Extension');
                $extension = $this->Extension->findById($extension_id);

                $to_save = array(
                    'TrialLicense' => array(
                        'license_id' => 'trial-'.$this->generateRandomString(10),
                        'extension_id' => $extension_id,
                        'extension_name' => $extension['Extension']['name'],
                        'domain' => $domain,
                        'customer_name' => $customer_name,
                        'customer_email' => $customer_email,
                    )
                );
                
                $this->saveAll($to_save);
                $trial_id = $this->getLastInsertID();

                $subject = 'Validate trial - '.$extension['Extension']['name'];
                $message = sprintf('<p>Hi %s!', $customer_name).'</p>';
                $message .= sprintf('<p>To validate trial <a href="%s">click in this link</a>.</p>', $this->api_url.'opencart/validate_trial/'.$trial_id);

                App::import('Component', 'Email');
                $emailComponent = new EmailComponent(new ComponentCollection());
                $emailComponent->send_email($customer_email, 'info@devmanextensions.com', 'Devmanextensions Trials system', $subject, $message);
            } else {
                if(!$result['TrialLicense']['activated'])
                    throw new Exception('The trial is pending to validate, check <b>INBOX/SPAM</b> folder of email "<b>'.$result['TrialLicense']['customer_email'].'</b>" If you put a incorrect email, put in contact with us and we will reset your trial.');
                else
                    throw new Exception('The trial period ended to domain registered.');
            }
        }

        public function validate($trial_id) {
            if(empty($trial_id)) {
                throw new Exception('empty param');
            }

            $trial = $this->findById($trial_id);

            if($trial['TrialLicense']['activated'])
                throw new Exception('Trial already validated on '.date('d/m/Y H:i:s', strtotime($trial['TrialLicense']['modified'])));

            $temp = array(
                'TrialLicense' => array(
                    'id' => $trial_id,
                    'activated' => 1
                )
            );

            $this->save($temp);

            App::import('Component','Mailchimp');
            $mailchimpComponent = new MailchimpComponent(new ComponentCollection);

            App::import('Component','Email');
            $emailComponent = new EmailComponent(new ComponentCollection);

            $mailchimp_list_id = 'ddb182e8b6';
            $mailchimp_data = array(
                'email' => $trial['TrialLicense']['customer_email'],
                'merge_fields' => array(
                    'FNAME' => $trial['TrialLicense']['customer_name'],
                    'ENAME' => $trial['Extension']['name'],
                    'EID' => $trial['Extension']['oc_extension_id']
                )
            );

            $mailchimpComponent->subscribe($mailchimp_list_id, $mailchimp_data);

            $message = '<b>Name</b>: '.$trial['TrialLicense']['customer_name'].'<br>
                <b>Email</b>: '.$trial['TrialLicense']['customer_email'].'<br>
                <b>Extension</b>: '.$trial['Extension']['name'];
            $emailComponent->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Devmanextensions Trials system', 'Trial validated - '.$trial['Extension']['name'], $message);

            $subject = 'Your trial License ID';
            $message = sprintf('<p>Hi %s!', $trial['TrialLicense']['customer_name']).'</p>';
            $message .= sprintf('<p>Here your trial license ID: <b>%s</b> enjoy of %s!', $trial['TrialLicense']['license_id'], $trial['TrialLicense']['extension_name']);
            $emailComponent->send_email($trial['TrialLicense']['customer_email'], 'info@devmanextensions.com', 'Devmanextensions Trials system', $subject, $message);
        }

        function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        function beforeSave($options = array()) {
            if (!empty($this->data['TrialLicense']['domain'])) {
                $this->data['TrialLicense']['domain'] = $this->get_domain($this->data['TrialLicense']['domain']);
            }
            return true;
        }
        
        function beforeFind($queryData)
        {
            $conditions = $queryData['conditions'];

            if(is_array($conditions) && array_key_exists('TrialLicense.domain', $conditions) && !empty($conditions['TrialLicense.domain'])) {
                $conditions['TrialLicense.domain'] = $this->get_domain($conditions['TrialLicense.domain']);
            }
            
            $queryData['conditions'] = $conditions;
            return $queryData;
        }
        
        function format_all_domains() {
            $trials = $this->find('all');
            foreach ($trials as $key => $trial) {
                $id = $trial['TrialLicense']['id'];
                $domain = $this->get_domain($trial['TrialLicense']['domain']);
                $query = "UPDATE intranet_trial_licenses SET domain = '".$domain."' WHERE id = '".$id."'";
                $this->query($query);
            }
            die('finish');
        }

        public function update_version($license_id, $version)
        {
          $this->recursive = -1;
          $versions = $this->find('first', array('conditions' => array('TrialLicense.license_id' => $license_id)));
          $final_version = '';
          $current_version = $versions['TrialLicense']['oc_version'];
          if (empty($current_version)) {
              $final_version = $version;
          } else {
              $current_version = explode('|', $current_version);

              if (!in_array($version, $current_version))
                  $current_version[] = $version;

              $final_version = implode('|', $current_version);
          }

          $this->query('UPDATE intranet_trial_licenses SET oc_version = "'.$final_version.'" WHERE license_id = "'.$license_id.'"');
        }

    }
?>