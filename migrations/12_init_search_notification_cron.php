<?php

/**
 * Unused migration planned for search notifications
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

require_once __DIR__ . '/../classes/SearchNotificationCron.php';

use \Marketplace\SearchNotificationCron;

class InitSearchNotificationCron extends Migration
{
    public function up()
    {
        SearchNotificationCron::register()->schedulePeriodic(59, 23)->activate();
    }

    public function down()
    {
        SearchNotificationCron::unregister();
    }
}
