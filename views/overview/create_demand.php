<?

use Studip\Button; ?>

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        new Vue({
            el: '#custom_properties_values',
            data: {
                custom_properties: [],
                demand_id: '<?php echo $demand_obj->id; ?>' //TODO do better, this is just dirty fix
            },
            created() {
                this.loadCustomProperties();
            },
            methods: {
                loadCustomProperties: function() {
                    fetch('/public/plugins.php/marketplace/overview/get_custom_properties', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify([this.demand_id])
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update properties with data from the response
                                this.custom_properties = data.map(prop => ({
                                    name: prop.name,
                                    type: parseInt(prop.type), // Convert to integer
                                    required: !!parseInt(prop.required), // Convert to boolean
                                    value: prop.value,
                                    custom_property_id: prop.id
                                }));
                            } else {
                                console.error('Invalid properties data received:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching properties:', error);
                        });
                },
                submitCustomProperties: function() {
                    // TODO: make url dynamic
                    fetch('/public/plugins.php/marketplace/overview/update_custom_properties', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify([this.custom_properties, this.demand_id])
                        })
                        .then(response => {
                            // Handle response
                            console.log('Response:', response.text());
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                        });
                },
                addItem: function() {}

            }

        });
    });
</script>

<form class="default collapsable" action="<?= $controller->link_for('overview/store_demand', $demand_obj->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
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
        <div>
            <label>
                Tags
            </label>
            <input name="tags" value="<?= $tagsString ?>">
        </div>
        <input type="hidden" name="tags_previous" value="<?= $tagsString ?>">

        <? foreach ($properties as $property) : ?>
            <div>
                <label>
                    <?= $property['name'] ?>
                </label>
                <input name="custom_properties[<?= $property['id'] ?>]" value="<?= $property['value'] ?>">
            </div>
        <? endforeach; ?>

    </fieldset>

    <footer data-dialog-button>
        <?= Button::create('Submit') ?>
    </footer>
</form>