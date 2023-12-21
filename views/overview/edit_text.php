<?

use Studip\Button; ?>
<form class="default collapsable" action="<?= $controller->link_for('overview/store_text', $text_obj->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <legend>
            Grunddaten
        </legend>

        <div>
            <label class="required">
                Titel
            </label>
            <input name="title" required value="<?= $text_obj->title ?>">
        </div>

        <div>
            <label>
                Beschreibung
            </label>
            <textarea name="description"><?= $text_obj->description ?></textarea>
        </div>

        <div>
            <label class="required">
                Text Typ
            </label>
            <select name="type" required>
                <? foreach (\TestPlugin\Test::getTypes() as $type_key => $type_label) : ?>
                    <option value="<?= $type_key ?>" <? if ($type_key == $text_obj->type) echo 'selected' ?>>
                        <?= $type_label ?>
                    </option>
                <? endforeach; ?>
            </select>
        </div>

    </fieldset>

    <footer data-dialog-button>
        <?= Button::create('Submit') ?>
    </footer>
</form>