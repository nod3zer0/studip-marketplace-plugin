<div id="app">
    <ul>
        <li v-for="(item, index) in properties" :key="index">
            <input v-model="properties[index]">
            <button @click="deleteItem(index)">Delete</button>
        </li>
    </ul>
    <button @click="addItem">Add property</button>
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
            }
        }
    });
    });
</script>