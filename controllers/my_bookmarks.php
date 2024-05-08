<?php

/**
 *  Controller for bookmarks list
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class MyBookmarksController extends \Marketplace\Controller
{
    public function index_action($marketplace_id = '')
    {
        Helpbar::get()->addPlainText("My bookmarks", "Here are shown all the demands you have bookmarked.");
        if ($marketplace_id) {
            $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
            $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;
            Navigation::activateItem('marketplace_' . $marketplace_id . '/overview/my_bookmarks');
            $this->all_demands = \Marketplace\Bookmark::getByMarketplace($marketplace_id, $GLOBALS['user']->id);
        } else {
            $this->marketplace_comodity_name_plural = 'Commodities';
            Navigation::activateItem('/marketplace_root/my_bookmarks');
            $this->all_demands = \Marketplace\Bookmark::getAllBookmarks($GLOBALS['user']->id);
        }
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');
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
