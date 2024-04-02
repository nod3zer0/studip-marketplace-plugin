<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SimpleSearch;
use \Marketplace\SearchNotification;
use \Marketplace\Category;
use \Marketplace\MarketplaceModel;
use \Marketplace\CustomProperty;
use \Marketplace\Tag;

class SimpleSearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/marketplace_simple_search');
        PageLayout::setTitle(MarketplaceModel::find($marketplace_id)->name);
        $this->marketplace_id = $marketplace_id;
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        $this->limit = Request::get('limit') ?: get_config('ENTRIES_PER_PAGE');
        $request_data = Request::getInstance();


        if ($request_data["search-query"] != '') {
            $advanced_search = new SimpleSearch();
            $sql = $advanced_search->generateSQL($request_data["search-query"], $marketplace_id,   $this->limit);
            $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
        } else {
            $this->all_demands = \Marketplace\Demand::findBySQL("marketplace_id = ?", [$marketplace_id]);
        }
    }
}
