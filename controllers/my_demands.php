<?php

/**
 *  Controller for My Commodities list
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class MyDemandsController extends \Marketplace\Controller
{

    private function buildSidebar(string $marketplace_id, string $comodity_name_singular)
    {
        $sidebar = Sidebar::Get();
        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            'Create ' . $comodity_name_singular,
            $this->url_for('overview/create_demand/', []) . $marketplace_id,
            Icon::create('add'),
            ['data-dialog' => true]
        );
    }

    public function index_action($marketplace_id = '')
    {
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        Navigation::activateItem('marketplace_' . $marketplace_id . '/overview/my_demands');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        if ($marketplace_id) { //check if global or local demands
            $this->all_demands = \Marketplace\Demand::findBySQL("marketplace_id = ? AND author_id = ?", [$marketplace_id, $GLOBALS['user']->id]);
        } else {
            $this->all_demands = \Marketplace\Demand::findBySQL("author_id = ?", [$GLOBALS['user']->id]);
        }
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;
        Helpbar::get()->addPlainText("My " .  $this->marketplace_comodity_name_plural, "Here are shown all the " . $this->marketplace_comodity_name_plural . " you have created.");
        MyDemandsController::buildSidebar($marketplace_id, $marketplace_obj->comodity_name_singular);
    }
}
