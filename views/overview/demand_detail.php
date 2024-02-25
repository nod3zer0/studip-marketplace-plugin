<?

use Studip\Button; ?>
<?= CSRFProtection::tokenTag() ?>



<fieldset data-open="bd_basicsettings">
    <section class="contentbox">
        <header>
            <h1>
                Details
            </h1>
        </header>
        <dl>
            <dt>
                Author
            </dt>
            <dd>
                <?= $avatar->getImageTag(Avatar::MEDIUM) ?>
            </dd>
            <dd>
                <?= htmlReady($demand_obj->author->getFullName()) ?>
            </dd>
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