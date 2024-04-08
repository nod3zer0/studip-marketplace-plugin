<?php

use Marketplace\TagDemand;
use \Marketplace\CustomProperty;
use \Marketplace\Property;
use \Marketplace\Category;
use \Marketplace\CategoryDemand;


class MySubscriptionsController extends \Marketplace\Controller
{
    private function buildSidebarMarketplace(string $marketplace_id, string $comodity_name_singular)
    {
        $sidebar = Sidebar::Get();
        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            'Create ' . $comodity_name_singular,
            $this->url_for('overview/create_demand/', []) . $marketplace_id,
            Icon::create('add'),
            ['data-dialog' => true]
        );
        $navigationWidget = $sidebar->addWidget(new NavigationWidget());
        $navigationWidget->addLink(
            'Suscription settings ',
            $this->url_for('user_config/index/', []) . $marketplace_id,
            Icon::create('settings2'),
            ['data-dialog' => false]
        );
    }

    private function buildSidebarIndex()
    {
        $sidebar = Sidebar::Get();
        $navigationWidget = $sidebar->addWidget(new NavigationWidget());
        $navigationWidget->addLink(
            'Suscription settings ',
            $this->url_for('user_config/index/', []),
            Icon::create('settings2'),
            ['data-dialog' => false]
        );
    }

    public function index_action()
    {
        Helpbar::get()->addPlainText("Subscriptions", "Here are shown all the demands from your subscribed tags and categories. These can be managed in user settings.");
        Navigation::activateItem('marketplace_root/my_subscriptions');
        PageLayout::setTitle("My Subscriptions");

        self::buildSidebarIndex();
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;

        //pagination
        $entries_per_page = get_config('ENTRIES_PER_PAGE');
        $page = Request::get('page') ?: 1;
        $this->page = $page;
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



        $this->all_demands =     $this->all_demands = \Marketplace\Demand::findBySQL("LEFT JOIN auth_user_md5 ON author_id = user_id LEFT JOIN mp_category_demand ON mp_demand.id = mp_category_demand.demand_id LEFT JOIN mp_tag_demand ON mp_demand.id = mp_tag_demand.demand_id
        WHERE
        ( mp_tag_demand.tag_id in (
            SELECT tag_id FROM mp_tag_notification WHERE author_id = ?
        )
        OR
        mp_category_demand.category_id in (
            SELECT category_id FROM mp_category_notification WHERE author_id = ?
        )
        )GROUP BY mp_demand.title ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?,?", [$GLOBALS['user']->id, $GLOBALS['user']->id, ($page - 1) * $entries_per_page, $entries_per_page]);
    }

    public function marketplace_action(string $marketplace_id)
    {

        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_overview/my_subscriptions');
        PageLayout::setTitle($marketplace_obj->name);
        self::buildSidebarMarketplace($marketplace_id, $marketplace_obj->comodity_name_singular);

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



        $this->all_demands = \Marketplace\Demand::findBySQL("LEFT JOIN auth_user_md5 ON author_id = user_id LEFT JOIN mp_category_demand ON mp_demand.id = mp_category_demand.demand_id LEFT JOIN mp_tag_demand ON mp_demand.id = mp_tag_demand.demand_id WHERE marketplace_id = ?
        AND
        ( mp_tag_demand.tag_id in (
            SELECT tag_id FROM mp_tag_notification WHERE author_id = ?
        )
        OR
        mp_category_demand.category_id in (
            SELECT category_id FROM mp_category_notification WHERE author_id = ?
        )
        )GROUP BY mp_demand.title ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?,?", [$marketplace_id, $GLOBALS['user']->id, $GLOBALS['user']->id, ($page - 1) * $entries_per_page, $entries_per_page]);
    }
}
