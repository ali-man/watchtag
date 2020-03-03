<?php
class ControllerProductManufacturer extends Controller {
	public function index() {
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_brand'),
			'href' => $this->url->link('product/manufacturer')
		);

		$data['categories'] = array();

		$results = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($results as $result) {
			if (is_numeric(utf8_substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
			}

			if (!isset($data['categories'][$key])) {
				$data['categories'][$key]['name'] = $key;
			}

			$data['categories'][$key]['manufacturer'][] = array(
				'name' => $result['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('product/manufacturer_list', $data));
	}

	public function infoBrands() {
		$this->response->setOutput($this->brands());
	}

	public function brands(){
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		if (!isset($this->request->get['limit'])) {
			$limit = 30;
			$data['limit'] = 30;
		}
		else{
			$limit = $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		}

		$parts = explode('_', (string)$this->request->get['path']);

		$category_id = (int)array_pop($parts);
		$filter_man_data = array(
			'filter_category_id' => $category_id,
		);
		$resultProducts = $this->model_catalog_product->getProducts($filter_man_data);
		$resultManufaturer = [];

		foreach($resultProducts as $resultProduct)
		{
			if(!in_array($resultProduct['manufacturer_id'], $resultManufaturer)){
				$resultManufaturer[] = $resultProduct['manufacturer_id'];
			}	
		}


		$filter_data = array(
			'limit'              => $limit,
			'idArray' 			 => $resultManufaturer
		);

		$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);
		$total_results =  $this->model_catalog_manufacturer->getTotalManufacturers($filter_data);

		if($limit >= $total_results)
		{
			$data['yes_again'] = false;
		}
		else
		{	
			$data['yes_again'] = true;
		}
		foreach ($results as $result) {

			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}
			// $filter = $this->model_catalog_manufacturer->getFilter($result['name']);
			$data['brandsCategory'][] = array(
				'name' => $result['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
				'thumb' => $image,
			);
		}
		

		return $this->load->view('product/brands', $data);

	}

	public function brandsHome() {
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');
		$limit = 8;
		$filter_data = array(
			'limit'              => $limit
		);
		$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}
			// $filter = $this->model_catalog_manufacturer->getFilter($result['name']);
			

			$data['brands'][] = array(
				'name' => $result['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
				'thumb' => $image,
			);
		}

		return $this->load->view('product/brandsHome', $data);		
	}

	public function brandsBrand() {
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');
		$limit = 8;
		$filter_data = array();
		$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);
		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

		$data['brand']['name'] = $manufacturer_info['name'];
		$data['brand']['href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id']);
		if ($manufacturer_info['image']) {
			$image = $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
		} else {
			$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
		}
		$data['brand']['thumb'] = $image;
		foreach ($results as $result) {
			if( $result['manufacturer_id'] != $this->request->get['manufacturer_id'])
			{
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				// $filter = $this->model_catalog_manufacturer->getFilter($result['name']);
				
	
				$data['brands'][] = array(
					'name' => $result['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
					'thumb' => $image,
				);
			}

		}

		return $this->load->view('product/brandsBrand', $data);		
	}

	public function info() {
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}


		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
		$this->document->setTitle($manufacturer_info['name']);
		$data['heading_title'] = $manufacturer_info['name'];
		if ($manufacturer_info) {


			$data['manufacturerProducts'] = $this->load->controller('product/manufacturer/manufacturerProducts');
			$data['continue'] = $this->url->link('common/home');
			$data['brands'] = $this->load->controller('product/manufacturer/brandsBrand');
			$data['filter'] = $this->load->controller('extension/module/filter_allB');
			$data['menu'] = $this->load->controller('common/menu');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/manufacturer_info', $data));
		} else {
			$url = '';

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/manufacturer/info', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function manufacturerProducts(){
		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}






		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		} else {
			$limit = 30;
			$data['limit'] = 30;
		}




			$url = '';


			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			$data['compare'] = $this->url->link('product/compare');

			$data['products'] = array();
			if (isset($this->request->get['filter'])) {
				$filter = $this->request->get['filter'];
			} else {
				$filter = '';
			}

			$filter_data = array(
				'filter_manufacturer_id' => $manufacturer_id,
				'limit'                  => $limit,
				'filter_filter' => $filter
			);

			$filter_data1 = array(
				'filter_manufacturer_id' => $manufacturer_id,
				'filter_filter' => $filter
			);

			$product_total = $this->model_catalog_product->getBrandProducts($filter_data1);

			$results = $this->model_catalog_product->getBrandProducts($filter_data);

			if($limit >= count($product_total))
			{
				$data['yes_again'] = false;
			}
			else
			{	
				$data['yes_again'] = true;
			}

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
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

				if($special){
					$percent_special = number_format(100 - ($result['special']/$result['price'])*100,1,'.',' ');
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'percent_special' => $percent_special,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}



			$url = '';


			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=' . $value)
				);
			}

			$url = '';


			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}



			
		return $this->load->view('product/manufacturerProducts',$data);
	}


	public function infoManafacturer(){
		$this->response->setOutput($this->manufacturerProducts());
	}
}
