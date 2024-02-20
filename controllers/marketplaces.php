<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;
use \Marketplace\MarketplaceModel;

class MarketplacesController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplaces');
        PageLayout::setTitle('Marketplaces');
        $this->marketplaces = MarketplaceModel::findBySQL("1");
    }
}
