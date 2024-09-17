<?php
	class PrestashopController extends AppController
    {
        public $uses = array(
            'Faq',
            'Sales.Sale',
        );

        public $components = array(
		    'OpencartTicket',
		);

        public function beforeFilter(){
            $this->Auth->allow('get_faq', 'ajax_open_ticket');
        }

        function get_faq() {
            $array_return = array('html_faq' => 'FAQ not found');
            $extension_id = array_key_exists('extension_id', $this->request->data) ? $this->request->data['extension_id'] : '';

            if(!empty($extension_id)) {
                $conditions = array('Faq.extension_id' => $extension_id, 'system' => 'prestashop');
                $faqs = $this->Faq->find('all', array('conditions' => $conditions, 'order' => array('Faq.order ASC', 'Faq.created ASC')));
                if(!empty($faqs)) {
                    $array_return['html_faq'] = $this->get_tab_faq($faqs);
                }

            }
            echo json_encode($array_return); die;
        }

        public function get_tab_faq($faqs) {
            $html_faqs = '';
            foreach ($faqs as $key => $faq) {
                $html_faqs .= '<h2 class="faq_title" onclick="toggle_faq($(this))">'.($key+1).'.- '.$faq['Faq']['title'].'</h2>';
                $html_faqs .= '<div class="faq_description">'.$faq['Faq']['description'].'</div>';
            }

            $html_faqs .= '
                <style type="text/css">
                    h2.faq_title
                    {
                        position: relative;
                        float: left;
                        width: 100%;
                        border-bottom: 1px solid #ddd;
                        padding-bottom: 15px;
                        font-size: 17px;
                        font-weight: bold;
                        padding-top: 15px;
                        margin-bottom: 0px;
                    }
                    h2.faq_title:hover,
                    h2.faq_title.openned
                    {
                        cursor: pointer;
                        background: #eee;
                    }
                    h2.faq_title:before
                    {
                        font-family: "FontAwesome";
                        content: "\f150";
                        margin-right: 10px;
                        font-size: 11px;
                        top: -3px;
                        position: relative;
                        font-weight: normal;
                        margin-left: 10px;
                    }
                    h2.faq_title.openned:before
                    {
                        content: "\f151";
                    }
                    div.faq_description
                    {
                        display: none;
                        margin-bottom: 0px;
                        position: relative;
                        float: left;
                        width: 100%;
                        margin-top: 20px;
                        border-bottom: 1px solid #eee;
                        padding-bottom: 15px;
                        padding-left: 20px;
                        padding-right: 30px;
                        font-size: 14px;

                    }
                    h2.faq_title.openned + div.faq_description
                    {
                        display: block;
                    }
                </style>
                <script type="text/javascript">
                    function toggle_faq(heading_pressed)
                    {
                        heading_pressed.toggleClass(\'openned\');
                    }
                </script>	
            ';
            return $html_faqs.'<div style="clear:both;"></div>';
        }

        public function ajax_open_ticket()
		{
		    $ticket_data = array(
		        'system' => 'prestashop',
		        'domain' => $this->request->data['support_domain'],
		        'license_id' => $this->request->data['support_license_id'],
		        'name' => $this->request->data['support_name'],
                'email' => $this->request->data['support_email'],
                'subject' => $this->request->data['support_subject'],
                'text' => $this->request->data['support_problem'],
                'conections' => $this->request->data['support_connections'],
                'type' => 'Support'
            );
			$array_return = array('error' => false, 'message' => '<b>Ticket sent successfully</b>, check your email inbox or SPAM folder, you will get an auto response with ticket details.');

			$this->request->data['type'] = 'Support';

			try {
			    $this->OpencartTicket->open_ticket($ticket_data);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			echo json_encode($array_return); die;
		}
    }
    ?>
