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
        $old_properties = \Marketplace\CustomProperty::findBySQL('',[]);
        $new_properties = json_decode(file_get_contents('php://input'), true);




        $this->render_text('' . file_get_contents('php://input'));
    }

}
