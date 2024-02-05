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
            $this->st = $db->fetchAll($sql, []);
        }else{
            $this->st = $db->fetchAll("SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id", []);
        }


    }

}
