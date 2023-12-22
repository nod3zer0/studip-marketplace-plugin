<?

use Studip\Button; ?>
<form class="default collapsable" action="<?= $controller->link_for('overview/store_demand', $demand_obj->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <legend>
            IDK what is this 1
        </legend>

        <div>
            <label class="required">
                Title
            </label>
            <input name="title" required value="<?= $demand_obj->title ?>">
        </div>

        <div>
            <label>
                Description
            </label>
            <textarea name="description"><?= $demand_obj->description ?></textarea>
        </div>

    </fieldset>

    <footer data-dialog-button>
        <?= Button::create('Submit') ?>
    </footer>
</form>