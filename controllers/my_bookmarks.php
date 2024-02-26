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

    public function add_bookmark_action($demand_id)
    {
        \Marketplace\Bookmark::setBookmark($demand_id, $GLOBALS['user']->id, true);
        $this->render_text('');
    }

    public function remove_bookmark_action($demand_id)
    {
        \Marketplace\Bookmark::setBookmark($demand_id, $GLOBALS['user']->id, false);
        $this->render_text('');
    }

    public function set_bookmark_action($demand_id, $bookmark_status)
    {
        \Marketplace\Bookmark::setBookmark($demand_id, $GLOBALS['user']->id, $bookmark_status == 'true' ? true : false);
        $this->render_text('');
    }

    public function get_bookmark_action($demand_id)
    {
        $bookmark = \Marketplace\Bookmark::getBookmarkByDemand($demand_id, $GLOBALS['user']->id);
        $bookmarked = false;
        if ($bookmark) {
            $bookmarked = true;
        }

        $this->render_text('' . json_encode(['bookmarked' => $bookmarked]));
    }
}
