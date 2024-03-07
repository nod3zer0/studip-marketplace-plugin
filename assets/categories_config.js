$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {

        Vue.component('category', {
            props: ['categories'],
            template: `
            <div class="category-box">
                <template v-for="(category, index) in categories">
                    <div>
                        <input type="text" v-model="category.name">
                        <button @click="addSubcategory(category)">Add Subcategory</button>
                        <button @click="deleteCategory(index)">Delete</button>
                        <category v-if="category.subcategories.length > 0" :categories="category.subcategories"></category>
                    </div>
                </template>
            </div>
        `,
            methods: {
                addSubcategory(category) {
                    category.subcategories.push({
                        name: 'New Subcategory',
                        subcategories: []
                    });
                },
                deleteCategory(index) {
                    this.categories.splice(index, 1);
                }
            }
        });

        Vue.component('categories_config', {
            props: ['marketplace_id'],
            template: `
            <div>
            <div>
            <category :categories="categories"></category>
            <button @click="addCategory">Add Category</button>
            </div>
            <div>
            <button @click="saveCategories">Save</button>
            </div>
            </div>`,
            data: () => ({
                categories: [{
                    name: 'Category 1',
                    subcategories: [{
                        name: 'Subcategory 1-1',
                        subcategories: [{
                            name: 'Subcategory 1-1-1',
                            subcategories: []
                        }]
                    }]
                }, {
                    name: 'Category 2',
                    subcategories: []
                }]
            }),
            mounted() {
                this.get_categories();
            },
            methods: {
                addCategory() {
                    this.categories.push({
                        name: 'New Category',
                        subcategories: []
                    });
                },
                saveCategories() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/config/set_categories/').concat(this.marketplace_id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                categories: this.categories
                            })
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
                get_categories() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/config/get_categories/').concat(this.marketplace_id))
                        .then(response => response.json())
                        .then(data => {
                            this.categories = data;
                        })
                        .catch(error => {
                            console.error('Error fetching categories:', error);
                        });
                }
            }
        });

        new Vue({
            el: '#categories_config',
        });
    });
});