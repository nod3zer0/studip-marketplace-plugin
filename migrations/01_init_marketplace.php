<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitMarketplace extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_marketplace (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            name TEXT NOT NULL,
            enabled BOOL NOT NULL DEFAULT 1,
            comodity_name_singular TEXT NOT NULL,
            comodity_name_plural TEXT NOT NULL,
            PRIMARY KEY (id)
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_marketplace");
    }
}
