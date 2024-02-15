<?php

require_once __DIR__ . '/models/demand.php';
require_once __DIR__ . '/models/property.php';
require_once __DIR__ . '/models/tag.php';
require_once __DIR__ . '/models/tag_demand.php';
require_once __DIR__ . '/models/custom_property.php';
require_once __DIR__ . '/classes/Controller.php';
require_once __DIR__ . '/classes/Plugin.php';
require_once __DIR__ . '/classes/Search.php';

class Marketplace extends StudIPPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();
        $root_nav = new Navigation(
            'Marketplace',
            PluginEngine::getURL($this, [], 'overview')
        );
        $root_nav->setImage(Icon::create(
            'file-text',
            Icon::ROLE_NAVIGATION
        ));
        Navigation::addItem('/marketplace_root', $root_nav);

        $default_marketplace = new Navigation(
            'Default marketplace',
            PluginEngine::getURL($this, [], 'overview')
        );
        $root_nav->addSubNavigation('default_marketplace', $default_marketplace);
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

        if ($GLOBALS['user']->perms === 'root') {
            $config_nav = new Navigation(
                'Config',
                PluginEngine::getURL($this, [], 'config')
            );
            $default_marketplace->addSubNavigation('marketplace_config', $config_nav);
        }
        $test = new Navigation(
            'Test',
            PluginEngine::getURL($this, [], 'overview/demand_detail/46c96adb568e8e00e8f9c4354ac52799')
        );
        $config_nav->addSubNavigation('marketplace_test', $test);
    }
}
