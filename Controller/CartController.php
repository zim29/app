<?php
	class CartController extends AppController {

		public  $uses = array(
      		'Extensions.Extension'
		);

		public $components = array(
			'ExtensionTool',
		    'OpencartExtension',
		    'Cart',
			'RequestHandler'
		);

		public function beforeFilter() {
	        $this->Auth->allow('ajax_add_to_cart','ajax_remove_from_cart','ajax_update_cart','ajax_apply_discount', 'ajax_apply_discount_general');
	    }

		public function ajax_add_to_cart()
		{
			$this->autoRender = false;

			$array_return = array('error' => false, 'message' => '', 'extension_data' => false);
			try {
				$extension_id = array_key_exists('extension_id', $this->request->data) && !empty($this->request->data['extension_id']) ? $this->request->data['extension_id'] : '';
				$quantity = array_key_exists('quantity', $this->request->data) && !empty($this->request->data['quantity']) ? $this->request->data['quantity'] : 1;
			    $this->Cart->add_product($extension_id, $quantity);

				$extension = $this->Extension->findById($extension_id);
				$array_return['extension_data'] = $this->Extension->formatExtensionToDatalayer($extension, $quantity);

				$array_return['current_cart'] = array();

				$this->Cart->add_product($extension_id, $quantity);

				$cart_products = $this->Cart->get_products();
				foreach ($cart_products as $ext) {
					$array_return['current_cart'][] = $this->Extension->formatExtensionToDatalayer($ext['id'], $ext['quantity']);
				}

				$array_return['total'] = $this->Cart->get_total();
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
				$array_return['product_id'] = $extension_id;
				$array_return['context'] = json_encode($this->request);

			}

			$link_to_cart = Router::url("/", false).'cart';

			// $array_return['message'] = sprintf(__('Product added to cart! Go to <a href="%s">cart view</a> to know your order details.'), $link_to_cart);
			$array_return['units'] = $this->Cart->count_products();

			return  json_encode($array_return); 
		}

		public function ajax_remove_from_cart()
		{
			$array_return = array('error' => false, 'message' => '');
			try {
				$extension_id = array_key_exists('extension_id', $this->request->data) && !empty($this->request->data['extension_id']) ? $this->request->data['extension_id'] : '';
			    $this->Cart->remmove_product($extension_id);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			$array_return['units'] = $this->Cart->count_products();

			echo json_encode($array_return); die;
		}

		public function ajax_update_cart()
		{
			$array_return = array('error' => false, 'message' => '');
			$products = array_key_exists('products', $this->request->data) ? $this->request->data['products'] : array();

			if(!empty($products))
			{
				foreach ($products as $extension_id => $units) {
					if(!is_numeric($units) || $units <= 0)
						unset($products[$extension_id]);
				}
				if(!empty($products))
					$this->Cart->update_cart($products);
				else
					$this->Cart->clear();
			}

			echo json_encode($array_return); die;
		}

		public function ajax_apply_discount() {
			$code = $this->request->data['code'];
			$extension_id = $this->request->data['extension_id'];

			$array_return = array('error' => false, 'message' => '');

			try {
				$this->Cart->add_discount($extension_id, $code);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			echo json_encode($array_return); die;
		}

		public function ajax_apply_discount_general() {
			$code = $this->request->data['code'];

			$array_return = array('error' => false, 'message' => '');

			try {
				$this->Cart->add_discount_general($code);
			} catch (Exception $e) {
				$array_return['error'] = true;
				$array_return['message'] = $e->getMessage();
			}

			echo json_encode($array_return); die;
		}
	}
?>
