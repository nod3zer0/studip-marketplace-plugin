<?php

use Marketplace\TagDemand;
use \Marketplace\CustomProperty;
use \Marketplace\Property;
use \Marketplace\Category;
use \Marketplace\CategoryDemand;
use \Marketplace\Tag;
use \Marketplace\Image;


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
        //Helpbar::get()->addPlainText("test", "test 2", 'icons/16/white/date.png'); https://github.com/nod3zer0/studip-docs-translated/blob/ba50f75faae1052d6c67a438c1c9d468f491944a/quickstart/helpbar.md
        Helpbar::get()->addPlainText("Overview", "Here are shown all the demands from the marketplace.");
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_overview/all');
        PageLayout::setTitle($marketplace_obj->name);
        OverviewController::buildSidebar($marketplace_id, $marketplace_obj->comodity_name_singular);
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');


        $this->marketplace_id = $marketplace_id;
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        //pagination
        $entries_per_page = get_config('ENTRIES_PER_PAGE');
        $page = Request::get('page') ?: 1;
        $this->page = $page;
        $this->marketplace_id = $marketplace_id;
        $this->number_of_demands = \Marketplace\Demand::countBymarketplace_id($marketplace_id);
        $this->pagination_url = 'overview/index/';


        //sorting
        //remap attributes to prevent sql injection
        $attribute_map = [
            'title' => 'title',
            'author' => 'auth_user_md5.username',
            'mkdate' => 'mkdate'
        ];
        $order_map = [
            'asc' => 'ASC',
            'desc' => 'DESC'
        ];
        $order = Request::get('order') ?: 'mkdate_desc';
        $this->order = $order;
        $order = explode('_', $order); // split into attribute and order



        $this->all_demands = \Marketplace\Demand::findBySQL("LEFT JOIN auth_user_md5 ON author_id = user_id WHERE marketplace_id = ? ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?,?", [$marketplace_id, ($page - 1) * $entries_per_page, $entries_per_page]);
    }

    public function delete_demands_action()
    {
        CSRFProtection::verifyRequest();
        if ($GLOBALS['user']->perms != 'root') {
            PageLayout::postError('You do not have permission to delete demands');
            $this->render_nothing();
            return;
        }


        $selected_demands = json_decode(file_get_contents('php://input'), true)["demand_ids"];
        foreach ($selected_demands as $demand_id) {
            $demand = \Marketplace\Demand::find($demand_id);
            $demand->delete();
        }
        PageLayout::postSuccess('The demands were successfully deleted');
        $this->render_text('');
    }

    public function demand_detail_action(string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        $this->avatar = Avatar::getAvatar($this->demand_obj->author_id);
        PageLayout::setTitle($this->demand_obj->title);
        $this->tags = \Marketplace\TagDemand::findBySQL("demand_id = ?", [$demand_id]);
        $db = DBManager::get();
        $this->images = Image::findBySQL("demand_id = ?", [$demand_id]);
        $this->properties = $db->fetchAll("SELECT * FROM mp_custom_property LEFT JOIN (SELECT value, demand_id, custom_property_id FROM mp_property WHERE mp_property.demand_id = ? ) t2 ON mp_custom_property.id = t2.custom_property_id WHERE mp_custom_property.marketplace_id = ? ORDER BY mp_custom_property.order_index", [$demand_id, $this->demand_obj->marketplace_id]);
        $this->selected_path = CategoryDemand::get_saved_path($demand_id);
    }

    public function create_demand_action(string $marketplace_id, string $demand_id = '')
    {
        PageLayout::setTitle('Edit ' . \Marketplace\MarketplaceModel::find($marketplace_id)->comodity_name_singular);
        $this->marketplace_id = $marketplace_id;
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        if (!$this->demand_obj) {
            $this->demand_obj = new \Marketplace\Demand();
        }

        $this->images = Image::findBySQL("demand_id = ?", [$demand_id]);

        //load all tags
        $tags =  Tag::findBySQL("1", []);

        $tags = array_map(function ($tag) {
            return [
                'name' => htmlReady($tag->name), // escape html
            ];
        }, $tags);
        $tags =  json_encode(["tags" => $tags]);
        //replace double quotes with single quotes, so it can be rendered in html
        $this->tags = str_replace("\"", "'", $tags);

        $picked_tags = \Marketplace\TagDemand::findBySQL("demand_id = ?", [$demand_id]);
        $picked_tags = array_map(function ($tag) {
            return [
                'name' => htmlReady($tag->mp_tag->name),
                'id' => $tag->mp_tag->id
            ];
        }, $picked_tags);

        $picked_tags = json_encode(["tags" => $picked_tags]);
        $this->picked_tags = str_replace("\"", "'", $picked_tags);


        $db = DBManager::get();
        $this->properties = $db->fetchAll("SELECT * FROM mp_custom_property LEFT JOIN (SELECT value, demand_id, custom_property_id FROM mp_property WHERE mp_property.demand_id = ? ) t2 ON mp_custom_property.id = t2.custom_property_id WHERE mp_custom_property.marketplace_id = ? ORDER BY mp_custom_property.order_index", [$demand_id, $marketplace_id]);

        $this->selected_path = CategoryDemand::get_saved_path($demand_id);
        $this->categories = json_encode(Category::get_categories($marketplace_id));
    }

    public function response_action($demand_id)
    {
        $this->demand_id = $demand_id;
    }

    public function send_response_action($demand_id)
    {
        $demand  = \Marketplace\Demand::find($demand_id);

        $mail = new StudipMail();
        $mail->addRecipient($demand->contact_mail)
            ->setReplyToEmail($GLOBALS['user']->email)
            ->setSubject('New response to your offer: ' . $demand->title)
            ->setBodyText("User " . $GLOBALS['user']->username . " has responded to your offer " . $this->url_for('overview/demand_detail/' . $demand_id, []) . ". Reply address is set to the sender. Response is below. \n\n" . Request::get('message'))
            ->send();


        PageLayout::postSuccess('The message was successfully sent.');
        $this->response->add_header('X-Dialog-Close', '1');
        $this->render_nothing();
    }


    public function store_demand_action(string $marketplace_id, string $demand_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->demand_obj = \Marketplace\Demand::find($demand_id);
        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
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
                PageLayout::postSuccess('The ' . $marketplace_obj->comodity_name_singular . ' was successfully deleted');
            } else {
                PageLayout::postError('An error occurred while deleting the ' . $marketplace_obj->comodity_name_singular);
                return;
            }
            $this->redirect('overview/index/' . $marketplace_id);
            return;
        }

        $this->demand_obj->setData([
            'title' => Request::get('title'),
            'description' => Studip\Markup::purifyHtml(Request::get('description')),
            'contact_mail' => Studip\Markup::purifyHtml(Request::get('contact_mail')),
            'contact_name' => Studip\Markup::purifyHtml(Request::get('contact_name'))
        ]);




        if ($this->demand_obj->store() !== false) {
            PageLayout::postSuccess('The ' . $marketplace_obj->comodity_name_singular . '  was
successfully saved');
        } else {
            PageLayout::postError('An error occurred while
saving the .' . $marketplace_obj->comodity_name_singular);
            return;
        }
        $demand_id = $this->demand_obj->id;

        if (image::storeImages($_FILES["images"], $demand_id) === false) {
            PageLayout::postError('An error occurred while saving the image');
            return;
        }
        $images_to_remove = Request::getArray('remove_images');
        if ($images_to_remove) {
            image::deleteImages($images_to_remove);
        }


        $tags = json_decode(Request::get("picked_tags"), true);
        TagDemand::updateTags($tags["tags"], $demand_id);

        $categories =  json_decode(Request::get('selected_categories'), true);
        CategoryDemand::set_category_demand($categories, $demand_id);

        $request = Request::getInstance();
        Property::update_custom_properties($request['custom_properties'], $demand_id);
        $this->response->add_header('X-Dialog-Close', '1');
        $this->render_nothing();
        //  $this->redirect('overview/index/' . $marketplace_id);
    }
}
