<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$brands = $this->model_catalog_manufacturer->getManufacturers();
		$data['count_brands'] = count($brands);
		$data['count_products'] = $this->model_catalog_product->getTotalProducts([]);

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		return $this->load->view('common/search', $data);
	}

	public function searchForHeader()
	{
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		return $this->load->view('common/search_for_header', $data);	
	}
}