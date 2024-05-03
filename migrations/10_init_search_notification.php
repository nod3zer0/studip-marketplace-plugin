<?php
/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitSearchNotification extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_search_notification (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            author_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            search_query TEXT NOT NULL,
            marketplace_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (marketplace_id) REFERENCES mp_marketplace(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_search_notification");
    }
}
