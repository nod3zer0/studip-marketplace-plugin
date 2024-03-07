<?php
class InitCategory extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_category (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            parent_category_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin,
            name TEXT NOT NULL,
            marketplace_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (parent_category_id) REFERENCES mp_category(id) ON DELETE CASCADE,
            FOREIGN KEY (marketplace_id) REFERENCES mp_marketplace(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_category");
    }
}
