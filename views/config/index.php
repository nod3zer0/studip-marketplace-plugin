<div id="app">
    <ul>
        <li v-for="(item, index) in properties" :key="index">
            <input v-model="properties[index].name">
            <button @click="deleteItem(index)">Delete</button>
            <label for="type">Type (doesn't work at the moment):</label>
            <select id="type" v-model="properties[index].type">
                <option value="1">Short text</option>
                <option value="5">Text area</option>
                <option value="2">Number</option>
                <option value="3">Date</option>
            </select>
            <label for="required">Required:</label>
            <input type="checkbox" id="required" v-model="properties[index].required">
        </li>
    </ul>
    <button @click="addItem">Add property</button>
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
            el: '#app',
            data: {
                properties: []
            },
            created() {
                this.loadProperties();
            },
            methods: {
                loadProperties: function() {
                    fetch('<?= $controller->link_for('config/get_properties') ?>')
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update properties with data from the response
                                this.properties = data.map(prop => ({
                                    name: prop.name,
                                    type: parseInt(prop.type), // Convert to integer
                                    required: !!parseInt(prop.required), // Convert to boolean
                                    id: prop.id
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
                    this.properties.push({
                        name: '',
                        type: 1, // Default type
                        required: false // Default required
                    });
                },
                deleteItem: function(index) {
                    this.properties.splice(index, 1);
                },
                submitProperties: function() {
                    // TODO: make url dynamic
                    fetch('<?= $controller->link_for('config/save_config') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.properties)
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