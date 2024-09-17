<?php
	class EmailComponent extends Component {

		public function initialize(Controller $controller) {
            //$this->Sale = ClassRegistry::init('Sales.Sale');
        }

        public function send_email($to, $from_email, $from_name, $subject, $content, $attached = '', $cc = array(), $images = array(), $is_ticket = false)
        {
        	App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();

			if(!$is_ticket)
			    $Email->config('gmail');

			$Email->from(array($from_email => $from_name));

			$Email->to(trim($to));
			/*if(!empty($cc))
				$Email->cc($cc);*/

			$Email->emailFormat('html');
			$Email->template('simple_email');
			$Email->subject($subject);

			if (!empty($attached) && !empty($attached[0]['name']))
			{
				$files = array();
				foreach ($attached as $key => $file) {
					$files += array($file['name'] => $file['tmp_name']);
				}
				$Email->attachments($files);
			}

			if(!empty($attached) && is_string($attached))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
    			$mime = finfo_file($finfo, $attached);

				$files = array(
					array(
	                    'file' => $attached,
	                    'mimetype' => $mime
	                ),
				);

				$Email->attachments($files);
			}

			if(!empty($images)) {
			    $images_final = array();

			    foreach ($images as $key => $img) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $img['path']);
                    $images_final[$img['name']] = array(
                        'file' => $img['path'],
                        'mimetype' => $mime,
                        'contentId' => 'image_'.$key
                    );
			    }
			    $Email->attachments($images_final);
            }

			if(!$Email->send($content))
				throw new Exception ('Error sending emails, try again later.');
        }

		public function send_email_new($data)
		{
			$to = $data['to'];
			$from_email = !empty($data['from_email']) ? $data['from_email'] : 'info@devmanextensions.com';
			$from_name = !empty($data['from_name']) ? $data['from_name'] : 'DevmanExtensions';
			$subject = $data['subject'];
			$content = $data['content'];
			$attached = !empty($data['attached']) ? $data['attached'] : '';
			$template = !empty($data['template']) ? $data['template'] : 'simple_email';
			$cc = !empty($data['cc']) ? $data['cc'] : array();
			$images = !empty($data['images']) ? $data['images'] : array();


			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail');
			$Email->from(array($from_email => $from_name));
			$Email->to(trim($to));
			$Email->emailFormat('html');
			$Email->template($template);
			$Email->subject($subject);

			if (!empty($attached) && !empty($attached[0]['name']))
			{
				$files = array();
				foreach ($attached as $key => $file) {
					$files += array($file['name'] => $file['tmp_name']);
				}
				$Email->attachments($files);
			}

			if(!empty($attached) && is_string($attached))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($finfo, $attached);

				$files = array(
					array(
						'file' => $attached,
						'mimetype' => $mime
					),
				);

				$Email->attachments($files);
			}

			if(!empty($images)) {
				$images_final = array();

				foreach ($images as $key => $img) {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $img['path']);
					$images_final[$img['name']] = array(
						'file' => $img['path'],
						'mimetype' => $mime,
						'contentId' => 'image_'.$key
					);
				}
				$Email->attachments($images_final);
			}

			if(!$Email->send($content))
				throw new Exception ('Error sending emails, try again later.');
		}

        public function send_email_support($to, $from_email, $from_name, $subject, $content, $attached = '', $cc = array(), $images = array())
        {
            $this->send_email($to, $from_email, $from_name, $subject, $content, $attached, $cc, $images, true);
        }
    }
?>
