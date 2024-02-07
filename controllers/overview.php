<?php

use Marketplace\TagDemand;
use \Marketplace\CustomProperty;

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
        $this->tags = \Marketplace\TagDemand::findBySQL("demand_id = ?", [$demand_id]);
        $this->tagsString = "";
        foreach ($this->tags as $tag) {
            $this->tagsString .= $tag->mp_tag->name . ",";
        }
        $this->tagsString = rtrim($this->tagsString, ",");
    }

    public function get_custom_properties_action()
    {
        $demand_id = json_decode(file_get_contents('php://input'), true);
        $db = DBManager::get();
        $properties = $db->fetchAll("SELECT * FROM mp_custom_property LEFT JOIN (SELECT value, demand_id, custom_property_id FROM mp_property WHERE mp_property.demand_id = ? ) t2 ON mp_custom_property.id = t2.custom_property_id", [$demand_id]);

        //$properties = \Marketplace\CustomProperty::findBySQL("LEFT JOIN mp_property ON mp_custom_property.id = mp_property.custom_property_id");
        $this->render_text('' . json_encode($properties));
    }

    public function get_custom_properties_testing_action()
    {
        $db = DBManager::get();
        $properties = $db->fetchAll("SELECT * FROM mp_property");
        $this->render_text('' . json_encode($properties));
    }

    public function update_custom_properties_action(string $demand_id = '')
    {
        $properties = json_decode(file_get_contents('php://input'), true);
        Property::update_custom_properties($properties['properties'], $properties['demand_id']);
        $this->render_text('' . json_encode($properties));
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


        if ($this->demand_obj->store() !== false) {
            PageLayout::postSuccess('The demand was
successfully saved');
        } else {
            PageLayout::postError('An error occurred while
saving the demand');
        }

        $tags = explode(",", Request::get('tags'));
        $previous_tags = explode(",", Request::get('tags_previous'));
        foreach ($previous_tags as $tag) {
            if (!in_array($tag, $tags)) {
                TagDemand::deleteTag($tag, $this->demand_obj->id);
            }
        }
        foreach ($tags as $tag) {
            if (!in_array($tag, $previous_tags)) {
                TagDemand::addTag($tag, $this->demand_obj->id);
            }
        }

        // foreach ($tags as $tag) {
        //     TagDemand::addTag($tag, $this->demand_obj->id);
        // }


        $this->redirect('overview/index');
    }
}
