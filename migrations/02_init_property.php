<?php
class InitProperty extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_property (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            name TEXT NOT NULL,
            type TINYINT(2) NOT NULL DEFAULT 1,
            matching BOOL NOT NULL DEFAULT 1,
            value TEXT,
            PRIMARY KEY (id)
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_property");
    }
}
