<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;

class ConfigController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        $this->marketplace_id = $marketplace_id;
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_config');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
    }

    public function save_config_action($marketplace_id)
    {
        CustomProperty::update_properties(json_decode(file_get_contents('php://input'), true), $marketplace_id);
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property WHERE marketplace_id = ?", [$marketplace_id]);
        PageLayout::postSuccess('Properties were saved successfully.');
        $this->render_text('' . json_encode($old_properties));
    }

    public function post_success_action()
    {
        echo MessageBox::success('Message', ['optional details'], true);
        $this->render_nothing();
    }

    public function get_properties_action($marketplace_id)
    {
        $db = DBManager::get();
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property WHERE marketplace_id = ?", [$marketplace_id]);
        $this->render_text('' . json_encode($old_properties));
    }
}
