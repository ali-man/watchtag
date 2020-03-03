<?php
class ControllerCommonFooter extends Controller {
    public function index() {
        $this->load->language('common/footer');

        $this->load->model('catalog/information');
        $this->load->model('catalog/category');
        $this->load->model('localisation/language');
        $this->load->model('localisation/currency');
        $data['languages'] = array();
        $data['informations'] = array();


        $data['currencies'] = array();

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

        $results = $this->model_localisation_language->getLanguages();

        $data['actionLanguage'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);
        $data['actionCurrency'] = $this->url->link('common/currency/currency', '', $this->request->server['HTTPS']);

        $data['codeLanguage'] = $this->session->data['language'];
        $data['codeCurrency'] = $this->session->data['currency'];

        foreach ($results as $result) {
            if ($result['status']) {
                $data['languages'][] = array(
                    'name' => substr($result['name'],0,2),
                    'code' => $result['code']
                );
            }
        }

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }
        $currentRoute = $this->request->get['route'];

        if($currentRoute == 'account/wishlist'){
            $data['cation_wish'] = true;
        }
        elseif($currentRoute == 'common/home'){
            $data['cation_home'] = true;
        }
        elseif($currentRoute == 'product/search'){
            $data['cation_search'] = true;
        }
        elseif($currentRoute == 'common/list'){
            $data['cation_list'] = true;

        }
        elseif($currentRoute == 'account/login' || $currentRoute == 'account/account' || $currentRoute == 'account/register' || $currentRoute == 'account/forgotten'){
            $data['cation_user'] = true;
        }

        $data['contact'] = $this->url->link('information/contact');
        $data['return'] = $this->url->link('account/return/add', '', true);
        $data['sitemap'] = $this->url->link('information/sitemap');
        $data['tracking'] = $this->url->link('information/tracking');
        $data['manufacturer'] = $this->url->link('product/manufacturer');
        $data['voucher'] = $this->url->link('account/voucher', '', true);
        $data['affiliate'] = $this->url->link('affiliate/login', '', true);
        $data['special'] = $this->url->link('product/special');
        $data['account'] = $this->url->link('account/account', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['newsletter'] = $this->url->link('account/newsletter', '', true);

        $data['yandex_metrika_html_code'] = $this->config->get('yandex_money_metrika_active') && $this->config->get('yandex_money_metrika_code')
            ? html_entity_decode($this->config->get('yandex_money_metrika_code'), ENT_QUOTES, 'UTF-8')
            : '';
        $data['yandex_money_kassa_show_in_footer'] = $this->config->get('yandex_money_kassa_enabled') && $this->config->get('yandex_money_kassa_show_in_footer');

        $data['home'] = $this->url->link('common/home', '', true);
        $data['search']	= $this->url->link('product/search','',true);
        $data['cart']	= $this->url->link('checkout/cart','',true);
        $data['list'] = $this->url->link('common/list');
        if($this->customer->isLogged()){
            $data['user'] = $this->url->link('account/account');
        }
        else{
            $data['user'] = $this->url->link('account/login');
        }

        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
        } else {
            $data['text_wishlist'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
        }
        $data['text_items'] = sprintf($this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));



        $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

        if($this->customer->isLogged()){
            $data['user_name'] = $this->customer->getFirstName() . " " . $this->customer->getLastName();
            $data['user_email'] = $this->customer->getEmail();
            $data['user_phone'] = $this->customer->getTelephone();
        }


        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
        }

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        $data['scripts'] = $this->document->getScripts('footer');

        return $this->load->view('common/footer', $data);
    }
}
