<?php
class ControllerCommonSuggest extends Controller {
    private $error = array();
    public function index(){
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        $this->load->language('common/suggest');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('localisation/country');
        $this->load->model('account/customer');

        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        $data['firstname'] = $customer_info['firstname'];
        $data['lastname'] = $customer_info['lastname'];
        $data['telephone'] = $customer_info['telephone'];

        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $data['product_id'] = (int)$this->request->get['product_id'];

        $product_info = $this->model_catalog_product->getProduct($product_id);
        if($product_info){
            $data['name'] = $product_info['name'];
            if ($product_info['image']) {
                $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
            } else {
                $data['thumb'] = '';
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['price'] = false;
            }

            if ((float)$product_info['special']) {
                $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['special'] = false;
            }

            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'],$this->session->data['currency']);
                $data['numberTax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'],false);
            } else {
                $data['tax'] = false;
                $data['numberTax'] = false;
            }
            $data['tax'] = $data['tax'];
            $data['price'] = $data['price'];

            $this->load->model('localisation/currency');
            $data['currencies'] = array();
            $data['codeCurrency'] = $this->session->data['currency'];

            $results = $this->model_localisation_currency->getCurrencies();

            foreach ($results as $result) {
                if ($result['status']) {
                    $data['currencies'][] = array(
                        'title'        => $result['title'],
                        'code'         => $result['code'],
                        'symbol_left'  => $result['symbol_left'],
                        'symbol_right' => $result['symbol_right']
                    );
                }
            }
            $data['actionCurrency'] = $this->url->link('common/currency/currency', '', $this->request->server['HTTPS']);

            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');
            $this->response->setOutput($this->load->view('common/suggest', $data));
        }
        else{
            $this->document->setTitle($this->language->get('text_error'));

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header_second');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function store(){
        $this->load->language('common/suggest');
        $this->load->model('common/suggest');

        if($this->validateForm()){
            $this->request->post['price'] = $this->request->post['price'] . $this->request->post['currency'];
            $this->model_common_suggest->addMessage($this->request->post);
            
           $to = 'eternitye60@gmail.com';
           $subject = "Человек, которого заказываете тура";
           $body = "sdsadas";
            
            // $headers = 'From: watchtag.rocketon.ru asd@rocketon.ru' . "\r\n" ;
            // $headers .='Reply-To: '. $to . "\r\n" ;
            // $headers .='X-Mailer: PHP/' . phpversion();
            // $headers .= "MIME-Version: 1.0\r\n";
            // $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   
            // $bool = mail($to, $subject, $body,$headers);
            
            // $json = $bool; 
            
            $json = $this->language->get('text_success');


            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
        else{
            $json['error_text'] = $this->language->get('error_text');
            $json['error'] = '';
            if($this->error['price'])
                $json['error'] = $json['error'] . $this->error['price']."<br>";

            if($this->error['firstname'])
                $json['error'] = $json['error'] . $this->error['firstname']."<br>";

            if($this->error['lastname'])
                $json['error'] = $json['error'] . $this->error['lastname']."<br>";

            if($this->error['phone'])
                $json['error'] = $json['error'] . $this->error['phone']."<br>";

            if($this->error['street'])
                $json['error'] = $json['error'] . $this->error['street']."<br>";

            if($this->error['country'])
                $json['error'] = $json['error'] . $this->error['country']."<br>";

            if($this->error['product_id'])
                $json['error'] = $json['error'] . $this->error['error_text']."<br>";

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

    public function validateForm(){

        if($this->request->post['price'] == ""){
            $this->error['price'] = $this->language->get('error_price');
        }
        if($this->request->post['firstname'] == ""){
            $this->error['firstname'] = $this->language->get('error_firstname');
        }
        if($this->request->post['lastname'] == ""){
            $this->error['lastname'] = $this->language->get('error_lastname');
        }
        if(strlen($this->request->post['phone']) == ""){
            $this->error['phone'] = $this->language->get('error_phone');
        }
        if(strlen($this->request->post['street']) == ""){
            $this->error['street'] = $this->language->get('error_street');
        }
        if(strlen($this->request->post['country']) == ""){
            $this->error['country'] = $this->language->get('error_country');
        }

        if(strlen($this->request->post['product_id']) == ""){
            $this->error['product_id'] = $this->language->get('error_product_id');
        }


        return !$this->error;
    }

    public function getQuantity() {
        $this->load->model('common/suggest
        ');
        $results = $this->model_common_suggest->getNotViewed()['COUNT(*)'];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($results));
    }
}