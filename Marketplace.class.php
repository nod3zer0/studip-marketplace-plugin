<?php

require_once __DIR__ . '/models/demand.php';
require_once __DIR__ . '/models/property.php';
require_once __DIR__ . '/models/tag.php';
require_once __DIR__ . '/models/tag_demand.php';
require_once __DIR__ . '/models/custom_property.php';
require_once __DIR__ . '/models/marketplace.php';
require_once __DIR__ . '/classes/Controller.php';
require_once __DIR__ . '/classes/Plugin.php';
require_once __DIR__ . '/classes/Search.php';

use \Marketplace\MarketplaceModel;

class Marketplace extends StudIPPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        // $this->addScript('assets/autocomplete.js');

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
    }
}
