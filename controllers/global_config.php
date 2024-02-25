<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;
use \Marketplace\MarketplaceModel;
use \Marketplace\Tag;

class GlobalConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/global_config');
        PageLayout::setTitle('Configuration');
    }

    public function save_config_action()
    {
        $config = json_decode(file_get_contents('php://input'), true);
        MarketplaceModel::update_marketplaces($config["marketplaces"]);
        Tag::update_tags($config["tags"]);
        $db = DBManager::get();
        $old_marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        PageLayout::postSuccess('Properties were saved successfully.');
        $this->render_text('' . json_encode($old_marketplaces));
    }

    public function post_success_action()
    {
        echo MessageBox::success('Message', ['optional details'], true);
        $this->render_nothing();
    }

    public function get_tags_action()
    {
        $db = DBManager::get();
        $tags = $db->fetchAll("SELECT * FROM mp_tag");
        $this->render_text('' . json_encode($tags));
    }

    public function get_config_action()
    {
        $db = DBManager::get();
        $old_marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        $this->render_text('' . json_encode($old_marketplaces));
    }
}
