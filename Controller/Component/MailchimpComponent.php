<?php
	class MailchimpComponent extends Component
    {
        public function initialize(Controller $controller)
        {
            //$this->Extension = ClassRegistry::init('Extensions.Extension');
            $this->mailchimp_api = Configure::read('mailchimp_api');
            $this->mailchimp_server = Configure::read('mailchimp_server');
        }

        public function subscribe($list_id, $data)
        {
            /*
            'FNAME' => $firstname,
            'LNAME' => $lastname,
            'CART_ID' => $id_cart_abandoned
            */

            $this->unsubscribe($list_id, $data['email']);

            $curl_url = 'https://' . $this->mailchimp_server . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';
            $auth = base64_encode('user:' . $this->mailchimp_api);
            $data = array(
                'apikey' => $this->mailchimp_api,
                'email_address' => $data['email'],
                'status' => 'subscribed',
                'merge_fields' => $data['merge_fields']
            );
            $json_data = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curl_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Authorization: Basic ' . $auth));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            $result = curl_exec($ch);

            $result_decode = json_decode($result);
        }

        public function unsubscribe($list_id, $email)
        {
            $userid = md5(strtolower($email));
            $curl_url = 'https://' . $this->mailchimp_server . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $userid;

            $auth = base64_encode('user:' . $this->mailchimp_api);
            $data = array(
                'apikey' => $this->mailchimp_api,
                'email_address' => $email,
            );
            $json_data = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curl_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Authorization: Basic ' . $auth));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            $result = curl_exec($ch);

            $result_decode = json_decode($result);
        }
    }