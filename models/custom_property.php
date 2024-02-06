<?php

namespace Marketplace;

use SimpleORMap;

class CustomProperty extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_custom_property';
        parent::configure($config);
    }
}
