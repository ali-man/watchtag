<?php
class ControllerCommonWishlist extends Controller {
	public function index() {
		$this->load->language('common/wishlist');
		$this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('account/wishlist');
		// Totals
		$this->load->model('setting/extension');


		
		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$data['products'] = array();
	
        $results = $this->session->data['wishlist'];
		// $this->session->data['wishlist'] = [];
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

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				$category = $this->model_catalog_product->getCategories($product_info['product_id']);
			
				if($category){
					$category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
				}

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'tax'  		 => $tax,
					'category_name' => $category_item['name'],
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
				);
			} else {
				$this->model_account_wishlist->deleteWishlist($result['product_id']);
			}
		}


        if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
		}




		$data['cart'] = $this->url->link('account/wishlist');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);

		return $this->load->view('common/wishlist', $data);
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}