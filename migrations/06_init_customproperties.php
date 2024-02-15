<?php
class InitCustomproperties extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_custom_property (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            marketplace_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            name TEXT NOT NULL,
            type TINYINT(2) NOT NULL DEFAULT 1,
            required BOOL NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_custom_property");
    }
}
