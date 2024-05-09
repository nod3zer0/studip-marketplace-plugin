<?php

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace Marketplace;

use SimpleORMap;
use User;

class Image extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'mp_image';
        $config['belongs_to']['mp_demand'] = [
            'class_name' => \Marketplace\Demand::class,
            'foreign_key' => 'demand_id',
            'assoc_foreign_key' => 'id'
        ];
        parent::configure($config);
    }

    /**
     * Stores the image in the database and the file system
     * @return true if the image was stored successfully, false otherwise
     */
    public static function storeImages($files, $demand_id): bool
    {
        for ($i = 0; $i < count($files['name']); $i++) {
            $image = new Image();
            $image->demand_id = $demand_id;
            $image->store();
            $info = pathinfo($files['name'][$i]);
            $ext = $info['extension']; // get the extension of the file
            $newname =  $image->id . "." . $ext;
            $image->filename =   $newname;
            $image->store();
            $target =  'plugins_packages/marketplace_data/user_data/images/' . $newname;

            // exif_imagetype is not allowed by default. Client side should be enough.
            // $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
            // $detectedType = \exif_imagetype($files['tmp_name']);
            // $correctType = in_array($detectedType, $allowedTypes);

            if (!move_uploaded_file($files['tmp_name'][$i], $target)) {
                $image->delete();

                return false;
            }
        }
        return true;
    }
    /**
     * Deletes the images from the database and the file system
     * @return void
     */
    public static function deleteImages($image_ids)
    {
        foreach ($image_ids as $image_id) {
            $image = Image::find($image_id);
            $target = 'plugins_packages/marketplace_data/user_data/images/' . $image->filename;
            if (file_exists($target)) {
                unlink($target);
            }
            $image->delete();
        }
    }
}
