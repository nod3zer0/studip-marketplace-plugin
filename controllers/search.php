<?php

use Marketplace\TagDemand;

class SearchController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplace_search');
        PageLayout::setTitle('Search');
        $db = DBManager::get();
        $this->st = $db->fetchAll("SELECT * FROM mp_demand", []);
    }

    public function search_action()
    {
    }
}
