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

    public function get_property_by_name($name): CustomProperty
    {
        return CustomProperty::findOneBySQL("name = ?", [$name]);
    }

    public function update_properties($new_properties, $marketplace_id)
    {
        $old_properties = CustomProperty::findBySQL("marketplace_id = ?", [$marketplace_id]);
        $new_properties_obj = [];
        $i = 0;
        foreach ($new_properties as $property) {
            $new_properties_obj[$i] = new CustomProperty();
            $new_properties_obj[$i]->name = $property["name"];
            $new_properties_obj[$i]->type = $property["type"];
            $new_properties_obj[$i]->required = $property["required"];
            $new_properties_obj[$i]->id = $property["id"];
            $new_properties_obj[$i]->marketplace_id = $marketplace_id;
            $i++;
        }

        $to_delete = array_udiff($old_properties, $new_properties_obj, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_insert = array_udiff($new_properties_obj, $old_properties, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });
        $to_update = array_uintersect($new_properties_obj, $old_properties, function ($a, $b) {
            return strcmp($a->id, $b->id);
        });

        foreach ($to_delete as $property) {
            CustomProperty::find($property->id)->delete();
        }
        foreach ($to_insert as $property) {
            $property_to_insert = new CustomProperty();
            $property_to_insert->name = $property->name;
            $property_to_insert->type = $property->type;
            $property_to_insert->required = $property->required;
            $property_to_insert->marketplace_id = $marketplace_id;
            $property_to_insert->store();
        }
        foreach ($to_update as $property) {
            $old_property = CustomProperty::find($property->id);
            $old_property->name = $property->name;
            $old_property->type = $property->type;
            $old_property->required = $property->required;
            $old_property->store();
        }
    }
}
