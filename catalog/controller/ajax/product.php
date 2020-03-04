<?php
class ControllerAjaxProduct extends Controller {
	private $error = array();

    public function index(){
        $this->load->model('tool/image');
        if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
        }
        
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('tool/image');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        $images = $this->model_catalog_product->getProductImages($product_id);


        $data['product_id'] = $product_id;
        $data['name'] = $product_info['name'];
        $data['stock_status'] = $product_info['stock_status'];
        $data['description'] = utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
        $data['length']	     = (float)$product_info['length'];
        $data['width']	     = (float)$product_info['width'];
        $data['height']	     = (float)$product_info['height'];
        $data['href']        = html_entity_decode($this->url->link('product/product', 'product_id='.$product_info['product_id']));
        $category = $this->model_catalog_product->getCategories($product_id);

        if($category){
            $category_item = $this->model_catalog_category->getCategory($category[0]['category_id']);	
        }


        if ((float)$product_info['special']) {
            $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        } else {
            $data['special'] = false;
        }

        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        } else {
            $data['price'] = false;
        }

        if ($this->config->get('config_tax')) {
            $data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
        } else {
            $data['tax'] = false;
        }
        $data['tax'] = $data['tax'];
		$data['price'] = $data['price'];
					
        $data['images'] = [];
        foreach($images as $image){
            array_push($data['images'],$this->model_tool_image->resize($image['image'], 541, 500));
        }

        if ($product_info['image']) {
            $data['image'] = $this->model_tool_image->resize($product_info['image'], 541, 500);
        } else {
            $data['image'] = $this->model_tool_image->resize('placeholder.png');
        }

        if(in_array($data['product_id'],$this->session->data['wishlist'])){
            $data['wishlist'] = true;
        }
        else{
            $data['wishlist'] = false;
        }
        
        $data['filter_groups'] = $this->model_catalog_product->getProductFilters($this->request->get['product_id']);


        $json['success']= $data;
        $json['success']['category_name'] = $category_item['name'];
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }


}
