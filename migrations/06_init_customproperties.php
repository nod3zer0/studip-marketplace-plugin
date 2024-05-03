<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
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
            order_index TINYINT(2) NOT NULL DEFAULT 0,
            required BOOL NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            FOREIGN KEY (marketplace_id) REFERENCES mp_marketplace(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_custom_property");
    }
}
