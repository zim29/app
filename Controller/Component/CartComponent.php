<?php
	class CartComponent extends Component {
		public $components = array('Session', 'ExtensionTool');

		public function initialize(Controller $controller) {
			//$this->Session->write('discounts', array());
            $this->Extension = ClassRegistry::init('Extensions.Extension');
            $this->Coupon = ClassRegistry::init('Sales.Coupon');
            $this->cart = $this->Session->read('cart');
            $this->discounts = $this->Session->read('discounts');

            $coupon_general = $this->get_general_coupon();
            if(!empty($coupon_general) && array_key_exists('code', $coupon_general) && !empty($coupon_general['code']))
            	$this->add_discount_general($coupon_general['code']);
        }

        public function count_products()
		{
			$total = 0;

			if(!empty($this->cart))
			{
				foreach ($this->cart as $extension_id => $units) {
					$total += $units;
				}
			}

			return $total;
		}

		public function add_product($extension_id, $quantity = 1)
		{
			if(empty($extension_id))
				throw new Exception (__('Empty extension_id var'));

			$cart_copy = $this->cart;


			$cart_copy[$extension_id] = $quantity;

			$cart_copy[$extension_id] = $cart_copy[$extension_id] > 10 ? 10 : $cart_copy[$extension_id];
			$this->Session->write('cart', $cart_copy);

			$cart = $this->Session->read('cart');

			$this->cart = $this->Session->read('cart');
		}

		public function add_discount($extension_id, $code) {
			$extension = $this->ExtensionTool->get_extension($extension_id);
			
			if($code != $extension['Extension']['discount_code'])
				throw new Exception ("Your discount code is not correct");

			$current_discounts = $this->get_discounts();
			$current_discounts = $current_discounts == '' ? array() : $current_discounts;
			if(!array_key_exists($extension_id, $current_discounts))
				$current_discounts[$extension_id] = array();
			
			$current_discounts[$extension_id] = array(
				'discount' => $extension['Extension']['discount'],
				'code' => $code,
			);
			
			$this->update_discounts($current_discounts);
		}

		public function add_discount_general($code) {
			$code = trim($code);
			$discount = $this->Coupon->getDiscountCoupon($code);

			$products_in_cart = $this->get_products();
			$discounts = array();

			$cart = $this->Session->read('cart');
			foreach ($cart as $extension_id => $quantity) {
				$discounts[$extension_id] = array(
					'discount' => $discount,
					'code' => $code,
				);
			}

			$this->Session->write('coupon_general', array('code' => $code, 'discount' => $discount));
			$this->update_discounts($discounts);
		}

		public function get_general_coupon() {
			$coupon_general = $this->Session->read('coupon_general');

			if(!empty($coupon_general))
				$discount = $this->Coupon->getDiscountCoupon($coupon_general['code'], false);

			if(isset($discount) && !empty($discount))
				return $coupon_general;
			else {
				$this->remove_general_coupon();
				return false;
			}
		}

		public function update_cart($new_cart)
		{
			$this->Session->write('cart', $new_cart);
			$this->cart = $this->Session->read('cart');
		}

		public function update_discounts($new_discounts)
		{
			$this->Session->write('discounts', $new_discounts);
			$this->discounts = $this->Session->read('discounts');
		}

		public function remmove_product($extension_id)
		{
			if(empty($extension_id))
				throw new Exception (__('Empty extension_id var'));

			unset($this->cart[$extension_id]);
			$this->Session->write('cart', $this->cart);
		}

		public function clear()
		{
			$this->Session->write('cart', array());
			$this->cart = $this->Session->read('cart');
		}

		public function get_total()
		{
			$cart = $this->Session->read('cart');
			$total = 0;

			if(!empty($cart))
			{
				foreach ($cart as $extension_id => $quantity) {
					$prices = $this->ExtensionTool->get_extension_to_cart($extension_id, $quantity);
					$total += $prices['total'];
				}

			}
			return $total;
		}

		public function get_products()
		{
			$cart = $this->Session->read('cart');

			$products = array();
			if(!empty($cart))
			{
				foreach ($cart as $extension_id => $quantity) {
					$products[] = $this->ExtensionTool->get_extension_to_cart($extension_id, $quantity);
				}
			}
			
			return $products;
		}

		public function get_discounts()
		{
			$discounts = $this->Session->read('discounts');
			return empty($discounts) ? array() : $discounts;
		}

		public function remove_general_coupon() {
			$this->Session->delete('coupon_general');
			$this->Session->delete('discounts');
			//$this->Session->delete('coupon_general');
		}
    }