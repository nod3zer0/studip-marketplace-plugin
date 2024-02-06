<div id="app">
    <ul>
        <li v-for="(item, index) in properties" :key="index">
            <input v-model="properties[index]">
            <button @click="deleteItem(index)">Delete</button>
        </li>
    </ul>
    <button @click="addItem">Add property</button>
    <button @click="submitProperties">Submit</button>
</div>

<script>
    STUDIP.Vue.load().then(({Vue, createApp, eventBus, store}) =>  {
        new Vue({
        el: '#app',
        data: {
            properties: []
        },
        methods: {
            addItem: function() {
                this.properties.push('');
            },
            deleteItem: function(index) {
                this.properties.splice(index, 1);
            },
            submitProperties: function() {
                // TODO: make url dynamic
                fetch('/public/plugins.php/marketplace/config/save_config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.properties)
                })
                .then(response => {
                    // Handle response
                    console.log('Response:', response.text());
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