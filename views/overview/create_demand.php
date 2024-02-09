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

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        new Vue({
            el: '#tags',
            data: {
                tags: []
            },
            created() {
                this.loadTags();
            },
            methods: {
                addItem: function() {
                    this.tags.push('');
                },
                deleteItem: function(index) {
                    this.tags.splice(index, 1);
                },
                loadTags: function() {
                    this.tags = '<?php echo $tagsString; ?>'.split(',');
                }
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
            <div id="tags">
                <label>
                    Tags
                </label>
                <ul>
                    <li v-for="(item, index) in tags" :key="index">
                        <input v-model="tags[index]">
                        <button @click.prevent="deleteItem(index)">Delete</button>
                    </li>
                </ul>
                <input name="tags" type="hidden" :value="tags.join(',')">
                <button @click.prevent="addItem">Add tag</button>
            </div>

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
        <?= Button::create('Submit', 'submit_btn') ?>
        <?= Button::create('Delete', 'delete_btn') ?>
    </footer>
</form>