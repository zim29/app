<?php
	class ClientComponent extends Component {

		public function initialize(Controller $controller) {
            $this->Sale = ClassRegistry::init('Sales.Sale');
            $this->Invoice = ClassRegistry::init('Invoices.Invoice');
            $this->Testimonial = ClassRegistry::init('Testimonial');
            $this->TestimonialByCountry = ClassRegistry::init('TestimonialByCountry');
            //$this->Trial = ClassRegistry::init('Trial');
        }

        public function get_number_total_of_clients($only_sales = false, $only_invoices = false, $only_trials = false)
        {
            $this->Sale = ClassRegistry::init('Sales.Sale');
            $this->Invoice = ClassRegistry::init('Invoices.Invoice');

        	$sum_sales = 700;
            $sum_invoices = 300;
            $count_trial = $count_trial2 = 0;
            if(!$only_invoices)
            {

                $count_query = $this->Sale->query("SELECT COUNT(*) as total_sales FROM intranet_sales WHERE order_status = 'Complete'");
                $count_sales = $count_query[0][0]['total_sales'];

                /*
            	$conditions = array(
            		'Sale.order_status' => 'Complete'
            	);

            	$count_sales = $this->Sale->find('count', array('conditions' => $conditions));*/
            }
            else
            {
                $count_sales = $sum_sales = 0;
            }

            if(!$only_sales)
            {
            	/*$conditions = array(
            		'Invoice.state' => 'Payed'
            	);
            	$count_invoices = $this->Invoice->find('count', array('conditions' => $conditions));*/

            	$count_query = $this->Sale->query("SELECT COUNT(*) as total_invoices FROM intranet_invoices WHERE state = 'Payed'");
                $count_invoices = $count_query[0][0]['total_invoices'];

            }
            else
            {
                $count_invoices = $sum_invoices = 0;
            }

            if(!$only_trials) {
                /*$conditions = array(
            		'Trial.activated' => 1
            	);
                $this->Trial = ClassRegistry::init('Trial');
            	$count_trial = $this->Trial->find('count', array('conditions' => $conditions));*/

                $count_query = $this->Sale->query("SELECT COUNT(*) as total FROM intranet_trials WHERE activated = 1");
                $count_trial = $count_query[0][0]['total'];


            	/*$conditions = array(
            		'TrialLicense.activated' => 1
            	);
                $this->TrialLicense = ClassRegistry::init('TrialLicense');
            	$count_trial2 = $this->TrialLicense->find('count', array('conditions' => $conditions));*/

            	$count_query = $this->Sale->query("SELECT COUNT(*) as total FROM intranet_trial_licenses WHERE activated = 1");
                $count_trial2 = $count_query[0][0]['total'];

            }

        	$total = $sum_sales + $sum_invoices + $count_sales + $count_invoices + $count_trial + $count_trial2;
        	return $total;        	
        }

        public function update_count_client_image()
        {
            $image_path = APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients_template.jpg';
            $image_path_ru = APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients_template_ru.jpg';
            $font_path = APP.WEBROOT_DIR.DS.'fonts'.DS.'WorkSans-Bold.ttf';

            $jpg_image = imagecreatefromjpeg($image_path);
            $color = imagecolorallocate($jpg_image, 255, 13, 77);

            $jpg_image_ru = imagecreatefromjpeg($image_path_ru);
            $color_ru = imagecolorallocate($jpg_image_ru, 255, 13, 77);

            $number_clients = $this->get_number_total_of_clients();
            $number_clients = sprintf('%05d', $number_clients);

            $positions = array(
                245,
                300,
                355,
                410,
                465
            );

            $top = $this->get_points_from_pixel(153);
            for ($i = 0; $i <count($positions); $i++)
            {
                $number = $number_clients[$i];

                $right = $this->get_points_from_pixel($positions[$i]);

                switch ($number)
                {
                    case 0:
                        $right += 13;
                        break;
                    case 1:
                        $right += 19;
                        break;
                    case 2:
                        $right += 18;
                        break;
                    case 3:
                        $right += 18;
                        break;
                    case 4:
                        $right += 16;
                        break;
                    case 5:
                        $right += 18;
                        break;
                    case 6:
                        $right += 15;
                        break;
                    case 7:
                        $right += 18;
                        break;
                    case 8:
                        $right += 15;
                        break;
                    case 9:
                        $right += 16;
                        break;
                }

                if($i > 0) {
                    $image_path = APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients.jpg';
                    $jpg_image = imagecreatefromjpeg($image_path);
                    $color = imagecolorallocate($jpg_image, 255, 13, 77);

                    $image_path_ru = APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients_ru.jpg';
                    $jpg_image_ru = imagecreatefromjpeg($image_path_ru);
                    $color_ru = imagecolorallocate($jpg_image_ru, 255, 13, 77);
                }

                imagettftext($jpg_image, $this->get_points_from_pixel(50), 0, $right, $top, $color, $font_path, $number);
                imagejpeg($jpg_image, APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients.jpg', 100);

                imagettftext($jpg_image_ru, $this->get_points_from_pixel(50), 0, $right, $top, $color, $font_path, $number);
                imagejpeg($jpg_image_ru, APP.WEBROOT_DIR.DS.'images'.DS.'count_devmanextensions_clients_ru.jpg', 100);
            }


            $image_path = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'thanks_for_your_purchase_template.jpg';
            $image_path_ru = APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'thanks_for_your_purchase_template_ru.jpg';
            //$font_path = APP.WEBROOT_DIR.DS.'fonts'.DS.'Kanit-Black.ttf';
            $right = 205;
            $top = 417;

            $jpg_image = imagecreatefromjpeg($image_path);
            $jpg_image_ru = imagecreatefromjpeg($image_path_ru);
            $color = imagecolorallocate($jpg_image, 255, 13, 77);
            $text = $this->get_number_total_of_clients();

            imagettftext($jpg_image, 50, 0, $right, $top, $color, $font_path, $text);
            imagejpeg($jpg_image, APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'thanks_for_your_purchase.jpg', 100);

            imagettftext($jpg_image_ru, 50, 0, $right, $top, $color, $font_path, $text);
            imagejpeg($jpg_image_ru, APP.WEBROOT_DIR.DS.'images'.DS.'extensions'.DS.'thanks_for_your_purchase_ru.jpg', 100);
        }

        public function get_testimonials($home = false)
        {
            $conditions = array('Testimonial.published' => 1);

            if(!empty($home))
                $conditions['Testimonial.home'] = 1;

            $testimonials = $this->Testimonial->find('all', array('conditions' => $conditions, 'order' => array('Testimonial.rate DESC', 'Testimonial.order ASC')));
            //Devman Extensions - info@devmanextensions.com - 2017-10-20 12:18:20 - Format urls
                foreach ($testimonials as $key => $test) {
                    if (strpos($test['Testimonial']['url'], '://') === false) 
                        $testimonials[$key]['Testimonial']['url'] = 'http://' . $test['Testimonial']['url'];

                    $testimonials[$key]['Testimonial']['extension_name'] = $test['Extension']['name'];
                }
            //END
            return $testimonials;
        }

        public function get_testimonials_by_country()
        {
            $testimonials = $this->TestimonialByCountry->find('all');
            return $testimonials;
        }

        public function get_points_from_pixel($pixels)
        {
            return ($pixels*6)/8;
        }


    }
?>