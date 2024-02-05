<?

use Studip\Button; ?>
<form class="default collapsable" action="<?= $controller->link_for('search/index', $demand_obj->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <div>
            <label class="required">
                search
            </label>
            <input name="search-query" required value="">
        </div>
    </fieldset>

    <div>
        <? foreach ($st as $row) : ?>
            <div>
                <div>
                    Title
                </div>
                <div><?= $row['title'] ?></div>
                <div><?= $row['name'] ?></div>
            </div>
        <? endforeach; ?>
    </div>

    <footer data-dialog-button>
        <?= Button::create('Submit') ?>
    </footer>
</form>