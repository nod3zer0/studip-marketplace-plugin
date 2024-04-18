<?php

class InitUserData extends Migration
{
    public function up()
    {
        mkdir("marketplace/user_data/images/", 0777, true);
        mkdir("marketplace/user_data/files/", 0777, true);
    }

    public function down()
    {
        self::removeDirectory("marketplace");
    }

    public function removeDirectory($path)
    {

        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? self::removeDirectory($file) : unlink($file);
        }
        rmdir($path);

        return;
    }
}
