<?php
class ControllerAjaxServiceMessage extends Controller {
	private $error = array();
    
    public function add() {
        $this->load->language('ajax/service_message');
        $this->load->model('ajax/service_message');
        $this->load->model('tool/upload');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            


            $uploadResult = $this->upload();
            if($uploadResult['error']){
                $data['error']['image'] = $uploadResult['error'];
            }
            else{
                $data['error'] = null;
                $this->request->post['file'] = $uploadResult['code'];
                $data['post'] = $this->request->post['file'];
                $this->model_ajax_service_message->add($this->request->post);
            }

            $data['success'] = $this->language->get('text_success');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($data));
		}
        else{
            $data['error'] = $this->error;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($data));
        }
    }

    public function getQuantity() {
        $this->load->model('ajax/service_message');
		$results = $this->model_ajax_service_message->getNotViewed()['COUNT(*)'];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($results));
    }
    
	private function validate() {
		if ((utf8_strlen(trim($this->request->post['fullname'])) < 1) || (utf8_strlen(trim($this->request->post['fullname'])) > 255)) {
			$this->error['fullname'] = $this->language->get('error_fullname');
        }
        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
        }
        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
        }
        if(($this->request->files['file']['name'][0] == "") && ($this->request->files['file']['name'][1] == "") && ($this->request->files['file']['name'][2] == "")){
            $this->error['image'] = $this->language->get('error_image');     
        }
		
		return !$this->error;
    }
    

    public function upload() {
		$this->load->language('tool/upload');

        $fileArray = $this->rearrange($this->request->files['file']);

        $json = array();

        foreach($fileArray as $file){

            if (!empty($file['name']) && is_file($file['tmp_name'])) {
                // Sanitize the filename
                $filename = $file['name'];

                // Validate the filename length
                if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array($file['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($file['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($file['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            }


        }

		
 
		
		if (!$json) {
            $json['code'] = [];
            foreach($fileArray as $file){
                if(!empty($file['name']) && is_file($file['tmp_name'])){
                    move_uploaded_file($file['tmp_name'], DIR_IMAGE . $file['name']);
                    array_push($json['code'],$file);
                }

            }
            
			

			// Hide the uploaded file path so people can not link to it directly.


		}
        return $json;

    }

    public function rearrange( $arr ){
        foreach( $arr as $key => $all ){
            foreach( $all as $i => $val ){
                $new[$i][$key] = $val;    
            }    
        }
        return $new;
    }

}
