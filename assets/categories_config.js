///@author Rene Ceska <xceska06@stud.fit.vutbr.cz>
///@brief Vue component for managing categories

$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {

        Vue.component('category', {
            props: ['categories', 'errors'],
            template: `
            <div class="category-box">
                <template v-for="(category, index) in categories">
                    <div>
                        <input type="text" v-model="category.name" @change="checkUniqueName(category,categories)">
                        <button class="button" @click="addSubcategory(category)">Add Subcategory</button>
                        <button class="button" @click="deleteCategory(index)">Delete</button>
                        <category v-if="category.subcategories.length > 0" :categories="category.subcategories" v-on:error="onError"></category>
                        <div v-if="category.error" style="color: red;">{{ category.error }}</div>
                    </div>
                </template>
            </div>
        `,
            data: () => ({
                error: 0
            }),
            methods: {
                addSubcategory(category) {

                    new_category = {
                        name: 'New Subcategory',
                        subcategories: []
                    };
                    category.subcategories.push(new_category);
                    this.checkUniqueName(new_category, category.subcategories);
                },
                deleteCategory(index) {
                    deleted_category = this.categories[index];
                    this.checkUniqueName(deleted_category, this.categories, true);
                    this.categories.splice(index, 1);

                },
                checkUniqueName(category, categories, isDelete = false) {
                    const countOccurrences = categories.filter(cat => cat.name === category.name).length;
                    const isUnique = countOccurrences <= 1;

                    if (isDelete && !isUnique) {
                        Vue.set(category, 'error', null);
                        this.$emit('error', -1);
                        this.error = 0;
                    } else if (!isUnique) {
                        Vue.set(category, 'error', 'Name must be unique within the category');
                        this.$emit('error', 1);
                        this.error = 1;
                    } else if (category.name === '') {
                        Vue.set(category, 'error', 'Name must not be empty');
                        this.$emit('error', 1);
                        this.error = 1;
                    } else if (category.name.includes('/')) {
                        Vue.set(category, 'error', 'Name must not contain /');
                        this.$emit('error', 1);
                        this.error = 1;
                    } else if (this.error == 1) {
                        Vue.set(category, 'error', null);
                        this.$emit('error', -1);
                        this.error = 0;

                    }
                },
                onError(error) {
                    this.$emit('error', error);
                }
            }
        });

        Vue.component('categories_config', {
            props: ['marketplace_id'],
            template: `
            <div>
            <div>
            <category v-on:error="onError" :error="errors" :categories="categories"></category>
            <button class="button"  @click="addCategory">Add Category</button>
            </div>
            <div>
            <button  class="button"  :disabled="hasErrors" @click="saveCategories">Save</button>
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
                }],
                errors: 0
            }),
            mounted() {
                this.get_categories();
            },
            computed: {
                hasErrors() {
                    return this.errors > 0;
                }
            },
            methods: {
                onError(error) {
                    this.errors += error;
                },
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
                            location.reload();
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