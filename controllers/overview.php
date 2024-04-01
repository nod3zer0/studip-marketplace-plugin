<?php

use Marketplace\TagDemand;
use \Marketplace\CustomProperty;
use \Marketplace\Property;
use \Marketplace\Category;
use \Marketplace\CategoryDemand;


class OverviewController extends \Marketplace\Controller
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

    public function index_action(string $marketplace_id)
    {

        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_overview');
        PageLayout::setTitle($marketplace_obj->name);
        OverviewController::buildSidebar($marketplace_id, $marketplace_obj->comodity_name_singular);

        $this->marketplace_id = $marketplace_id;
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        //pagination
        $entries_per_page = get_config('ENTRIES_PER_PAGE');
        $page = Request::get('page') ?: 1;
        $this->page = $page;
        $this->marketplace_id = $marketplace_id;
        $this->number_of_demands = \Marketplace\Demand::countBymarketplace_id($marketplace_id);

        $this->all_demands = \Marketplace\Demand::findBySQL("marketplace_id = ? ORDER BY chdate DESC LIMIT ?,?", [$marketplace_id, ($page - 1) * $entries_per_page, $entries_per_page]);
    }

    public function demand_detail_action(string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        $this->avatar = Avatar::getAvatar($this->demand_obj->author_id);
        PageLayout::setTitle($this->demand_obj->title);
        $this->tags = \Marketplace\TagDemand::findBySQL("demand_id = ?", [$demand_id]);
        $db = DBManager::get();
        $this->properties = $db->fetchAll("SELECT * FROM mp_custom_property LEFT JOIN (SELECT value, demand_id, custom_property_id FROM mp_property WHERE mp_property.demand_id = ? ) t2 ON mp_custom_property.id = t2.custom_property_id WHERE mp_custom_property.marketplace_id = ? ORDER BY mp_custom_property.order_index", [$demand_id, $this->demand_obj->marketplace_id]);
        $this->selected_path = CategoryDemand::get_saved_path($demand_id);
    }

    public function create_demand_action(string $marketplace_id, string $demand_id = '')
    {
        PageLayout::setTitle('Edit demand');
        $this->marketplace_id = $marketplace_id;
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
        $db = DBManager::get();
        $this->properties = $db->fetchAll("SELECT * FROM mp_custom_property LEFT JOIN (SELECT value, demand_id, custom_property_id FROM mp_property WHERE mp_property.demand_id = ? ) t2 ON mp_custom_property.id = t2.custom_property_id WHERE mp_custom_property.marketplace_id = ? ORDER BY mp_custom_property.order_index", [$demand_id, $marketplace_id]);

        $this->selected_path = CategoryDemand::get_saved_path($demand_id);
        $this->categories = json_encode(Category::get_categories($marketplace_id));
    }


    public function store_demand_action(string $marketplace_id, string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        if (!$this->demand_obj) {
            $this->demand_obj = new \Marketplace\Demand();
            $this->demand_obj->author_id = $GLOBALS['user']->id;
            $this->demand_obj->marketplace_id = $marketplace_id;
        }
        if (!$this->demand_obj->hasPermission()) {
            PageLayout::postError('You do not have permission to customize the text');
            $this->redirect('overview/index/' . $marketplace_id);
            return;
        }

        if (Request::submitted('delete_btn')) {
            if ($this->demand_obj->delete()) {
                PageLayout::postSuccess('The demand was successfully deleted');
            } else {
                PageLayout::postError('An error occurred while deleting the demand');
            }
            $this->redirect('overview/index/' . $marketplace_id);
            return;
        }

        $this->demand_obj->setData([
            'title' => Request::get('title'),
            'description' => Studip\Markup::purifyHtml(Request::get('description'))
        ]);


        if ($this->demand_obj->store() !== false) {
            PageLayout::postSuccess('The demand was
successfully saved');
        } else {
            PageLayout::postError('An error occurred while
saving the demand');
        }
        $demand_id = $this->demand_obj->id;

        $tags = explode(",", Request::get('tags'));
        TagDemand::updateTags($tags, $demand_id);

        $categories =  json_decode(Request::get('selected_categories'), true);
        CategoryDemand::set_category_demand($categories, $demand_id);

        $request = Request::getInstance();
        Property::update_custom_properties($request['custom_properties'], $demand_id);
        $this->redirect('overview/index/' . $marketplace_id);
    }
}
