<?php
class ControllerAccountWishList extends Controller {
	public function index() {


		$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			// Remove Wishlist
			$this->model_account_wishlist->deleteWishlist($this->request->get['remove']);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);



		$data['products'] = array();
		

		$results = $this->session->data['wishlist'];
		if($results){
			foreach ($results as $result) {
				$product_info = $this->model_catalog_product->getProduct($result);
	
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
					} else {
						$image = false;
					}
	
					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$stock = $product_info['quantity'];
					} else {
						$stock = $this->language->get('text_instock');
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
	
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}
	
					$data['products'][] = array(
						'product_id' => $product_info['product_id'],
						'thumb'      => $image,
						'name'       => $product_info['name'],
						'model'      => $product_info['model'],
						'stock'      => $stock,
						'price'      => $price,
						'tax'         => $tax,
						'special'    => $special,
						'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
					);
				} else {
					$this->model_account_wishlist->deleteWishlist($result['product_id']);
				}
			}
			$data['continue'] = $this->url->link('account/account', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
	
			$this->response->setOutput($this->load->view('account/wishlist', $data));
		}
		else{
			$data['text_error'] = $this->language->get('text_empty');
			
			$data['continue'] = $this->url->link('common/home');

			unset($this->session->data['success']);

			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));	
		}

	}

	public function add() {
		$this->load->language('account/wishlist');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if ($this->customer->isLogged()) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				if(!in_array($this->request->post['product_id'],$this->session->data['wishlist'])){
					$this->session->data['wishlist'][] = $this->request->post['product_id'];
					$json['status'] = "add";
				}
					
				else{
					$newWishlist = [];

					foreach($this->session->data['wishlist'] as $wishlist){
						if($wishlist != $this->request->post['product_id']){
							$newWishlist[] = $wishlist;
						}
					}
					$this->session->data['wishlist'] = [];
					$this->session->data['wishlist'] = $newWishlist;
					$json['status'] = "delete";
				}
				

				
				$json['total'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
			} 
			else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				if(!in_array($this->request->post['product_id'],$this->session->data['wishlist'])){
					$this->session->data['wishlist'][] = $this->request->post['product_id'];
					$json['status'] = "add";
				}
					
				else{
					$newWishlist = [];

					foreach($this->session->data['wishlist'] as $wishlist){
						if($wishlist != $this->request->post['product_id']){
							$newWishlist[] = $wishlist;
						}
					}
					$this->session->data['wishlist'] = [];
					$this->session->data['wishlist'] = $newWishlist;
					$json['status'] = "delete";
				}
				

				
				$json['total'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
			}
		}
		$json['success'] = $this->language->get('success');
		$json['wishlist_added'] = $this->language->get('wishlist_added');
		$json['wishlist_removed'] = $this->language->get('wishlist_removed');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$newWishlist = [];

		foreach($this->session->data['wishlist'] as $wishlist){
			if($wishlist != $this->request->post['product_id']){
				$newWishlist[] = $wishlist;
			}
		}
		$this->session->data['wishlist'] = [];
		$this->session->data['wishlist'] = $newWishlist;
		
		$json['total'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
		$json['success'] = $this->language->get('success');
		$json['wishlist_removed'] = $this->language->get('wishlist_removed');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
