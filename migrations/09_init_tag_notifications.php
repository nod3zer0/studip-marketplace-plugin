<?php
class InitTagnotifications extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_tag_notifications (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            author_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            tag_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (tag_id) REFERENCES mp_tag(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_tag_notifications");
    }
}
