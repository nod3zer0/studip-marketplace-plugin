<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class SearchController extends \Marketplace\Controller
{

    public function index_action()
    {




        Navigation::activateItem('marketplace_root/default_marketplace/marketplace_search');
        PageLayout::setTitle('Search');
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
                $sql = $generator->generateSQL($query, $custom_properties);
                $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
            } catch (SearchException $e) {
                PageLayout::postError('Error', [$e->getMessage()]);
                $this->all_demands = [];
                return;
            }
        } else {
            $this->all_demands = \Marketplace\Demand::findBySQL("LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id");
            //    $this->st = $db->fetchAll("SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id", []);
        }
    }
}
