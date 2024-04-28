<?php
// require_once __DIR__ . '/bootstrap.inc.php';
require_once __DIR__ . '/models/demand.php';
require_once __DIR__ . '/models/property.php';
require_once __DIR__ . '/models/tag.php';
require_once __DIR__ . '/models/tag_demand.php';
require_once __DIR__ . '/models/custom_property.php';
require_once __DIR__ . '/models/marketplace.php';
require_once __DIR__ . '/models/tag_notification.php';
require_once __DIR__ . '/models/bookmark.php';
require_once __DIR__ . '/models/search_notification.php';
require_once __DIR__ . '/models/search_demand.php';
require_once __DIR__ . '/models/category.php';
require_once __DIR__ . '/models/category_demand.php';
require_once __DIR__ . '/models/category_notification.php';
require_once __DIR__ . '/models/image.php';
require_once __DIR__ . '/classes/Controller.php';
require_once __DIR__ . '/classes/Plugin.php';
require_once __DIR__ . '/classes/Search.php';
require_once __DIR__ . '/classes/search/exceptions/SearchException.php';
require_once __DIR__ . '/classes/search/simpleSearch/SimpleSearch.php';
require_once __DIR__ . '/classes/search/advancedSearch/AdvancedSearch.php';
require_once __DIR__ . '/classes/StudIPSqlSearches/SimpleSearchStudIp.php';
require_once __DIR__ . '/classes/StudIPSqlSearches/CustomPropertySearchStudIp.php';
require_once __DIR__ . '/classes/StudIPSqlSearches/DefaultPropertySearchStudIp.php';





use \Marketplace\MarketplaceModel;

