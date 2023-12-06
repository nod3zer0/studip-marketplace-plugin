<?php
class InitTest extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $query = "CREATE TABLE IF NOT EXISTS tp_texte (
    text_id CHAR(32) CHARACTER SET latin1 COLLATE
    latin1_bin NOT NULL,
    title TEXT NOT NULL,
    description TEXT NULL DEFAULT NULL,
    type TINYINT(2) NOT NULL DEFAULT 1,
    mkdate INT(11) NOT NULL,
    chdate INT(11) NOT NULL,
    author_id CHAR(32) CHARACTER SET latin1 COLLATE
    latin1_bin NOT NULL,
    PRIMARY KEY (text_id)
    )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS tp_texte");
    }
}
