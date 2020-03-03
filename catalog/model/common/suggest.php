<?php
class ModelCommonSuggest extends Model {

    public function addMessage($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX
            . "suggests SET price = '" . $this->db->escape($data['price'])
            . "', address = '" . $this->db->escape($data['address'])
            . "', firstname = '" . $this->db->escape($data['firstname'])
            . "', lastname = '" . $this->db->escape($data['lastname'])
            . "', street = '" . $this->db->escape($data['street'])
            . "', supplement_address = '" . $this->db->escape($data['supplement_address'])
            . "', zip_code = '" . $this->db->escape($data['zip_code'])
            . "', city = '" . $this->db->escape($data['city'])
            . "', delivery_method = '" . $this->db->escape($data['delivery_method'])
            . "', country = '" . $this->db->escape($data['country'])
            . "', text = '" . $this->db->escape($data['text'])
            . "', product_id = '" . $this->db->escape($data['product_id'])
            . "', phone = '" . $this->db->escape($data['phone']). "'");
    }

    public function getNotViewed() {
        $query = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "suggests WHERE viewed = '" . (int)0 ."'");

        return $query->row;
    }

}