<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');
		
		// Menu
		$this->load->model('catalog/category');
		$this->load->model('localisation/language');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$data['languages'] = array();

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info['image']) {
				$image = $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}
			$data['brand']['thumb'] = $image;
		}
		else{
			if ($this->config->get('config_image') && is_file(DIR_IMAGE . $this->config->get('config_image'))) {
				$data['thumb'] = $this->model_tool_image->resize($this->config->get('config_image'), 500, 500);
			} else {
				$data['thumb'] = "catalog/view/theme/default/images/baner/bitmap.png";
			}
		}

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => substr($result['name'],0,2),
					'code' => $result['code']
				);
			}
		}
		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);
		if (isset($this->request->get['path'])) { 
			$data['activeCategoryId'] = $this->request->get['path'];
		}
		if($this->request->get['route'] == "information/service"){
			$data['activeCategoryId'] = -1;
		}
		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'id' 	   => $category['category_id'],	 
					'name'     => $category['name'],
					'children' => $children_data,
					'image'	   => 'image/'.$category['image'],
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('account/customer');


		$brands = $this->model_catalog_manufacturer->getManufacturers();
		$customers = $this->model_account_customer->getCustomers([]);
		$data['title'] = $this->document->getTitle();

		$data['count_brands'] = count($brands);
		$data['count_products'] = $this->model_catalog_product->getTotalProducts([]);
		$data['count_users'] = count($customers);
		$data['language'] = $this->load->controller('common/language');
		return $this->load->view('common/menu', $data);
	}
}
