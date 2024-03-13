<style>
    #properties_settings ul {
        list-style-type: none;
        padding: 0;
    }

    #properties_settings li {
        margin-bottom: 5px;
        padding: 5px;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        cursor: move;
        /* Change cursor to indicate draggable */
        transition: background-color 0.3s ease;
        /* Add transition effect */
    }

    #properties_settings li.draggable {
        background-color: #e0e0e0;
        /* Change background color when dragging */
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        /* Add shadow effect */
    }
</style>

<div id="properties_settings">
    <ul>
        <li v-for="(item, index) in properties" :key="index" :draggable="true" @dragstart="onDragStart(index)" @dragover.prevent="onDragOver" @drop.prevent="onDrop(index)" @dragenter="onDragEnter(index)" @dragleave="onDragLeave(index)" :class="{ 'draggable': isDragging === index }">
            <input v-model="properties[index].name">
            <button @click="deleteItem(index)">Delete</button>
            <label for="type">Type:</label>
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
            el: '#properties_settings',
            data: {
                properties: [],
                isDragging: null
            },
            created() {
                this.loadProperties();
            },
            methods: {
                loadProperties: function() {
                    fetch('<?= $controller->link_for('config/get_properties', $marketplace_id) ?>')
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
                    fetch('<?= $controller->link_for('config/save_config', $marketplace_id) ?>', {
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
                },
                onDragStart: function(index) {
                    // Set the drag data to the index of the dragged item
                    event.dataTransfer.setData('text/plain', index);
                },
                onDragOver: function() {
                    // Allow drops
                    event.preventDefault();
                },
                onDrop: function(index) {
                    // Get the index of the item being dragged
                    const draggedIndex = event.dataTransfer.getData('text/plain');
                    // Move the item to the new index
                    const draggedProperty = this.properties[draggedIndex];
                    this.properties.splice(draggedIndex, 1); // Remove the item from the original position
                    this.properties.splice(index, 0, draggedProperty); // Insert the item at the new position
                },
                onDragEnter: function(index) {
                    if (this.isDragging !== null && index !== this.isDragging) {
                        // Apply styles to the target element
                        event.target.classList.add('draggable');
                    }
                },
                // Remove the highlight when dragging leaves
                onDragLeave: function(index) {
                    if (this.isDragging !== null && index !== this.isDragging) {
                        // Remove styles from the target element
                        event.target.classList.remove('draggable');
                    }
                }
            }

        });
    });
</script>