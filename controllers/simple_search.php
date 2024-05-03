<?php

/**
 *  Controller for Simple search
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */


use Marketplace\SearchException;
use Marketplace\TagDemand;
use \Marketplace\SearchNotification;
use \Marketplace\Category;
use \Marketplace\MarketplaceModel;
use \Marketplace\CustomProperty;
use \Marketplace\Tag;
use \search\SimpleSearch;

class SimpleSearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        Helpbar::get()->addPlainText("Searching", "Specified search query is searched inside the title and description of the demands.");
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/marketplace_simple_search');
        PageLayout::setTitle(MarketplaceModel::find($marketplace_id)->name);
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');

        $this->marketplace_id = $marketplace_id;
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        $this->limit = intval(Request::get('limit') ?: get_config('ENTRIES_PER_PAGE'));
        $this->order = Request::get('order') ?: 'mkdate_desc';
        $request_data = Request::getInstance();
        $this->query = $request_data["search-query_parameter"];

        if ($this->query != '') {
            $simple_search = new SimpleSearch();
            $sql = $simple_search->generateSQL($this->query, $marketplace_id,   $this->limit, $this->order);
            $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
        } else {
            $attribute_map = [
                'title' => 'title',
                'author' => 'auth_user_md5.username',
                'mkdate' => 'mkdate'
            ];
            $order_map = [
                'asc' => 'ASC',
                'desc' => 'DESC'
            ];
            $order = explode('_', $this->order);

            $this->all_demands = \Marketplace\Demand::findBySQL("LEFT JOIN auth_user_md5 ON author_id = user_id  WHERE marketplace_id = ?  ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?", [$marketplace_id, $this->limit]);
        }
    }
}
