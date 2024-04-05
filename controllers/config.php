<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;
use \Marketplace\Category;

class ConfigController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        $this->marketplace_id = $marketplace_id;
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_config/general');
        $markeplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        $this->marketplace_name = $markeplace_obj->name;
        PageLayout::setTitle($this->marketplace_name);
        $this->enabled =  $markeplace_obj->enabled;
        $this->comodity_name = $markeplace_obj->comodity_name_singular;
        $this->comodity_name_plural = $markeplace_obj->comodity_name_plural;
    }

    public function save_general_config_action($marketplace_id)
    {
        $marketplace = \Marketplace\MarketplaceModel::find($marketplace_id);


        $marketplace->enabled = Request::submitted('enabled');
        $marketplace->comodity_name_singular = Request::get('comodity_name');
        $marketplace->comodity_name_plural = Request::get('comodity_name_plural');
        $marketplace_id->name = Request::get('marketplace_name');
        $marketplace->store();
        PageLayout::postSuccess('Configuration save successfully.');
        $this->redirect('config/index/' . $marketplace_id);
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
        $old_properties = $db->fetchAll("SELECT * FROM mp_custom_property WHERE marketplace_id = ? ORDER BY mp_custom_property.order_index", [$marketplace_id]);
        $this->render_text('' . json_encode($old_properties));
    }

    public function categories_action($marketplace_id = '')
    {
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_config/categories');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/categories_config.js');
        $this->marketplace_id = $marketplace_id;
    }

    public function properties_action($marketplace_id = '')
    {
        $this->marketplace_id = $marketplace_id;
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_config/properties');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
    }

    public function get_categories_action($marketplace_id)
    {
        $categories = Category::get_categories($marketplace_id);
        $this->render_text('' . json_encode($categories));
    }

    public function set_categories_action($marketplace_id)
    {
        Category::set_categories(json_decode(file_get_contents('php://input'), true)["categories"], $marketplace_id);
        PageLayout::postSuccess('Categories were saved successfully.');
        $this->render_text('');
    }
}
