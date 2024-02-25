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
        PageLayout::postSuccess('Configuration was saved successfully.');
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
        //count number of references
        $tags = $db->fetchAll("SELECT mp_tag.id AS id, mp_tag.name AS name, COUNT(mp_tag_demand.demand_id) AS number_of_references FROM mp_tag LEFT JOIN mp_tag_demand ON mp_tag.id = mp_tag_demand.tag_id GROUP BY mp_tag.id");
        $this->render_text('' . json_encode($tags));
    }

    public function get_config_action()
    {
        $db = DBManager::get();
        $old_marketplaces = $db->fetchAll("SELECT * FROM mp_marketplace");
        $this->render_text('' . json_encode($old_marketplaces));
    }
}
