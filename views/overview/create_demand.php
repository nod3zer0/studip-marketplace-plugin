<?

use Studip\Form;
use Studip\Button; ?>

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        new Vue({
            el: '#tags',
            data: {
                tags: []
            },
            created() {
                this.loadTags();
            },
            methods: {
                addItem: function() {
                    this.tags.push('');
                },
                deleteItem: function(index) {
                    this.tags.splice(index, 1);
                },
                loadTags: function() {
                    this.tags = '<?php echo $tagsString; ?>'.split(',');
                }
            }
        });
    });
</script>

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        Vue.component('category_picker', {
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
        <input type="hidden" name="selected_categories" :value="JSON.stringify(selectedCategories)">
        </div>
        `,
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
            el: '#categories',
        });
    });
</script>

<form class="default collapsable" action="<?= $controller->link_for('overview/store_demand', $marketplace_id, $demand_obj->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <div>
            <label class="required">
                Title
            </label>
            <input name="title" required value="<?= htmlReady($demand_obj->title) ?>">
        </div>

        <div>
            <label>
                Description
            </label>
            <textarea class="add_toolbar wysiwyg" name="description"><?= wysiwygReady($demand_obj->description) ?></textarea>
        </div>
        <div>
            <div id="tags">
                <label>
                    Tags
                </label>
                <ul>
                    <li v-for="(item, index) in tags" :key="index">
                        <input v-model="tags[index]">
                        <button @click.prevent="deleteItem(index)">Delete</button>
                    </li>
                </ul>
                <input name="tags" type="hidden" :value="tags.join(',')">
                <button @click.prevent="addItem">Add tag</button>
            </div>

        </div>
        <input type="hidden" name="tags_previous" value="<?= $tagsString ?>">

        <? foreach ($properties as $property) : ?>
            <div>
                <label>
                    <?= $property['name'] ?>
                </label>
                <?
                $property_html = "";
                $required = "";
                if ($property["required"]) {
                    $required = "required";
                }
                switch ($property['type']) {
                    case 1:
                        $property_html = ('<input type="text" ' . $required . ' name="custom_properties[' . $property['id'] . ']" value="' . htmlReady($property['value']) . '">');
                        break;
                    case 2:
                        $property_html = ('<input type="number" '  . $required . ' name="custom_properties[' . $property['id'] . ']" value="' . htmlReady($property['value']) . '">');
                        break;
                    case 3:
                        $property_html = ('<input type="date" '  . $required .  ' name="custom_properties[' . $property['id'] . ']" value="' . htmlReady($property['value']) . '">');
                        break;
                    case 4:
                        //TODO boolean
                    case 5:
                        $property_html = ('<textarea class="add_toolbar wysiwyg"'  . $required .  ' name="custom_properties[' . $property['id'] . ']">' . htmlReady($property['value']) . '</textarea>');
                        break;
                }
                echo $property_html;
                ?>
                <!-- <? if ($property["required"])  echo "required"; ?> name="custom_properties[<?= $property['id'] ?>]" value="<?= htmlReady($property['value']) ?>"> -->
            </div>
        <? endforeach; ?>

        <div> Category </div>
        <div id="categories">
            <category_picker :categories="<?= str_replace("\"", "'", $categories) ?>" :selected_path="'<?= $selected_path ?>'"></category_picker>
        </div>
        </div>

    </fieldset>

    <footer data-dialog-button>
        <?= Button::create('Submit', 'submit_btn') ?>
        <?= Button::create('Delete', 'delete_btn') ?>

    </footer>
</form>