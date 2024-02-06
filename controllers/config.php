<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;

class ConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplace_config');
        PageLayout::setTitle('Configuration');
    }

    public function save_config_action()
    {
        CustomProperty::update_properties(json_decode(file_get_contents('php://input'), true));
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property");
        $this->render_text('' . json_encode($old_properties));
    }

    public function get_properties_action()
    {
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property");
        $this->render_text('' . json_encode($old_properties));
    }
}
