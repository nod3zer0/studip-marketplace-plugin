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

    public function save_config_action(){
        $this->render_text('' . file_get_contents('php://input'));
    }

}
