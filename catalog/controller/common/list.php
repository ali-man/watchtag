<?php
class ControllerCommonList extends Controller {
	public function index() {
		$this->load->language('common/list');
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$this->load->model('catalog/product');

		$this->load->model('tool/image');
		$this->load->model('catalog/category');

        if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
        }
        
        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

		$result_products = $this->model_catalog_product->getHotProducts(4);

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

				
				$data['hot_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],0,15,
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


		$data['filterD'] = $this->load->controller('extension/module/filter_allD');
		$data['filterM'] = $this->load->controller('extension/module/filter_allM');
		
        $data['random'] = $this->load->controller('extension/module/random');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['menu'] = $this->load->controller('common/menu');
		$data['brands'] = $this->load->controller('product/manufacturer/brandsHome');
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header_list');

		$this->response->setOutput($this->load->view('common/list', $data));
	}
}
