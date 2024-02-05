<?php

require_once __DIR__ . '/models/demand.php';
require_once __DIR__ . '/models/property.php';
require_once __DIR__ . '/models/tag.php';
require_once __DIR__ . '/models/tag_demand.php';
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
        $navigation = new Navigation(
            'Overview',
            PluginEngine::getURL($this, [], 'overview')
        );
        $root_nav->addSubNavigation('marketplace_overview', $navigation);
        $search_nav = new Navigation(
            'Search',
            PluginEngine::getURL($this, [], 'search')
        );
        $root_nav->addSubNavigation('marketplace_search', $search_nav);
        $search_nav = new Navigation(
            'Config',
            PluginEngine::getURL($this, [], 'config')
        );
        $root_nav->addSubNavigation('marketplace_config', $search_nav);
    }
}
