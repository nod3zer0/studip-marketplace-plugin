<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitDemandproperty extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS mp_demand_property (
            id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            property_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            demand_id CHAR(32) CHARACTER SET latin1 COLLATE
            latin1_bin NOT NULL,
            PRIMARY KEY (id)
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS mp_demand_property");
    }
}
