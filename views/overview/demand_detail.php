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
    <div>
        <div>
            Tags
        </div>
        <div>
            <? foreach ($tags as $tag) : ?>
                <div><?= $tag->mp_tag->name ?></div>
            <? endforeach; ?>
        </div>

</fieldset>