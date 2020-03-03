<?php
class ControllerCommonSuggests extends Controller {
    private $error = array();
    public function index(){
        $this->load->language('common/suggests');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/suggests');

        $this->getList();
    }

    public function edit(){

        $this->load->language('common/suggests');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/suggests');

        $this->getForm();
    }

    protected function getList() {

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/suggests', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link('common/suggests/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $data['suggests'] = array();

        $results = $this->model_common_suggests->getMessages();

        foreach ($results as $result) {
            $data['suggests'][] = array(
                'id'  => $result['id'],
                'firstname'       => $result['firstname'],
                'product_id'     => $result['product_id'],
                'lastname' 			=> $result['lastname'],
                'viewed' 			=> $result['viewed'],
                'price' 			=> $result['price'],
                'edit'       => $this->url->link('common/suggests/edit', 'user_token=' . $this->session->data['user_token'] . '&message_id=' . $result['id'] . $url, true)
            );
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/suggests_list', $data));
    }

    protected function getForm() {
        $this->load->model('catalog/product');
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/suggests', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['cancel'] = $this->url->link('common/suggests', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['message_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $message_info = $this->model_common_suggests->getMessage($this->request->get['message_id']);
            $this->model_common_suggests->updateMessage($this->request->get['message_id']);
        }

        $data['firstname'] = $message_info['firstname'];
        $data['lastname'] = $message_info['lastname'];
        $data['price'] = $message_info['price'];
        $data['phone'] = $message_info['phone'];
        $data['street'] = $message_info['street'];
        $data['supplement_address'] = $message_info['supplement_address'];
        $data['zip_code'] = $message_info['zip_code'];
        $data['city'] = $message_info['city'];
        $data['delivery_method'] = $message_info['delivery_method'];
        $data['country'] = $message_info['country'];
        $data['text'] = $message_info['text'];

        if($message_info['product_id'] == 0){
            $data['product'] = "Это сообщение не для продуктов";
        }
        else{
            $product = $this->model_catalog_product->getProduct($message_info['product_id']);
            $data['product'] = $product['name'];
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('common/suggests_form', $data));
    }


    public function delete() {
        $this->load->language('common/message');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('common/message');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $message_id) {
                $this->model_common_message->deleteMessage($message_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_product'])) {
                $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_author'])) {
                $url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

            $this->response->redirect($this->url->link('common/message', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'common/message')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}