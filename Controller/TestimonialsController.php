<?php 
	class TestimonialsController extends AppController {

		public function beforeFilter() {
	        $this->Auth->allow('insert');
	    }

	    public  $uses = array(
      		'Testimonial',
		); 

		public $components = array(
		    'Email'
		);

		public function insert()
		{
			$image = $this->request->data['Testimonial']['file'];
			$extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $new_filename = $this->convert_to_filename($this->request->data['Testimonial']['name'].'_'.$this->request->data['Testimonial']['position'].'.'.$extension);

			unset($this->request->data['Testimonial']['file']);
			$this->request->data['Testimonial']['image'] = $new_filename;

			$this->Testimonial->saveAll($this->request->data);

			$id = $this->Testimonial->getLastInsertId();


			//Devman Extensions - info@devmanextensions.com - 2017-10-17 13:32:24 - Save image
				$image_route = APP.WEBROOT_DIR.DS.'images'.DS.'testimonials'.DS.$new_filename;
				move_uploaded_file($image['tmp_name'], $image_route);
			//END
			
			//Devman Extensions - info@devmanextensions.com - 2017-10-17 13:44:02 - Send alert email to me
				$subject_email = 'New testimonial';

				$content_email = '';

				//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:36:57 - Ticket for support license
					$content_email .= "<b>Name:</b> ".$this->request->data['Testimonial']['name']."<br>";
					$content_email .= "<b>Position:</b> ".$this->request->data['Testimonial']['position']."<br>";
					$content_email .= "<b>URL:</b> ".$this->request->data['Testimonial']['url']."<br>";
					$content_email .= "<b>Email:</b> ".$this->request->data['Testimonial']['email']."<br>";
					$content_email .= "<b>Rate:</b> ".$this->request->data['Testimonial']['rate']."<br>";
					$content_email .= "<b>Image:</b> <img src='https://devmanextensions.com/images/testimonials/".$id.'-'.$image['name']."'><br>";
					$content_email .= "<b>Testimonial:</b> ".nl2br($this->request->data['Testimonial']['testimonial'])."<br>";
				//END

				$email = 'info@devmanextensions.com';

				$this->Email->send_email('info@devmanextensions.com', 'testimonials@devmanextensions.com', 'DevmanExtensions Testimonials System', $subject_email, $content_email);

			//END	

			$this->Session->setFlash(
				'<i class="fa fa fa-check-circle"></i> Thanks you so much by your testimony! I will alert you when publishs it. Have a nice day! :)'
			);

			$this->redirect($this->referer());

		}

		function convert_to_filename($string) {

            $string = strtolower($string);
         
            $string = str_replace ("Ã¸", "oe", $string);
            $string = str_replace ("Ã¥", "aa", $string);
            $string = str_replace ("Ã¦", "ae", $string);
         
            $string = str_replace (" ", "_", $string);
            $string = str_replace ("..", ".", $string);
            $string = str_replace ("/", "", $string);
         
            preg_replace ("/[^0-9^a-z^_^.]/", "", $string);
            return $string;
        }
	}
?>