<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;

class MyDemandsController extends \Marketplace\Controller
{

    private function buildSidebar(string $marketplace_id)
    {
        $sidebar = Sidebar::Get();
        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            'Create demand',
            $this->url_for('overview/create_demand/', []) . $marketplace_id,
            Icon::create('add'),
            ['data-dialog' => true]
        );
    }

    public function index_action($marketplace_id = '')
    {
        //TODO move $navigations in all controlers somewhere else (DRY)
        $navigation = Navigation::getItem('default_marketplace/marketplace_search');
        $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'search/index/', []) . $marketplace_id);
        if ($GLOBALS['user']->perms === 'root') {
            $navigation = Navigation::getItem('default_marketplace/marketplace_config');
            $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'config/index/', []) . $marketplace_id);
        }
        $navigation = Navigation::getItem('default_marketplace/marketplace_overview');
        $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'overview/index/', []) . $marketplace_id);
        $navigation = Navigation::getItem('default_marketplace/marketplace_my_demands');
        $navigation->setURL(PluginEngine::getURL($this->plugin, [], 'my_demands/index/', []) . $marketplace_id);
        Navigation::activateItem('default_marketplace/marketplace_my_demands');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        if ($marketplace_id) {
            $this->all_demands = \Marketplace\Demand::findBySQL("marketplace_id = ? AND author_id = ?", [$marketplace_id, $GLOBALS['user']->id]);
        } else {
            $this->all_demands = \Marketplace\Demand::findBySQL("author_id = ?", [$GLOBALS['user']->id]);
        }

        MyDemandsController::buildSidebar($marketplace_id);
    }
}
