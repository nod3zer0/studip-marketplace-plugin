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
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            value TEXT,
            PRIMARY KEY (id),
            CONSTRAINT FK_DemandProperty FOREIGN KEY (demand_id)
            REFERENCES mp_demand(id)
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DDROP TABLE IF EXISTS mp_property");
    }
}
