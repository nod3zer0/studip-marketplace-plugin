<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class SearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        //TODO move $navigations in all controlers somewhere else (DRY)
        $navigation = Navigation::getItem('default_marketplace/marketplace_search');
        $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'search/index/', []) . $marketplace_id);
        if ($GLOBALS['user']->perms === 'root') {
            $navigation = Navigation::getItem('default_marketplace/marketplace_config');
            $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'config/index/', []) . $marketplace_id);
        }
        $navigation = Navigation::getItem('default_marketplace/marketplace_overview');
        $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'overview/index/', []) . $marketplace_id);


        $this->marketplace_id = $marketplace_id;
        Navigation::activateItem('default_marketplace/marketplace_search');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        $db = DBManager::get();
        $query = Request::get('search-query');
        if ($query != '') {


            $db = DBManager::get();
            $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property", []);
            // $custom_properties = array_map(function ($value) {
            //     return $value['name'];
            // }, $custom_properties);


            $generator = new SqlGenerator();
            try {
                $sql = $generator->generateSQL($query, $custom_properties, $marketplace_id);

                $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
            } catch (SearchException $e) {
                PageLayout::postError('Error', [$e->getMessage()]);
                $this->all_demands = [];
                return;
            }
        } else {

            $query = "LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id LEFT JOIN mp_property ON mp_property.demand_id=mp_demand.id LEFT JOIN mp_custom_property ON mp_custom_property.id=mp_property.custom_property_id";

            if ($marketplace_id) {
                $query .= " WHERE mp_marketplace.id = ? Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id";
                $this->all_demands = \Marketplace\Demand::findBySQL($query, [$marketplace_id]);
            } else {
                $query .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id";
                $this->all_demands = \Marketplace\Demand::findBySQL($query);
            }


            //    $this->st = $db->fetchAll("SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id", []);
        }
    }

    public function get_attributes_action($marketplace_id)
    {
        $db = DBManager::get();
        $custom_properties = $db->fetchAll("SELECT name, type FROM mp_custom_property WHERE marketplace_id = ?", [$marketplace_id]);
        $this->render_text('' . json_encode($custom_properties));
    }

    public function get_tags_action()
    {
        $db = DBManager::get();
        $tags = $db->fetchAll("SELECT name FROM mp_tag", []);
        $this->render_text('' . json_encode($tags));
    }
}
