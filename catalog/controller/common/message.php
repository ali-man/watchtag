<?php
class ControllerCommonMessage extends Controller {
	private $error = array();
    
    public function add() {
        $this->load->language('common/message');
        $this->load->model('common/message');
        if($this->validateForm()){
            $this->model_common_message->addMessage($this->request->post);

            $json = $this->language->get('text_success');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
        else{
            $json['error_text'] = $this->language->get('error_text');
            $json['error'] = '';
            if($this->error['name'])
                $json['error'] = $json['error'] . $this->error['name']."<br>";

            if($this->error['phone'])
                $json['error'] = $json['error'] . $this->error['phone']."<br>"; 

            if($this->error['question'])
                $json['error'] = $json['error'] . $this->error['question']."<br>";                
            
                if($this->error['mail'])
                $json['error'] = $json['error'] . $this->error['mail']."<br>";
                
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

    public function getQuantity() {
        $this->load->model('common/message
        ');
		$results = $this->model_common_message->getNotViewed()['COUNT(*)'];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($results));
	}

    public function validateForm(){

        if($this->request->post['type'] == "callback" || $this->request->post['type'] == "for_product"){
            if(strlen($this->request->post['phone']) == ""){
                $this->error['phone'] = $this->language->get('error_phone');
            }
        }

        if($this->request->post['type'] == "question")
        {
            if($this->request->post['text'] == ""){
                $this->error['question'] = $this->language->get('error_question');
            }
            if($this->request->post['mail'] == ""){
                $this->error['mail'] = $this->language->get('error_email');
            }
            if($this->request->post['name'] == ""){
                $this->error['name'] = $this->language->get('error_name');
            }
            if(strlen($this->request->post['phone']) == ""){
                $this->error['phone'] = $this->language->get('error_phone');
            }
        }


        return !$this->error;
    }

}