class Marketplace extends StudIPPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();
        //$this->addScript('assets/table_edit.js');
        //$this->addScript('assets/bookmark_component.js'); //does not work sometimes
        $this->addStylesheet('assets/stylesheet.css');
        // PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');
        $root_nav = new Navigation(
            'Catalog',
            PluginEngine::getURL($this, [], 'marketplaces')
        );
        $root_nav->setImage(Icon::create(
            'file-text',
            Icon::ROLE_NAVIGATION
        ));
        Navigation::addItem('/marketplace_root', $root_nav);

        $marketplaces = new Navigation(
            'Catalogs',
            PluginEngine::getURL($this, [], 'marketplaces')
        );
        $root_nav->addSubNavigation('marketplaces', $marketplaces);

        $my_global_bookmarks = new Navigation(
            'My bookmarks',
            PluginEngine::getURL($this, [], 'my_bookmarks/index/')
        );
        $root_nav->addSubNavigation('marketplace_my_bookmarks', $my_global_bookmarks);



        $subscriptions = new Navigation(
            'My subscriptions',
            PluginEngine::getURL($this, [], 'my_subscriptions/index/', [])
        );
        $root_nav->addSubNavigation('my_subscriptions', $subscriptions);

        $user_config = new Navigation(
            'Subscription settings',
            PluginEngine::getURL($this, [], 'user_config')
        );
        $root_nav->addSubNavigation('user_config', $user_config);

        // $default_marketplace = new Navigation(
        //     'Default marketplace',
        //     PluginEngine::getURL($this, [], 'overview')
        // );
        // Navigation::addItem('/default_marketplace', $default_marketplace);
        // //$root_nav->addSubNavigation('default_marketplace', $default_marketplace);
        // $overview = new Navigation(
        //     'Overview',
        //     PluginEngine::getURL($this, [], 'overview')
        // );
        // $default_marketplace->addSubNavigation('marketplace_overview', $overview);
        // $search_nav = new Navigation(
        //     'Search',
        //     PluginEngine::getURL($this, [], 'search')
        // );
        // $default_marketplace->addSubNavigation('marketplace_search', $search_nav);
        // $my_demands = new Navigation(
        //     'My demands',
        //     PluginEngine::getURL($this, [], 'my_demands')
        // );
        // $default_marketplace->addSubNavigation('marketplace_my_demands', $my_demands);


        // $global_search = new Navigation(
        //     'Search',
        //     PluginEngine::getURL($this, [], 'search')
        // );

        // $root_nav->addSubNavigation('global_search', $global_search);


        if ($GLOBALS['user']->perms === 'root') {
            // $config_nav = new Navigation(
            //     'Config',
            //     PluginEngine::getURL($this, [], 'config')
            // );
            // $default_marketplace->addSubNavigation('marketplace_config', $config_nav);
            $global_config = new Navigation(
                'Config',
                PluginEngine::getURL($this, [], 'global_config')
            );
            $root_nav->addSubNavigation('global_config', $global_config);
            $global_config_general = new Navigation(
                'General',
                PluginEngine::getURL($this, [], 'global_config')
            );
            $global_config->addSubNavigation('general', $global_config_general);
            $global_config_export = new Navigation(
                'Export/Import',
                PluginEngine::getURL($this, [], 'global_config/export')
            );
            $global_config->addSubNavigation('export', $global_config_export);
        }

        $marketplaces = MarketplaceModel::findBySQL("1");

        foreach ($marketplaces as $marketplace) {
            $marketplace_nav = new Navigation(
                $marketplace->name,
                PluginEngine::getURL($this, [], 'overview/index/', []) . $marketplace->id
            );
            Navigation::addItem('/marketplace_' . $marketplace->id, $marketplace_nav);

            $marketplaces = new Navigation(
                'Catalogs',
                PluginEngine::getURL($this, [], 'marketplaces')
            );
            $marketplace_nav->addSubNavigation('marketplaces', $marketplaces);

            $overview = new Navigation(
                'Overview',
                PluginEngine::getURL($this, [], 'overview/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_overview', $overview);
            $all = new Navigation(
                'All',
                PluginEngine::getURL($this, [], 'overview/index/', []) . $marketplace->id
            );
            $overview->addSubNavigation('all', $all);
            $subscriptions = new Navigation(
                'My subscriptions',
                PluginEngine::getURL($this, [], 'my_subscriptions/marketplace/', []) . $marketplace->id
            );
            $overview->addSubNavigation('my_subscriptions', $subscriptions);
            $my_demands = new Navigation(
                'My ' . $marketplace->comodity_name_plural,
                PluginEngine::getURL($this, [], 'my_demands/index/', []) . $marketplace->id
            );
            //$marketplace_nav->addSubNavigation('marketplace_my_demands', $my_demands);
            $overview->addSubNavigation('my_demands', $my_demands);
            $my_bookmarks = new Navigation(
                'My bookmarks',
                PluginEngine::getURL($this, [], 'my_bookmarks/index/', []) . $marketplace->id
            );
            $overview->addSubNavigation('my_bookmarks', $my_bookmarks);
            // $marketplace_nav->addSubNavigation('marketplace_my_bookmarks', $my_demands);



            $search_nav = new Navigation(
                'Search',
                PluginEngine::getURL($this, [], 'simple_search/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_search', $search_nav);
            $simple_search_nav = new Navigation(
                'Search',
                PluginEngine::getURL($this, [], 'simple_search/index/', []) . $marketplace->id
            );
            $search_nav->addSubNavigation('marketplace_simple_search', $simple_search_nav);
            $advanced_search_nav = new Navigation(
                'Advanced search',
                PluginEngine::getURL($this, [], 'advanced_search/index/', []) . $marketplace->id
            );
            $search_nav->addSubNavigation('marketplace_advanced_search', $advanced_search_nav);
            $advanced_search_plus_nav = new Navigation(
                'Advanced search plus',
                PluginEngine::getURL($this, [], 'search/index/', []) . $marketplace->id
            );
            $search_nav->addSubNavigation('marketplace_advanced_search_plus', $advanced_search_plus_nav);


            $user_config = new Navigation(
                'Subscription settings',
                PluginEngine::getURL($this, [], 'user_config/index/') . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('user_config', $user_config);


            if ($GLOBALS['user']->perms === 'root') {
                $config_nav = new Navigation(
                    'Config',
                    PluginEngine::getURL($this, [], 'config/index/', []) . $marketplace->id
                );
                $marketplace_nav->addSubNavigation('marketplace_config', $config_nav);
                $general_config = new Navigation(
                    'General',
                    PluginEngine::getURL($this, [], 'config/index/', []) . $marketplace->id
                );
                $config_nav->addSubNavigation('general', $general_config);
                $categories_config = new Navigation(
                    'Categories',
                    PluginEngine::getURL($this, [], 'config/categories/', []) . $marketplace->id
                );
                $config_nav->addSubNavigation('categories', $categories_config);
                $properties_config = new Navigation(
                    'Properties',
                    PluginEngine::getURL($this, [], 'config/properties/', []) . $marketplace->id
                );
                $config_nav->addSubNavigation('properties', $properties_config);
            }
        }
    }
}
