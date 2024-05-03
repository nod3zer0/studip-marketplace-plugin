<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitTagdemand extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_tag_demand (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            tag_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (demand_id) REFERENCES mp_demand(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES mp_tag(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_tag_demand");
    }
}
