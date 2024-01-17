<?

use Studip\Button; ?>
<?= CSRFProtection::tokenTag() ?>
<fieldset data-open="bd_basicsettings">
    <div>
        <div>
            Title
        </div>
        <div><?= $demand_obj->title ?>"</div>
    </div>

    <div>
        <div>
            Description
        </div>
        <div><?= $demand_obj->description ?></div>
    </div>
</fieldset>