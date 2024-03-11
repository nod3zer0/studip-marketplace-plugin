<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\SearchNotification;
use \Marketplace\Category;
use \Marketplace\MarketplaceModel;
use \Marketplace\CustomProperty;
use \Marketplace\Tag;

class AdvancedSearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {

        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_tag_select.js');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_category_select.js');
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');

        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/marketplace_advanced_search');
        PageLayout::setTitle(MarketplaceModel::find($marketplace_id)->name);
        $this->marketplace_id = $marketplace_id;
        $this->categories = json_encode(Category::get_categories($marketplace_id));
        $this->properties = CustomProperty::findBySQL("marketplace_id = ?", [$marketplace_id]);
        $this->tags = Tag::get_all_tags_csv();
        //$query = Request::get('search-query');
        $properties = Request::getInstance();
        // foreach ($this->properties as $property) {
        //     $properties[] =  Request::get(str_replace(" ", "_", $property->name));
        // }
    }
}
