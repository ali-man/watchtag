<?php
class ControllerCommonServiceMessage extends Controller {
	private $error = array();	
	public function index(){
        $this->load->language('common/message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('common/service_message');

		$this->getList();
    }


	public function edit(){

		$this->load->language('common/message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('common/service_message');

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
			'href' => $this->url->link('common/service_message', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('common/service_message/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		
		$data['messages'] = array();

		$results = $this->model_common_service_message->getMessages();

		foreach ($results as $result) {
			$data['messages'][] = array(
				'message_id'  => $result['service_message_id'],
				'name'       => $result['fullname'],
				'phone'     => $result['phone'],
				'type' 			=> $result['type'],	
				'viewed' 			=> $result['viewed'],	
				'edit'       => $this->url->link('common/service_message/edit', 'user_token=' . $this->session->data['user_token'] . '&message_id=' . $result['service_message_id'] . $url, true)
			);
		}
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/service_message_list', $data));
	}

	protected function getForm() {

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/service_message', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['cancel'] = $this->url->link('common/service_message', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->get['message_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$message_info = $this->model_common_service_message->getMessage($this->request->get['message_id']);
            $images = $this->model_common_service_message->getMessageImages($this->request->get['message_id']);

            $this->model_common_service_message->updateMessage($this->request->get['message_id']);
		}

		$data['fullname'] = $message_info['fullname'];
		$data['phone'] = $message_info['phone'];
		$data['email'] = $message_info['email'];
		$data['type'] = $message_info['type'];
        $data['images'] = [];
        $this->load->model('tool/image');
        foreach($images as $image){
            
            $link = $this->model_tool_image->resize($image['image'], 200, 200);
            array_push($data['images'],$link);
        }

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('common/service_message_form', $data));
	}


	public function delete() {
		$this->load->language('common/message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('common/service_message');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $message_id) {
				$this->model_common_service_message->deleteMessage($message_id);
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

			$this->response->redirect($this->url->link('common/service_message', 'user_token=' . $this->session->data['user_token'] . $url, true));
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