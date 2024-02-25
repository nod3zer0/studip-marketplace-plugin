<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class MyBookmarksController extends \Marketplace\Controller
{
    public function index_action($marketplace_id = '')
    {
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_my_bookmarks');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        $this->all_demands = \Marketplace\Bookmark::getByMarketplace($marketplace_id, $GLOBALS['user']->id);
    }

    public function set_bookmark_action($demand_id)
    {
        \Marketplace\Bookmark::setBookmark($demand_id, $GLOBALS['user']->id, true);
    }

    public function remove_bookmark_action($demand_id)
    {
        \Marketplace\Bookmark::setBookmark($demand_id, $GLOBALS['user']->id, false);
    }

    public function get_bookmarks($demand_id)
    {
        $bookmark = \Marketplace\Bookmark::getByDemand($demand_id, $GLOBALS['user']->id);
        $this->render_text('' . json_encode($bookmark));
    }
}
