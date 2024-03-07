<?php
class InitCategoryDemand extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_category_demand (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            category_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin,
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin,
            PRIMARY KEY (id),
            FOREIGN KEY (category_id) REFERENCES mp_category(id) ON DELETE CASCADE,
            FOREIGN KEY (demand_id) REFERENCES mp_demand(id) ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_category_demand");
    }
}
