<?

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
?>

<div id="global_config">
    <h1>Catalog Configuration</h1>
    <h2>Catalogs</h2>
    <ul>
        <li v-for="(item, index) in marketplaces" :key="index">
            <input v-model="marketplaces[index].name">
            <button class="button" @click="deleteItem(index)">Delete</button>
            <label for="type">Enabled</label>
            <input type="checkbox" id="enabled" v-model="marketplaces[index].enabled">
            <span v-if="marketplaces[index].hasOwnProperty('id')">
                <a :href="'<?= $controller->link_for('config/index/') ?>' + marketplaces[index].id">configure</a>
            </span>
        </li>
    </ul>
    <button class="button" @click="addItem">Add catalog</button>
    <h2>Tags</h2>
    <ul>
        <li v-for="(tag, index) in tags" :key="index">

            <input v-model="tags[index].name"> Number of references: {{ tags[index].number_of_references }}
            <button class="button" @click="deleteTag(index)">Delete</button>
        </li>
    </ul>
    <button class="button" @click="addTag">Add tag</button>
    <button class="button" data-confirm @click="deleteUnusedTags">Delete unused tags</button>
    <br>
    <div> <button class="button" @click="submitConfig">Save config</button> </div>
</div>

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        new Vue({
            el: '#global_config',
            data: {
                marketplaces: [],
                tags: []
            },
            created() {
                this.loadProperties();
                this.loadTags();
            },
            methods: {
                loadProperties: function() {
                    fetch('<?= $controller->link_for('global_config/get_config') ?>')
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update properties with data from the response
                                this.marketplaces = data.map(marketplace => ({
                                    name: marketplace.name,
                                    enabled: !!parseInt(marketplace.enabled), // Convert to boolean
                                    id: marketplace.id
                                }));
                            } else {
                                console.error('Invalid properties data received:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching properties:', error);
                        });
                },
                loadTags: function() {
                    fetch('<?= $controller->link_for('global_config/get_tags') ?>')
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update tags with data from the response
                                this.tags = data.map(tag => ({
                                    name: tag.name,
                                    id: tag.id,
                                    number_of_references: tag.number_of_references
                                }));
                            } else {
                                console.error('Invalid tags data received:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching tags:', error);
                        });
                },
                addItem: function() {
                    this.marketplaces.push({
                        name: '',
                        enabled: false // Default required
                    });
                },
                deleteItem: function(index) {
                    this.marketplaces.splice(index, 1);
                },
                addTag: function() {
                    this.tags.push({
                        name: ''
                    });
                },
                deleteTag: function(index) {
                    this.tags.splice(index, 1);
                },
                deleteUnusedTags: function() {
                    fetch('<?= $controller->link_for('global_config/delete_unused_tags') ?>')
                        .then(response => response.text())
                        .then(data => {
                            console.log('Response:', data);
                            this.loadTags();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                },
                submitConfig: function() {
                    // TODO: make url dynamic
                    fetch('<?= $controller->link_for('global_config/save_config') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                marketplaces: this.marketplaces,
                                tags: this.tags
                            })
                        })
                        .then(response => {
                            // Handle response
                            console.log('Response:', response.text());
                            //reload page
                            location.reload();
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                        });
                }
            }

        });
    });
</script>