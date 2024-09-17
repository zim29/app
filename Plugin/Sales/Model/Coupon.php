<?php
    class Coupon extends AppModel
  	{
        var $name = 'Coupon';
        var $useTable = 'coupons';

        var $belongsTo = array(
            'Sale' => array(
                'className' => 'Sales.Sale',
                'foreignKey' => false,
                'conditions' => array('Sale.order_id = Coupon.sale_id'),
            ),
            'Extension' => array(
                'foreignKey' => false,
                'conditions' => array(
                    'OR' => array(
                        array('Extension.oc_extension_id = Coupon.extension_id'),
                        array('Extension.id = Coupon.extension_id')
                    )
                )
            )
        );

        function create_coupon($sale_id, $extension_id) {
            //Get discount from extension
            $extensionModel = ClassRegistry::init('Extensions.Extension');

            $extension = $extensionModel->findById($extension_id);
            if(empty($extension))
                $extension = $extensionModel->findByOcExtensionId($extension_id);

            if(empty($extension))
                return false;

            $saleModel = ClassRegistry::init('Sales.Sale');
            $sale = $saleModel->findByOrderId($sale_id);

            $discount = !empty($extension['Extension']['discount']) ? $extension['Extension']['discount'] : 20;
            $discount_days = 30;
            $to_date = date('Y-m-d H:i:s', strtotime("+".$discount_days." day", strtotime(date('Y-m-d H:i:s'))));
            $code = $this->_generate_random_string();
            $to_save = array(
                'Coupon' => array(
                    'sale_id' => $sale_id,
                    'extension_id' => $extension['Extension']['id'],
                    'discount' => $discount,
                    'customer_name' => $sale['Sale']['buyer_username'],
                    'customer_email' => $sale['Sale']['buyer_email'],
                    'coupon' => $code,
                    'uses_allowed' => 1,
                    'from' => date('Y-m-d H:i:s'),
                    'to' => $to_date
                )
            );

            //$to = 'info@devmanextensions.com';
            $to = $sale['Sale']['buyer_email'];
            $subject = 'You got '.$discount.'% discount to your next purchase!! =)';
			$content = 'Thank you for your purchase!<br><br>To show our appreciation for your confidence in our products, we offer you this discount coupon valid until <b><u>'.(date('d/m/Y H:i:s', strtotime($to_date))).'</u></b> with a discount of <b><u>'.$discount.'%</u></b>!
                <br>To apply this discount, follow the steps below:

                <ol>
                    <li><a href="https://devmanextensions.com/extensions-shop">Go to the shop.</a></li>
                    <li>Add products to the cart.</li>
                    <li><a href="https://devmanextensions.com/cart">Go to the "My cart" page.</a></li>
                    <li>Locate the "Coupon" text area and enter your coupon code: <b>'.$code.'</b></li>
                    <li>Press the green button "<b>Apply coupon code</b>".</li>
                    <li>Press the orange button "<b>Checkout process</b>" and complete your purchase.</li>
                </ol>
            ';

			App::import('Component', 'Email');
            $emailComponent = new EmailComponent(new ComponentCollection());

			$data_email = array(
				'template' => 'coupon',
				'to' => $to,
				'subject' => $subject,
				'content' => $content,
			);
            $emailComponent->send_email_new($data_email);

            $this->saveAll($to_save);
        }

        function getDiscountCoupon($code, $exceptions = true) {

			$extension = ClassRegistry::init('Extension.Extension');
			if(!empty($extension->is_black_friday()))
				throw new Exception ("Coupons not compatible with BlackFriday promotion.");

            $conditions = array(
                'Coupon.coupon' => $code,
                'Coupon.uses_allowed >=' => 1,
                'Coupon.to >= ' => date('Y-m-d H:i:s'),
                'Coupon.from <= ' => date('Y-m-d H:i:s'),
            );

            $coupon = $this->find('first', array('conditions' => $conditions));

            if(empty($coupon)) {
                if($exceptions)
                    throw new Exception ("Your coupon code is not correct, is expired, or you used it in past.");
                else
                    return '';
            }
            else
                return $coupon['Coupon']['discount'];
        }

        function _generate_random_string($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
  	}
?>
