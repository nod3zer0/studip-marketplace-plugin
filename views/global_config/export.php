<?

use Studip\LinkButton;
use Studip\Button;
?>

<?= LinkButton::create('Export user generated data', $controller->link_for('global_config/export_data'), []); ?>
<?= LinkButton::create('Export users', $controller->link_for('global_config/export_users'), []); ?>


<form method="post" action="<?= $controller->link_for('global_config/import_data') ?>" enctype="multipart/form-data">

    <input name="backup" type="file">
    <label class="file-upload">


        Restore user generated data (local data will be lost).
    </label>
    <?= Button::create('Upload', 'submit_btn') ?>
</form>