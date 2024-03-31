<?

use Studip\Button; ?>
<form action="<?= $controller->link_for('user_config/save_user_config') ?>" method="post">

    <div id="notification_settings">
        <h1> Subscribe to tags </h1>
        <tag_settings :all_tags="<?= $tags ?>" :picked_tags="<?= $picked_tags ?>" :set_picked_tags_url="'<?= $controller->link_for('user_config/set_tags') ?>'"> </tag_settings>
        <h1> Subscribe to searches </h1>
        <search_notification_settings> </search_notification_settings>
    </div>

    <?= Button::create('Save', 'save') ?>
</form>
<!--
<div id="categories_user_config">
    <category :categories="categories" :selected_categories="selected_categories" :parent="null"></category>
    {{JSON.stringify( selected_categories) }}
</div> -->