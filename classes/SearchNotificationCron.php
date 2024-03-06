<?php

namespace Marketplace;

class SearchNotificationCron extends \CronJob
{
    public static function getName()
    {
        return _('Search Notification');
    }

    public static function getDescription()
    {
        return _('Notifies all users when a new search result is available');
    }

    public static function getParameters()
    {
        return [
            'verbose' => [
                'type' => 'boolean',
                'default' => false,
                'status' => 'optional',
                'description' => '',
            ],
        ];
    }

    public function setUp()
    {
    }

    public function execute($last_result, $parameters = [])
    {
        do_something();
    }

    public function tearDown()
    {
    }
}
