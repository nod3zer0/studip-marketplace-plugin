<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;
use User;

class Demand extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_demand';
        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['belongs_to']['mp_marketplace'] = [
            'class_name' => \Marketplace\MarketplaceModel::class,
            'foreign_key' => 'marketplace_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['has_many']['mp_tag_demand'] = [
            'class_name' => \Marketplace\TagDemand::class,
            'assoc_func' => 'findByParent_id'
        ];
        parent::configure($config);
    }

    public function hasPermission(): bool
    {
        return $GLOBALS['user']->id === $this->author_id || $GLOBALS['user']->perms === 'root';
    }
}
