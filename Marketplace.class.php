<?php

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
require_once __DIR__ . '/classes/Controller.php';
require_once __DIR__ . '/classes/Plugin.php';
require_once __DIR__ . '/classes/Search.php';





use \Marketplace\MarketplaceModel;

class Marketplace extends StudIPPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        //$this->addScript('assets/bookmark_component.js');
        $this->addStylesheet('assets/stylesheet.css');
        // PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');
        $root_nav = new Navigation(
            'Marketplace',
            PluginEngine::getURL($this, [], 'marketplaces')
        );
        $root_nav->setImage(Icon::create(
            'file-text',
            Icon::ROLE_NAVIGATION
        ));
        Navigation::addItem('/marketplace_root', $root_nav);

        $marketplaces = new Navigation(
            'Marketplaces',
            PluginEngine::getURL($this, [], 'marketplaces')
        );
        $root_nav->addSubNavigation('marketplaces', $marketplaces);

        $my_global_bookmarks = new Navigation(
            'My bookmarks',
            PluginEngine::getURL($this, [], 'my_bookmarks/index/')
        );
        $root_nav->addSubNavigation('marketplace_my_bookmarks', $my_global_bookmarks);

        $user_config = new Navigation(
            'User config',
            PluginEngine::getURL($this, [], 'user_config')
        );
        $root_nav->addSubNavigation('user_config', $user_config);

        $default_marketplace = new Navigation(
            'Default marketplace',
            PluginEngine::getURL($this, [], 'overview')
        );
        Navigation::addItem('/default_marketplace', $default_marketplace);
        //$root_nav->addSubNavigation('default_marketplace', $default_marketplace);
        $overview = new Navigation(
            'Overview',
            PluginEngine::getURL($this, [], 'overview')
        );
        $default_marketplace->addSubNavigation('marketplace_overview', $overview);
        $search_nav = new Navigation(
            'Search',
            PluginEngine::getURL($this, [], 'search')
        );
        $default_marketplace->addSubNavigation('marketplace_search', $search_nav);
        $my_demands = new Navigation(
            'My demands',
            PluginEngine::getURL($this, [], 'my_demands')
        );
        $default_marketplace->addSubNavigation('marketplace_my_demands', $my_demands);


        // $global_search = new Navigation(
        //     'Search',
        //     PluginEngine::getURL($this, [], 'search')
        // );

        // $root_nav->addSubNavigation('global_search', $global_search);


        if ($GLOBALS['user']->perms === 'root') {
            $config_nav = new Navigation(
                'Config',
                PluginEngine::getURL($this, [], 'config')
            );
            $default_marketplace->addSubNavigation('marketplace_config', $config_nav);
            $global_config = new Navigation(
                'Config',
                PluginEngine::getURL($this, [], 'global_config')
            );
            $root_nav->addSubNavigation('global_config', $global_config);
        }

        $marketplaces = MarketplaceModel::findBySQL("1");

        foreach ($marketplaces as $marketplace) {
            $marketplace_nav = new Navigation(
                $marketplace->name,
                PluginEngine::getURL($this, [], 'overview/index/', []) . $marketplace->id
            );
            Navigation::addItem('/marketplace_' . $marketplace->id, $marketplace_nav);
            $marketplace_nav_item = new Navigation(
                'Overview',
                PluginEngine::getURL($this, [], 'overview/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_overview', $marketplace_nav_item);
            $search_nav = new Navigation(
                'Search',
                PluginEngine::getURL($this, [], 'search/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_search', $search_nav);
            $my_demands = new Navigation(
                'My demands',
                PluginEngine::getURL($this, [], 'my_demands/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_my_demands', $my_demands);
            $my_demands = new Navigation(
                'My bookmarks',
                PluginEngine::getURL($this, [], 'my_bookmarks/index/', []) . $marketplace->id
            );
            $marketplace_nav->addSubNavigation('marketplace_my_bookmarks', $my_demands);
            if ($GLOBALS['user']->perms === 'root') {
                $config_nav = new Navigation(
                    'Config',
                    PluginEngine::getURL($this, [], 'config/index/', []) . $marketplace->id
                );
                $marketplace_nav->addSubNavigation('marketplace_config', $config_nav);
                $categories_config = new Navigation(
                    'Categories',
                    PluginEngine::getURL($this, [], 'config/categories/', []) . $marketplace->id
                );
                $config_nav->addSubNavigation('categories', $categories_config);
                $properties_config = new Navigation(
                    'Properties',
                    PluginEngine::getURL($this, [], 'config/index/', []) . $marketplace->id
                );
                $config_nav->addSubNavigation('properties', $properties_config);
            }
        }
    }
}
