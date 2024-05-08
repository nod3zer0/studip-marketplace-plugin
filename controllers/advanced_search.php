<?php

/**
 * Controller for advanced search
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */





use Marketplace\SearchException;
use Marketplace\TagDemand;
use \Marketplace\SearchNotification;
use \Marketplace\Category;
use \Marketplace\MarketplaceModel;
use \Marketplace\CustomProperty;
use \Marketplace\Tag;
use \search\AdvancedSearch;

class AdvancedSearchController extends \Marketplace\Controller
{

    public function index_action($marketplace_id = '')
    {
        Helpbar::get()->addPlainText("Searching", "This search allows you to search for demands based on custom properties, tags, and categories.");
        Helpbar::get()->addPlainText("Text properties", "Specified query is searched inside property as a whole. Partial words can be searched by adding * (asterisk) in the word (eg. example*, exa*ple, etc...).");
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_tag_select.js');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/search_category_select.js');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/bookmark_component.js');
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/assets/stylesheet.css');

        Navigation::activateItem('marketplace_' . $marketplace_id . '/marketplace_search/advanced_search');
        PageLayout::setTitle(MarketplaceModel::find($marketplace_id)->name);

        $marketplace_obj = \Marketplace\MarketplaceModel::find($marketplace_id);
        $this->marketplace_comodity_name_plural = $marketplace_obj->comodity_name_plural;
        $this->marketplace_id = $marketplace_id;
        $categories = Category::get_categories($marketplace_id);
        $this->categories = json_encode($categories);
        $this->properties = CustomProperty::findBySQL("marketplace_id = ?", [$marketplace_id]);
        $this->tags = Tag::get_all_tags_csv();

        $request_data = Request::getInstance();
        $this->custom_property_data = $this->get_custom_property_data($request_data, $this->properties);
        $this->tag_data = $this->get_tag_data($request_data, Tag::get_all_tags());
        $this->default_property_data = $this->get_default_property_data($request_data);
        $this->selected_tags = $request_data["selected_tags"];
        $this->selected_categories = $request_data["selected_categories"];
        $advanced_search = new AdvancedSearch();
        $this->limit = Request::get('limit') ?: get_config('ENTRIES_PER_PAGE');

        $this->order = Request::get('order') ?: 'mkdate_desc';
        $sql = $advanced_search->generateSQL($this->custom_property_data, $this->tag_data, $this->default_property_data, $this->selected_categories,   $categories, $marketplace_id, intval($this->limit), $this->order);
        $this->all_demands = \Marketplace\Demand::findBySQL($sql[0], $sql[1]);
    }

    /**
     * Converts data from request to format used by advanced search
     * @param $RequestData data from request
     */
    public function get_default_property_data($RequestData)
    {
        $data = [];
        if ($RequestData["title_parameter"]["value"] != "") {
            $title = [
                "name" => "title",
                "type" => 1,
                "compare_type" => $RequestData["title_parameter"]["compare_type"],
                "value" => $RequestData["title_parameter"]["value"]
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
        if ($RequestData["author_parameter"]["value"] != "") {
            $author = [
                "name" => "author",
                "type" => 6,
                "compare_type" => $RequestData["author_parameter"]["compare_type"],
                "value" => $RequestData["author_parameter"]["value"]
            ];
            $data["author"] = $author;
        }

        return $data;
    }

    /**
     * Converts data from request to format used by advanced search
     * @param $RequestData data from request
     */
    public function get_custom_property_data($RequestData, $properties)
    {

        $custom_property_data = [];
        foreach ($properties as $property) {
            if ($property->type == 5 || $property->type == 1) { //properties with autocomplete end with _parameter (because of use of inbuilt autocomplete component from Stud.IP)
                $filledProperty = $RequestData[str_replace(" ", "_", $property->name . "_parameter")];
            } else {
                $filledProperty = $RequestData[str_replace(" ", "_", $property->name)];
            }

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

    /**
     * Converts data from request to format used by advanced search
     * @param $RequestData data from request
     * @param $tags all tags
     */
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
