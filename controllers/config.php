<?php

use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
class ConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/marketplace_config');
        PageLayout::setTitle('Configuration');
    }

}
