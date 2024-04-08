<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\CustomProperty;
use \Marketplace\MarketplaceModel;

class MarketplacesController extends \Marketplace\Controller
{

    public function index_action()
    {
        Helpbar::get()->addPlainText("Marketplaces", "Here are shown all the marketplaces. In a marketplace you can create demands and offers or search for commodities, created by other users.");
        Navigation::activateItem('marketplace_root/marketplaces');
        PageLayout::setTitle('Marketplaces');
        $this->marketplaces = MarketplaceModel::findBySQL("1");
    }
}
