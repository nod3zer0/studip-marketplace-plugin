<?php


require_once __DIR__ . '/models/test.php';
require_once __DIR__ . '/classes/Controller.php';
require_once __DIR__ . '/classes/Plugin.php';

class TestPlugin extends StudIPPlugin implements SystemPlugin
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
        Navigation::addItem('/test_root', $root_nav);
        $navigation = new Navigation(
            'Overview',
            PluginEngine::getURL($this, [], 'overview')
        );
        $root_nav->addSubNavigation('test_overview', $navigation);
    }
}
