<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->load->language('common/home');
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');




		$result_products = $this->model_catalog_product->getWithOrderProducts(6);
		if ($result_products) {
			foreach ($result_products as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 295, 308);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',500,500);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				
				$category = $this->model_catalog_product->getCategories($result['product_id']);
			
				if($category){
					$category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
				}
				if($special){
					$percent_special = number_format(100 - ($special/$price)*100,1,'.',' ');
				}

				
				$data['with_order_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'		  => $tax,
					'with_order_url' => $result['with_order_url'],
					'rating'      => $rating,
					'percent_special' => $percent_special,
					'category_name' => $category_item['name'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['menu'] = $this->load->controller('common/menu');
		$data['brands'] = $this->load->controller('product/manufacturer/brandsHome');
		$data['hot_product'] = $this->load->controller('common/home/hotProduct');
		$data['last_product'] = $this->load->controller('common/home/lastProduct');
		$data['with_product'] = $this->load->controller('common/home/withProduct');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function hotProduct(){
		$this->load->language('common/home');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');

		$this->load->model('tool/image');
		if (!isset($this->request->get['limit'])) {
			$limit = 30;
			$data['limit'] = 30;
		}
		else{
			$limit = $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		}

		$filter_data = array(
			'limit'              => $limit
		);
		$result_products = $this->model_catalog_product->getHotProducts($limit);
		$total_products  = $this->model_catalog_product->getTotalHotProducts();
		if($limit >= $total_products)
		{
			$data['yes_again'] = false;
		}
		else
		{	
			$data['yes_again'] = true;
		}
		if ($result_products) {
			foreach ($result_products as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 500, 500);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',500,500);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'],  $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				
				$category = $this->model_catalog_product->getCategories($result['product_id']);
			
				if($category){
					$category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
				}
				if($special){
					$percent_special = number_format(100 - ($result['special']/$result['price'])*100,1,'.',' ');
				}

				$data['hot_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'stock_status'=> $result['stock_status'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'		  => $tax,
					'rating'      => $rating,
					'percent_special' => $percent_special,
					'category_name' => $category_item['name'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}

		return $this->load->view('common/hot_product', $data);

	}

	public function infoHotProduct() {
		$this->response->setOutput($this->hotProduct());
	}

	public function lastProduct(){
		$this->load->language('common/home');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');

		$this->load->model('tool/image');
		if (!isset($this->request->get['limit'])) {
			$limit = 30;
			$data['limit'] = 30;
		}
		else{
			$limit = $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		}

		$filter_data = array(
			'limit'              => $limit
		);
		$result_products = $this->model_catalog_product->getLatestProducts($limit);
		$total_products  = $this->model_catalog_product->getTotalLatestProducts();

		if($limit >= $total_products)
		{
			$data['yes_again'] = false;
		}
		else
		{	
			$data['yes_again'] = true;
		}
		if ($result_products) {
			foreach ($result_products as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 500, 500);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',500,500);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				
				$category = $this->model_catalog_product->getCategories($result['product_id']);
			
				if($category){
					$category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
				}
				if($special){
					$percent_special = number_format(100 - ($result['special']/$result['price'])*100,1,'.',' ');
				}
				
				
				$data['last_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'stock_status'=> $result['stock_status'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'percent_special' => $percent_special,
					'category_name' => $category_item['name'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}

		return $this->load->view('common/last_product', $data);

	}

	public function infoLastProduct() {
		$this->response->setOutput($this->lastProduct());
	}

	public function withProduct(){
		$this->load->language('common/home');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');

		$this->load->model('tool/image');
		if (!isset($this->request->get['limit'])) {
			$limit = 30;
			$data['limit'] = 30;
		}
		else{
			$limit = $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		}

		$filter_data = array(
			'limit'              => $limit
		);
		$result_products = $this->model_catalog_product->getWithOrderProducts($limit);
		$total_products  = $this->model_catalog_product->getTotalWithOrderProducts();

		if($limit >= $total_products)
		{
			$data['yes_again'] = false;
		}
		else
		{	
			$data['yes_again'] = true;
		}
		if ($result_products) {
			foreach ($result_products as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 295, 308);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',500,500);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				
				$category = $this->model_catalog_product->getCategories($result['product_id']);
			
				if($category){
					$category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
				}
				if($special){
					$percent_special = number_format(100 - ($special/$price)*100,1,'.',' ');
				}

				
				$data['with_order_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'with_order_url' => $result['with_order_url'],
					'rating'      => $rating,
					'percent_special' => $percent_special,
					'category_name' => $category_item['name'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}

		return $this->load->view('common/with_product', $data);

	}

	public function infoWithProduct() {
		$this->response->setOutput($this->withProduct());
	}

}
