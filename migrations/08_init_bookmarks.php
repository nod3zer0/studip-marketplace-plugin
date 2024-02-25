<?php
class InitBookmarks extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_bookmark (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            author_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (demand_id) REFERENCES mp_demand(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }



    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_bookmark");
    }
}
