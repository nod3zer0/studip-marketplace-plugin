<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
class SearchController extends \Marketplace\Controller
{

    public function index_action()
    {




        Navigation::activateItem('marketplace_root/marketplace_search');
        PageLayout::setTitle('Search');
        $db = DBManager::get();
        $query = Request::get('search-query');
        if ($query != '') {
            $generator = new SqlGenerator();
            $sql = $generator->generateSQL($query);
            $this->all_demands= \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
        }else{
            $this->all_demands= \Marketplace\Demand::findBySQL("LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id");
        //    $this->st = $db->fetchAll("SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id", []);
        }


    }

}
