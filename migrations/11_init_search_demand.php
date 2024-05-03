<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitSearchDemand extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_search_demand (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            search_notification_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (demand_id) REFERENCES mp_demand(id) ON DELETE CASCADE,
            FOREIGN KEY (search_notification_id) REFERENCES mp_search_notification(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_search_demand");
    }
}
