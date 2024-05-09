<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;

class Property extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_property';
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        $config['belongs_to']['mp_custom_property'] = [
            'class_name' => \Marketplace\CustomProperty::class,
            'foreign_key' => 'custom_property_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }
    /**
     * Get all properties for a given demand
     * @param $demand_id
     * @return array
     */
    public function findByDemandId($demand_id)
    {
        return self::findBySQL("demand_id = ?", [$demand_id]);
    }

    /**
     * Set custom properties for a given demand
     * @param $new_properties
     * @param $demand_id
     * @return void
     */
    public function update_custom_properties($new_properties, $demand_id)
    {
        $old_properties = self::findBySQL("demand_id = ?", [$demand_id]);
        $new_properties_obj = [];
        $i = 0;
        foreach ($new_properties as $key =>  $property) {
            $new_properties_obj[$i] = new Property();
            $new_properties_obj[$i]->value = $property;
            $new_properties_obj[$i]->demand_id = $demand_id;
            $new_properties_obj[$i]->custom_property_id = $key;
            $i++;
        }

        $to_insert = array_udiff($new_properties_obj, $old_properties, function ($a, $b) {
            return strcmp($a->custom_property_id, $b->custom_property_id);
        });
        $to_update = array_uintersect($new_properties_obj, $old_properties, function ($a, $b) {
            return strcmp($a->custom_property_id, $b->custom_property_id);
        });

        foreach ($to_insert as $property) {
            $property->store();
        }
        foreach ($to_update as $property) {
            $old_property = Property::findOneBySQL("demand_id = ? AND custom_property_id = ?", [$demand_id, $property->custom_property_id]);
            $old_property->value = $property->value;
            $old_property->store();
        }
    }
}
