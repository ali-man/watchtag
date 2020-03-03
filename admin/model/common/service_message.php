<?php
class ModelCommonServiceMessage extends Model {

	public function getMessage($message_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "service_message WHERE service_message_id = '" . (int)$message_id . "'");
		
		return $query->row;
	}

	public function updateMessage($message_id) {
		$query = $this->db->query("UPDATE ".DB_PREFIX."service_message SET viewed = '1' WHERE  service_message_id = '" . (int)$message_id . "'");
		return $query->row;
	}

	public function getNotViewed() {
		$query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "service_message WHERE viewed = '" . (int)0 ."'");
		
		return $query->row;
	}

	public function getMessages() {
        $sql = "SELECT * FROM " . DB_PREFIX . "service_message";
        
        if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;

		}
		
	public function deleteMessage($message_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "service_message WHERE service_message_id = '" . (int)$message_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "service_message_image WHERE service_message_id = '" . (int)$message_id . "'");
	}
	public function getMessageImages($message_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "service_message_image WHERE service_message_id = '" . (int)$message_id . "'");
		return $query->rows;
	}		

}