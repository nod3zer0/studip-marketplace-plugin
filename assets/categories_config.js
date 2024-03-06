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

        new Vue({
            el: '#categories_config',
            data: {
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
            },
            methods: {
                addCategory() {
                    this.categories.push({
                        name: 'New Category',
                        subcategories: []
                    });
                }
            }
        });
    });
});