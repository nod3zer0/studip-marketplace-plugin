$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {

        Vue.component('category', {
            props: ['categories', 'selected_categories', 'parent'],
            template: `
            <div class="category-box">
                <template v-for="(category, index) in categories">
                    <div class="tags">
                        <div class="tag" @click="categorySwitch(index)" :class="{ selected: selected_categories.includes(category) }">
                            {{category.name }}
                        </div>
                        \
                        <category v-on:delete_parent_category="deleteParentCategory" :parent="category" v-if="category.subcategories.length > 0" :categories="category.subcategories" :selected_categories="selected_categories"></category>
                    </div>
                </template>
            </div>
        `,
            methods: {
                categorySwitch(index) {
                    if (this.selected_categories.includes(this.categories[index])) {
                        this.removeCategory(index);
                    } else {
                        this.addCategory(index);
                    }
                },
                addCategory(index) {
                    category = this.categories[index];
                    this.selected_categories.push(category);
                    // remove previous selected parent category
                    this.$emit('delete_parent_category', this.parent);
                    // remove selected child categories and childs of childs
                    this.clearSubcategories(category);
                },
                clearSubcategories(category) {
                    for (let subcategory of category.subcategories) {
                        if (this.selected_categories.includes(subcategory)) {
                            index = this.selected_categories.indexOf(subcategory);
                            if (index > -1) {
                                this.selected_categories.splice(index, 1);
                            }

                        }
                    }

                    for (let subcategory of category.subcategories) {
                        this.clearSubcategories(subcategory);
                    }

                },

                removeCategory(index) {
                    this.selected_categories.splice(this.selected_categories.indexOf(this.categories[index]), 1);
                },
                deleteParentCategory(category) {

                    index = this.selected_categories.indexOf(category);
                    if (index > -1) {
                        this.selected_categories.splice(index, 1);
                    } else {
                        this.$emit('delete_parent_category', this.parent);
                    }
                }
            }
        });

        const app = new Vue({
            el: '#categories_user_config',
            data: {
                categories: [{
                    name: 'Category 1',
                    subcategories: [{
                        name: 'Subcategory 1-1',
                        subcategories: [{
                            name: 'Subcategory 1-1-1',
                            subcategories: []
                        }, {
                            name: 'Subcategory 1-1-2',
                            subcategories: []
                        }, {
                            name: 'Subcategory 1-1-3',
                            subcategories: []
                        }]
                    }, {
                        name: 'Subcategory 1-2',
                        subcategories: []
                    }, {
                        name: 'Subcategory 1-3',
                        subcategories: []
                    }]
                }, {
                    name: 'Category 2',
                    subcategories: []
                }],
                selected_categories: []
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