<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\AdvancedSearch;
use \Marketplace\SearchNotification;
use \Marketplace\Category;
use \Marketplace\MarketplaceModel;
use \Marketplace\CustomProperty;
use \Marketplace\Tag;

class AdvancedSearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {

        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_tag_select.js');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_category_select.js');
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');

        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/marketplace_advanced_search');
        PageLayout::setTitle(MarketplaceModel::find($marketplace_id)->name);
        $this->marketplace_id = $marketplace_id;
        $categories = Category::get_categories($marketplace_id);
        $this->categories = json_encode($categories);
        $this->properties = CustomProperty::findBySQL("marketplace_id = ?", [$marketplace_id]);
        $this->tags = Tag::get_all_tags_csv();
        //$query = Request::get('search-query');
        $request_data = Request::getInstance();
        // foreach ($this->properties as $property) {
        //     $properties[] =  Request::get(str_replace(" ", "_", $property->name));
        // }
        $this->custom_property_data = $this->get_custom_property_data($request_data, $this->properties);
        $this->tag_data = $this->get_tag_data($request_data, Tag::get_all_tags());
        $this->default_property_data = $this->get_default_property_data($request_data);
        $this->selected_tags = $request_data["selected_tags"];
        $this->selected_categories = $request_data["selected_categories"];
        $advanced_search = new AdvancedSearch();
        $sql = $advanced_search->generateSQL($this->custom_property_data, $this->tag_data, $this->default_property_data, $this->selected_categories,   $categories, $marketplace_id);
        $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
    }

    public function get_default_property_data($RequestData)
    {
        $data = [];
        if ($RequestData["title"]["value"] != "") {
            $title = [
                "name" => "title",
                "type" => 1,
                "compare_type" => $RequestData["title"]["compare_type"],
                "value" => $RequestData["title"]["value"]
            ];
            $data["title"] = $title;
        }
        if ($RequestData["description"]["value"] != "") {
            $description = [
                "name" => "description",
                "type" => 5,
                "compare_type" => $RequestData["description"]["compare_type"],
                "value" => $RequestData["description"]["value"]
            ];
            $data["description"] = $description;
        }
        if ($RequestData["created"]["value"] != "") {
            $created = [
                "name" => "created",
                "type" => 3,
                "compare_type" => $RequestData["created"]["compare_type"],
                "value" => $RequestData["created"]["value"],
                "value_from" => $RequestData["created"]["range_value_from"],
                "value_to" => $RequestData["created"]["range_value_to"]
            ];
            $data["created"] = $created;
        }

        return $data;
    }

    public function get_custom_property_data($RequestData, $properties)
    {

        $custom_property_data = [];
        foreach ($properties as $property) {
            $filledProperty = $RequestData[str_replace(" ", "_", $property->name)];
            if ($filledProperty["value"] == "") {
                continue;
            }
            $name = $property->name;

            $type = $property->type;
            $compare_type = $filledProperty["compare_type"];
            if ($compare_type == "range") {
                $value_from = $filledProperty["range_value_from"];
                $value_to = $filledProperty["range_value_to"];
            } else {
                $value = $filledProperty["value"];
            }
            $id = $property->id;
            $custom_property_data[$name] = [
                "name" => $name,
                "type" => $type,
                "compare_type" => $compare_type,
                "value" => $value,
                "value_from" => $value_from,
                "value_to" => $value_to,
                "id" => $id
            ];
        }
        return $custom_property_data;
    }

    public function get_tag_data($RequestData, $tags)
    {

        $split_tags = explode(",", $RequestData["selected_tags"]);

        $tag_data = [];

        foreach ($tags as $tag) {
            $name = "";
            if (in_array($tag->name, $split_tags)) {
                $name = $tag->name;
            }
            if ($name == "") {
                continue;
            }
            $tag_data[] = [
                "name" => $name,
                "id" => $tag->id
            ];
        }

        return $tag_data;
    }
}
