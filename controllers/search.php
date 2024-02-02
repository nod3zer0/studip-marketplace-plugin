<?php

use Marketplace\TagDemand;

class SearchController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplace_search');
        PageLayout::setTitle('Search');
    }
}
