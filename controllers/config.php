<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;

class ConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/default_marketplace/marketplace_config');
        PageLayout::setTitle('Configuration');
    }

    public function save_config_action()
    {
        CustomProperty::update_properties(json_decode(file_get_contents('php://input'), true));
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property");
        PageLayout::postSuccess('Properties were saved successfully.');
        $this->render_text('' . json_encode($old_properties));
    }

    public function post_success_action()
    {
        echo MessageBox::success('Message', ['optional details'], true);
        $this->render_nothing();
    }

    public function get_properties_action()
    {
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property");
        $this->render_text('' . json_encode($old_properties));
    }
}
