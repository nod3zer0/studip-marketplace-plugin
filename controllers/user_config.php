<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\TagNotification;
use \Marketplace\Tag;
use \Marketplace\SearchNotification;

class UserConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/user_config');
        PageLayout::setTitle('Configuration');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/notifications_tags.js');

        //load all tags
        $tags =  Tag::findBySQL("1", []);

        $tags = array_map(function ($tag) {
            return [
                'name' => htmlReady($tag->name), // escape html
                'id' => $tag->id
            ];
        }, $tags);


        $tags =  json_encode(["tags" => $tags]);

        //replace double quotes with single quotes, so it can be rendered in html
        $this->tags = str_replace("\"", "'", $tags);


        //load subscribed tags
        $picked_tags = TagNotification::getSubscribedTags($GLOBALS['user']->id);
        $picked_tags = array_map(function ($tag) {
            return [
                'name' => htmlReady($tag->mp_tag->name),
                'id' => $tag->mp_tag->id
            ];
        }, $picked_tags);

        $picked_tags = json_encode(["tags" => $picked_tags]);

        $this->picked_tags = str_replace("\"", "'", $picked_tags);
    }

    public function get_tags_action()
    {
        $tags = Tag::findBySQL("1", []);

        //remap to tags.name tags.id
        $tags = array_map(function ($tag) {
            return [
                'name' => $tag->name,
                'id' => $tag->id
            ];
        }, $tags);

        $this->render_text('' . json_encode(["tags" => $tags]));
    }

    public function get_subscribed_tags_action()
    {
        $tags = TagNotification::getSubscribedTags($GLOBALS['user']->id);
        $tags = array_map(function ($tag) {
            return [
                'name' => $tag->mp_tag->name,
                'id' => $tag->mp_tag->id
            ];
        }, $tags);
        $this->render_text('' . json_encode(["tags" => $tags]));
    }

    public function save_user_config_action()
    {
        //save tag configuration
        $tags = json_decode(Request::get("picked_tags"), true);
        TagNotification::setSubscribedTags($GLOBALS['user']->id, $tags["tags"]);
        PageLayout::postSuccess('Configuration saved');
        $this->redirect('user_config/index');
    }

    public function set_tags_action()
    {
        $tags = json_decode(file_get_contents('php://input'), true);
        TagNotification::setSubscribedTags($GLOBALS['user']->id, $tags["tags"]);
        $this->render_text('' .  print_r($tags));
    }

    public function get_search_notifications_action()
    {
        $notifications = SearchNotification::getSubscribedSearches($GLOBALS['user']->id);

        //remap to other format
        $remapped_notifications = [];
        foreach ($notifications as $item) {
            $marketplace = $item->mp_marketplace->name;
            $marketplaceId = $item->mp_marketplace->id;

            // Create a new search query entry
            $searchQuery = [
                'query' => $item->search_query,
                'id' => $item->id,
            ];

            // Check if the marketplace already exists in the converted data
            if (isset($remapped_notifications[$marketplaceId])) {
                // If it does, add the search query to the existing marketplace
                $remapped_notifications[$marketplaceId]['queries'][] = $searchQuery;
            } else {
                // If not, create a new marketplace entry
                $remapped_notifications[$marketplaceId] = [
                    'marketplace' => $marketplace,
                    'id' => $marketplaceId,
                    'queries' => [$searchQuery]
                ];
            }
        }

        $this->render_text('' . json_encode(["notifications" => array_values($remapped_notifications)]));
    }
    public function set_search_notifications_action()
    {
        $notifications = json_decode(file_get_contents('php://input'), true);

        $remapped_notifications = [];
        foreach ($notifications["notifications"] as $item) {
            foreach ($item["queries"] as $query) {
                $remapped_notifications[] = $query["query"];
            }
        }

        SearchNotification::setSearchNotifications($remapped_notifications, $GLOBALS['user']->id);
        $this->render_text('');
    }
}
