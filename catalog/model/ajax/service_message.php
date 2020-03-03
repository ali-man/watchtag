<?php
class ModelAjaxServiceMessage extends Model {

	public function add($data) {

                
    $this->db->query("INSERT INTO " . DB_PREFIX 
    . "service_message SET fullname = '" . $this->db->escape($data['fullname']) 
    . "', phone = '" . $this->db->escape($data['telephone']) 
    . "', email = '" . $this->db->escape($data['email'])  
    . "', type = '" . $this->db->escape($data['type']). "'");

    $service_message_id = $this->db->getLastId();
    if (isset($data['file'])) {
      foreach ($data['file'] as $product_image) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "service_message_image SET service_message_id = '" . (int)$service_message_id . "', image = '" . $this->db->escape($product_image['name']) . "'");
      }
    }
                
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
    $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "service_message WHERE viewed = '" . (int)0 ."'");
    
    return $query->row;
  }


}