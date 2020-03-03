<?php
class ControllerExtensionModuleRandom extends Controller {
	private $productsId = array();
	public function index() {	
		$this->load->language('extension/module/random');

		$this->load->model('catalog/product');
		$this->load->model('extension/module/random');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$data['products'] = array();	
		if(isset($this->request->get['limit']))
			$limit = $this->request->get['limit'];
		else
			$limit = 30;

		$data['limit'] = $limit;
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if($limit == 30)
			$this->cache->set('productsId',array());

		$filter_data=array('filter_filter' => $filter);


		if ($this->config->get('module_random_status')) {
			if(!$this->cache->get('productsId'))
			{
				$products = $this->model_extension_module_random->getFilterProducts($filter_data);
	
				foreach ($products as $product) {
					array_push($this->productsId,$product['product_id']);
				}
				shuffle($this->productsId);
				$this->cache->set('productsId', $this->productsId);
			}

			$this->productsId = array_slice($this->cache->get('productsId'),0,$limit);

			if($limit >= count($this->cache->get('productsId')))
			{
				$data['yes_again'] = false;
			}
			else
			{	
				$data['yes_again'] = true;
			}


			
			foreach ($this->productsId as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				$filter_groups = $this->model_catalog_product->getProductFilters($product_id);
				 

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'],  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png',  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
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

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$brand_image = $this->model_tool_image->resize($this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id'])['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					// $filterBrand = $this->model_catalog_manufacturer->getFilter($product_info['manufacturer']);
					
					$product_cat = $this->model_catalog_product->getCategories($product_info['product_id']);
		
					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'filter_groups' => $filter_groups,
						'rating'      => $rating,
						'brand_name'  => $product_info['manufacturer'],
						'brand_image' => $brand_image,
						'brand_href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']),	
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/random', $data);
		}
	}

	public function info()
	{
		return $this->response->setOutput($this->index());
	}
}