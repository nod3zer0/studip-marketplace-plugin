<?php

use Marketplace\SearchException;
use Marketplace\TagDemand;
use Marketplace\SqlGenerator;
use \Marketplace\TagNotification;
use \Marketplace\Tag;

class UserConfigController extends \Marketplace\Controller
{

    public function index_action()
    {
        Navigation::activateItem('marketplace_root/user_config');
        PageLayout::setTitle('Configuration');
        PageLayout::addScript($this->plugin->getPluginURL() . '/assets/notifications_tags.js');
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

    public function set_tags_action()
    {
        $tags = json_decode(file_get_contents('php://input'), true);
        TagNotification::setSubscribedTags($GLOBALS['user']->id, $tags["tags"]);
        $this->render_text('' .  print_r($tags));
    }
}
