<?php

class InitUserData extends Migration
{
    public function up()
    {
        if (!mkdir("plugins_packages/marketplace_data/user_data/images/", 0777, true)) {
            PageLayout::postError("Could not create directory" . getcwd());
        }

        if (!mkdir("plugins_packages/marketplace_data/marketplace/user_data/files/", 0777, true)) {
            PageLayout::postError("Could not create directory" . getcwd());
        }
    }

    public function down()
    {
        self::removeDirectory("plugins_packages/marketplace_data");
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
