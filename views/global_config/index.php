<div id="global_config">
    <ul>
        <li v-for="(item, index) in marketplaces" :key="index">
            <input v-model="marketplaces[index].name">
            <button @click="deleteItem(index)">Delete</button>
            <label for="type">Type (doesn't work at the moment):</label>
            <input type="checkbox" id="enabled" v-model="marketplaces[index].enabled">
        </li>
    </ul>
    <button @click="addItem">Add marketplace</button>
    <button @click="submitProperties">Submit</button>
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
                marketplaces: []
            },
            created() {
                this.loadProperties();
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
                                    enabled: !!parseInt(marketplace.required), // Convert to boolean
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
                addItem: function() {
                    this.marketplaces.push({
                        name: '',
                        enabled: false // Default required
                    });
                },
                deleteItem: function(index) {
                    this.marketplaces.splice(index, 1);
                },
                submitProperties: function() {
                    // TODO: make url dynamic
                    fetch('<?= $controller->link_for('global_config/save_config') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.marketplaces)
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