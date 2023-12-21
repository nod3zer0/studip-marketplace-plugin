<?php
class InitTest extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS demand (
            id CHAR(32),
            title TEXT NOT NULL,
            description TEXT NULL DEFAULT NULL,
            mkdate INT(11) NOT NULL,
            chdate INT(11) NOT NULL,
            author_id CHAR(32),
            PRIMARY KEY (id)
            );
        CREATE TABLE IF NOT EXISTS property (
            id CHAR(32),
            name TEXT NOT NULL,
            type TINYINT(2) NOT NULL DEFAULT 1,
            matching BOOL NOT NULL DEFAULT 1,
            demand_id CHAR(32),
            value TEXT,
            PRIMARY KEY (id)
            CONSTRAINT `fk_property_demand_id`
                FOREIGN KEY (demand_id) REFERENCES demand (id)
                ON DELETE CASCADE
            )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS demand; DROP TABLE IF EXISTS property;");
    }
}
