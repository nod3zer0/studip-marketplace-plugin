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
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_my_demands');
        PageLayout::setTitle(\Marketplace\MarketplaceModel::find($marketplace_id)->name);
        if ($marketplace_id) {
            $this->all_demands = \Marketplace\Demand::findBySQL("marketplace_id = ? AND author_id = ?", [$marketplace_id, $GLOBALS['user']->id]);
        } else {
            $this->all_demands = \Marketplace\Demand::findBySQL("author_id = ?", [$GLOBALS['user']->id]);
        }

        MyDemandsController::buildSidebar($marketplace_id);
    }
}
