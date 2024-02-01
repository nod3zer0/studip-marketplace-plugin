<?php

use Marketplace\TagDemand;

class OverviewController extends \Marketplace\Controller
{
    private function buildSidebar()
    {
        $sidebar = Sidebar::Get();
        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            'Create demand',
            $this->url_for('overview/create_demand'),
            Icon::create('add'),
            ['data-dialog' => true]
        );
    }

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplace_overview');
        PageLayout::setTitle('Demands');
        OverviewController::buildSidebar();
        $this->all_demands = \Marketplace\Demand::findBySQL("1");
    }

    public function demand_detail_action(string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        $this->tags = \Marketplace\TagDemand::findBySQL("demand_id = ?", [$demand_id]);
    }

    public function create_demand_action(string $demand_id = '')
    {
        PageLayout::setTitle('Edit demand');
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        if (!$this->demand_obj) {
            $this->demand_obj = new \Marketplace\Demand();
        }
    }


    public function store_demand_action(string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        if (!$this->demand_obj) {
            $this->demand_obj = new \Marketplace\Demand();
            $this->demand_obj->author_id = $GLOBALS['user']->id;
        }
        if (!$this->demand_obj->hasPermission()) {
            PageLayout::postError('You do not have permission to customize the text');
            $this->redirect('overview/index');
            return;
        }
        $this->demand_obj->setData([
            'title' => Request::get('title'),
            'description' => Request::get('description')
        ]);

        $tags = explode(",", Request::get('tags'));

        if ($this->demand_obj->store() !== false) {
            PageLayout::postSuccess('The demand was
successfully saved');
        } else {
            PageLayout::postError('An error occurred while
saving the demand');
        }

        foreach ($tags as $tag) {
            TagDemand::addTag($tag, $this->demand_obj->id);
        }


        $this->redirect('overview/index');
    }
}
