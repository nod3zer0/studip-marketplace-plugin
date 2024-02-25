<div id="global_config">
    <h1>Marketplace Configuration</h1>
    <h2>Marketplaces</h2>
    <ul>
        <li v-for="(item, index) in marketplaces" :key="index">
            <input v-model="marketplaces[index].name">
            <button @click="deleteItem(index)">Delete</button>
            <label for="type">Enabled</label>
            <input type="checkbox" id="enabled" v-model="marketplaces[index].enabled">
        </li>
    </ul>
    <button @click="addItem">Add marketplace</button>
    <h2>Tags</h2>
    <ul>
        <li v-for="(tag, index) in tags" :key="index">

            <input v-model="tags[index].name"> Number of references: {{ tags[index].number_of_references }}
            <button @click="deleteTag(index)">Delete</button>
        </li>
    </ul>
    <button @click="addTag">Add tag</button>
    <button @click="deleteUnusedTags">Delete unused tags</button>
    <br>
    <div> <button @click="submitConfig">Save config</button> </div>
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