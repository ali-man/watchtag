<?php
class ModelCommonMessage extends Model {

	public function addMessage($data) {
                $this->db->query("INSERT INTO " . DB_PREFIX 
                . "message SET name = '" . $this->db->escape($data['name']) 
                . "', phone = '" . $this->db->escape($data['phone']) 
                . "', mail = '" . $this->db->escape($data['mail']) 
                . "', text = '" . $this->db->escape($data['text']) 
                . "', type = '" . $this->db->escape($data['type']). "'");
                
    }
    
    public function getMessages() {
        $sql = "SELECT id, name, type, product_id FROM " . DB_PREFIX . "message";
        
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

    public function getNotViewed() {
      $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "message WHERE viewed = '" . (int)0 ."'");
      
      return $query->row;
    }

}