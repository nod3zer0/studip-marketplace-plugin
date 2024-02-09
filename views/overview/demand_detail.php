<?

use Studip\Button; ?>
<?= CSRFProtection::tokenTag() ?>
<fieldset data-open="bd_basicsettings">
    <section>
        <dl>
            <dt>
                Title
            </dt>
            <dd><?= $demand_obj->title ?>"</dd>



            <dt>
                Description
            </dt>
            <dd><?= $demand_obj->description ?></dd>


            <dt>
                Tags
            </dt>

            <? foreach ($tags as $tag) : ?>
                <dd><?= $tag->mp_tag->name ?></dd>
            <? endforeach; ?>

            <? foreach ($properties as $property) : ?>
                <dt>
                    <?= $property['name'] ?>
                </dt>
                <dd><?= $property['value'] ?></dd>
            <? endforeach; ?>

        </dl>
    </section>
</fieldset>