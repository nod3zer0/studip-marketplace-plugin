///@author Rene Ceska <xceska06@stud.fit.vutbr.cz>
///@brief Vue component for managing categories in advanced search
$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        Vue.component('search_category_select', {
            props: ['categories', 'selected_path'],
            template: `
        <div>
        <template v-for="(select, index) in selects">
        <span>/</span>
        <select style="width:20%;" v-model="selectedCategories[index]" @change="categoryChanged(index)">
            <option :value="null" v-if="index === 0">Select Category</option>
            <option :value="null" v-else>Select Subcategory</option>
            <option v-for="option in select" :value="option">{{ option.name }}</option>
        </select>
    </template>
    <input type="hidden" name="selected_categories" :value="this.selectedCategoriesString">
    </div>
    `,
            computed: {
                selectedCategoriesString() {
                    var categoryPath;
                    categoryPath = this.selectedCategories.map(category => category ? category.name : '').join('/');

                    return categoryPath;
                },
            },
            mounted() {
                this.$emit('input', this.selectedCategories);
                this.loadPath();
            },
            data: () => ({
                selectedCategories: [null],
                selects: []
            }),
            methods: {
                categoryChanged(index) {
                    // Remove subsequent selects if a category is changed
                    this.selectedCategories.splice(index + 1);
                    this.selects.splice(index + 1);

                    const selectedCategory = this.selectedCategories[index];
                    if (selectedCategory !== null) {
                        if (selectedCategory.subcategories.length > 0) {
                            this.selectedCategories.push(null);
                            this.selects.push(selectedCategory.subcategories);
                        }
                    }
                },
                loadPath() {
                    const path = this.selected_path.split('/');
                    this.selectedCategories = path.map((name, index) => {
                        const category = this.selects[index].find(category => category.name === name);
                        if (category) {
                            this.selects.push(category.subcategories);
                            return category;
                        }
                        return null;
                    });
                }
            },
            created() {
                if (this.categories.length > 0) {
                    this.selects.push(this.categories);
                }
            }
        });


        new Vue({
            el: '#search_category_select',
        });
    });
});