<?php

namespace Marketplace;

use SimpleORMap;

class MarketplaceModel extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_marketplace';
        parent::configure($config);
    }

    public function update_marketplaces($new_marketplaces)
    {
        $old_marketplaces = MarketplaceModel::findBySQL("1");
        $new_marketplaces_obj = [];
        $i = 0;
        foreach ($new_marketplaces as $marketplace) {
            $new_marketplaces_obj[$i] = new MarketplaceModel();
            $new_marketplaces_obj[$i]->name = $marketplace["name"];
            $new_marketplaces_obj[$i]->enabled = $marketplace["enabled"];
            $new_marketplaces_obj[$i]->id = $marketplace["id"];
            $i++;
        }

        $to_delete = array_udiff($old_marketplaces, $new_marketplaces_obj, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_insert = array_udiff($new_marketplaces_obj, $old_marketplaces, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_update = array_uintersect($new_marketplaces_obj, $old_marketplaces, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });

        foreach ($to_delete as $marketplace) {
            MarketplaceModel::find($marketplace->id)->delete();
        }
        foreach ($to_insert as $marketplace) {
            $marketplace_to_insert = new MarketplaceModel();
            $marketplace_to_insert->name = $marketplace->name;
            $marketplace_to_insert->enabled = $marketplace->enabled;
            $marketplace_to_insert->store();
        }
        foreach ($to_update as $marketplace) {
            $old_marketplace = MarketplaceModel::find($marketplace->id);
            $old_marketplace->name = $marketplace->name;
            $old_marketplace->enabled = $marketplace->enabled;
            $old_marketplace->store();
        }
    }
}
