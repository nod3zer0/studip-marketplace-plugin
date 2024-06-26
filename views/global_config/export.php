<?

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

use Studip\LinkButton;
use Studip\Button;
?>

<?= LinkButton::create('Export user generated data', $controller->link_for('global_config/export_data'), []); ?>
<?= LinkButton::create('Export users', $controller->link_for('global_config/export_users'), []); ?>


<form class="default" method="post" action="<?= $controller->link_for('global_config/import_data') ?>" enctype="multipart/form-data">


    <label class="file-upload">
        <input name="backup" type="file">

        Restore user generated data (local data will be lost).
    </label>
    <button class="button" name="submit_btn" data-confirm @click="deleteUnusedTags">Upload</button>
</form>