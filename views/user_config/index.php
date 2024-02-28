<div id="notification_settings">
    <notification_settings :get_tags_url="'<?= $controller->link_for('user_config/get_tags') ?>'" :get_picked_tags_url="'<?= $controller->link_for('user_config/get_subscribed_tags') ?>'" :set_picked_tags_url="'<?= $controller->link_for('user_config/set_tags_action') ?>'" />
</div>