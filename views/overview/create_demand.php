<?

use Studip\Form;
use Studip\Button; ?>

<!-- <script>
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
</script> -->

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


<script>
    $(document).ready(function() {
        STUDIP.Vue.load().then(({
            Vue,
            createApp,
            eventBus,
            store
        }) => {




            // tag settings component
            Vue.component('tags', {
                template: `<div>
        <span>
            <input autocomplete="one-time-code" type="text" name="search-query" @focus="OnChange" value="" ref="search_input" id="search_input" v-model="search" @keydown.esc.prevent="OnEsc" @keydown.enter.prevent="OnEnter" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp">
            <?= tooltipIcon('You can add new tag by writing its name and pressing enter.') ?>
            <ul v-show="isOpen" class="mp_autocomplete-results">
                <li :class="{ 'is-active': i === arrowCounter }" @click="setResult(result)" v-for="(result, i) in results" :key="i" class="mp_autocomplete-result">
                    {{ result.name }}
                </li>
            </ul>
            <div>
                <span v-for="tag in picked_tags" :key="tag">
                    <div class="mp_tag">
                        {{ tag.name }} <span @click="removeTag(tag)" class="mp_remove-tag">x</span>
                        </div>
                        </span>
                        </div>
                        </span>
                        <input type="hidden" name="picked_tags" :value="JSON.stringify({
                            tags: this.picked_tags
                        })">
                        </div>`,
                props: ['all_tags', 'picked_tags'],
                data: () => ({
                    results: [],
                    picked_tags: [],
                    tags: [],
                    isOpen: false,
                    search: '',
                    arrowCounter: -1,
                }),
                mounted() {
                    this.get_tags();
                    this.get_picked_tags();
                    document.addEventListener('click', this.handleClickOutside);
                },
                methods: {
                    get_tags() {
                        this.tags = this.all_tags.tags;
                    },
                    get_picked_tags() {
                        this.picked_tags = this.picked_tags.tags;
                    },
                    filterResults(event) {

                        this.results = this.tags.filter(tag => tag.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1);
                        //remove already picked tags
                        this.results = this.results.filter(tag => !this.picked_tags.find(picked_tag => picked_tag.name === tag.name));
                    },
                    OnEnter() {
                        //add inputed text as tag
                        //check if keyword is already in pickedtags
                        if (!this.picked_tags.find(tag => tag.name === this.search)) {
                            this.picked_tags.push({
                                name: this.search
                            });
                        }
                        this.isOpen = false;
                        this.search = '';
                    },
                    OnEsc() {
                        this.isOpen = false;
                    },
                    OnTab() {
                        this.isOpen = false;
                        if (this.arrowCounter > -1) {
                            this.picked_tags.push(this.results[this.arrowCounter]);
                            this.arrowCounter = -1;
                        }
                    },
                    handleClickOutside(event) {
                        if (!this.$el.contains(event.target)) {
                            this.arrowCounter = -1;
                            this.isOpen = false;
                        }
                    },
                    onArrowDown() {
                        if (this.arrowCounter < this.results.length) {
                            this.arrowCounter = this.arrowCounter + 1;
                        }
                        if (this.arrowCounter >= this.results.length) {
                            this.arrowCounter = 0;
                        }


                    },
                    onArrowUp() {
                        if (this.arrowCounter > 0) {
                            this.arrowCounter = this.arrowCounter - 1;
                        }
                        if (this.arrowCounter < 0) {
                            this.arrowCounter = this.results.length - 1;
                        }

                    },
                    setResult(result) {
                        this.isOpen = false;
                        this.arrowCounter = -1;
                        this.picked_tags.push(result);
                    },
                    OnChange(event) {
                        this.filterResults(event);
                        this.isOpen = true;
                    },
                    removeTag(tag) {
                        this.picked_tags = this.picked_tags.filter(item => item !== tag);
                    },
                },
            });

            new Vue({
                el: '#tags',
            });
        });
    });
</script>

<form data-dialog="reload-on-close" data-secure='true' enctype="multipart/form-data" class="default collapsable" action="<?= $controller->link_for('overview/store_demand', $marketplace_id, $demand_obj->id) ?>" method="post">
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
            <label>
                Contact name
            </label>
            <? if ($demand_obj->contact_name) : ?>
                <input required name="contact_name" value="<?= htmlReady($demand_obj->contact_name) ?>">
            <? else : ?>
                <input required name="contact_name" value="<?= htmlReady($GLOBALS['user']->Vorname . ' ' . $GLOBALS['user']->Nachname) ?>">
            <? endif; ?>
        </div>
        <div>
            <label>
                Contact email
            </label>
            <? if ($demand_obj->contact_mail) : ?>
                <input required name="contact_mail" value="<?= htmlReady($demand_obj->contact_mail) ?>">
            <? else : ?>
                <input required name="contact_mail" value="<?= htmlReady($GLOBALS['user']->email) ?>">
            <? endif; ?>
        </div>
        <div>
            <div id="tags">
                <label>
                    Tags
                </label>
                <tags :all_tags="<?= $tags ?>" :picked_tags="<?= $picked_tags ?>"></tags>
                <!-- <label>
                    Tags
                </label>
                <ul>
                    <li v-for="(item, index) in tags" :key="index">
                        <input v-model="tags[index]">
                        <button class="button" @click.prevent="deleteItem(index)">Delete</button>
                    </li>
                </ul>
                <input name="tags" type="hidden" :value="tags.join(',')">
                <button class="button" @click.prevent="addItem">Add tag</button> -->
            </div>

        </div>
        <input type="hidden" name="tags_previous" value="<?= $tagsString ?>">
        <!-- <div>
            <label class="file-upload">
                <input name="images" type="file" multiple>

                Upload Images
            </label>
        </div> -->

        <? foreach ($properties as $property) : ?>
            <div>
                <label>
                    <?
                    if ($property['type'] == 10) {
                        echo "<h2>" . $property['name'] . "</h2>";
                    } else if ($property['type'] == 11) {
                        echo "<p>" . $property['name'] . "</p>";
                    } else {
                        echo $property['name'];
                    }

                    ?>
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
                        $property_html = ('<textarea class="add_toolbar wysiwyg"'  . $required .  ' name="custom_properties[' . $property['id'] . ']">' . wysiwygReady($property['value']) . '</textarea>');
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
        <button class="buton" data-confirm name="delete_btn" type="submit">
            Delete
        </button>

    </footer>
</form>