<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitDemand extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_demand (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            title TEXT NOT NULL,
            description TEXT NULL DEFAULT NULL,
            mkdate INT(11) NOT NULL,
            chdate INT(11) NOT NULL,
            author_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            marketplace_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            contact_mail NVARCHAR(255) NULL DEFAULT NULL,
            contact_name NVARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (id),
            FULLTEXT (title),
            FULLTEXT (description),
            FOREIGN KEY (marketplace_id) REFERENCES mp_marketplace(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_demand");
    }
}
