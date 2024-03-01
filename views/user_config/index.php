<div id="notification_settings">
    <notification_settings :get_tags_url="'<?= $controller->link_for('user_config/get_tags') ?>'" :get_picked_tags_url="'<?= $controller->link_for('user_config/get_subscribed_tags') ?>'" :set_picked_tags_url="'<?= $controller->link_for('user_config/set_tags') ?>'" />
</div>
<!-- <div id="search_notification_settings">
    <ul>
        <li v-for="(item, index) in searches" :key="index">
            <input v-model="searches[index].name">
            <button @click="deleteItem(index)">Delete</button>
        </li>
    </ul>
    <button @click="addItem">Add search notification</button>
    <button @click="submitSearches">Submit</button>
</div> -->

<script>
    // STUDIP.Vue.load().then(({
    //     Vue,
    //     createApp,
    //     eventBus,
    //     store
    // }) => {
    //     new Vue({
    //         el: '#search_notification_settings',
    //         data: {
    //             searches: []
    //         },
    //         created() {
    //             this.loadSearches();
    //         },
    //         methods: {
    //             loadSearches: function() {
    //                 fetch('<?= $controller->link_for('user_config/get_search_notifications', $marketplace_id) ?>')
    //                     .then(response => response.json())
    //                     .then(data => {
    //                         // Check if data is an array
    //                         if (Array.isArray(data)) {
    //                             // Update properties with data from the response
    //                             this.searches = data.map(prop => ({
    //                                 name: prop.name,
    //                                 type: parseInt(prop.type), // Convert to integer
    //                                 required: !!parseInt(prop.required), // Convert to boolean
    //                                 id: prop.id
    //                             }));
    //                         } else {
    //                             console.error('Invalid properties data received:', data);
    //                         }
    //                     })
    //                     .catch(error => {
    //                         console.error('Error fetching properties:', error);
    //                     });
    //             },
    //             addItem: function() {
    //                 this.searches.push({
    //                     name: '',
    //                     type: 1, // Default type
    //                     required: false // Default required
    //                 });
    //             },
    //             deleteItem: function(index) {
    //                 this.searches.splice(index, 1);
    //             },
    //             submitSearches: function() {
    //                 // TODO: make url dynamic
    //                 fetch('<?= $controller->link_for('config/save_config', $marketplace_id) ?>', {
    //                         method: 'POST',
    //                         headers: {
    //                             'Content-Type': 'application/json'
    //                         },
    //                         body: JSON.stringify(this.searches)
    //                     })
    //                     .then(response => {
    //                         // Handle response
    //                         console.log('Response:', response.text());
    //                         //reload page
    //                         location.reload();
    //                     })
    //                     .catch(error => {
    //                         // Handle error
    //                         console.error('Error:', error);
    //                     });
    //             }
    //         }

    //     });
    // });
</script>