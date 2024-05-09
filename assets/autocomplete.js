/// @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
/// @brief  autocomplete for search input in marketplace
//  @brief  this code was inspired by https://www.digitalocean.com/community/tutorials/vuejs-vue-autocomplete-component
$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {

        Vue.component('search_input', {
            template: `
        <span>
        <input  autocomplete="one-time-code" type="text" name="search-query" value="" ref="search_input" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent= "onArrowDown" @keydown.up.prevent ="onArrowUp">
        <ul autofocus v-show="isOpen" class="autocomplete-results">
            <li :class="{ 'is-active': i === arrowCounter }" @click="setResult(result)" v-for="(result, i) in results_render" :key="i" class="autocomplete-result">
                {{ result }}
            </li>
        </ul>
        <!--  <input type="button" value="Notify on new demands" @click="SetNotification()"> -->
        </span>
        `,
            props: ['attributes_url', 'marketplace_id', 'value', 'categories', 'attributes', 'tags'],
            data: () => ({
                attributes: [{
                    name: 'test1',
                    type: 1
                }, {
                    name: 'test2',
                    type: 2
                }, {
                    name: 'test3',
                    type: 3
                }, {
                    name: 'test4',
                    type: 3
                }, {
                    name: 'test5',
                    type: 5
                }],
                tags: [{
                    name: 'tag1',
                    type: 'tag'
                }, {
                    name: 'tag2',
                    type: 'tag'
                }, {
                    name: 'tag3',
                    type: 'tag'
                }, {
                    name: 'tag4',
                    type: 'tag'
                }, {
                    name: 'tag5',
                    type: 'tag'
                }],
                number_operators: [{
                    name: '=',
                    type: 'number_operator'
                }, {
                    name: '>',
                    type: 'number_operator'
                }, {
                    name: '<',
                    type: 'number_operator'
                }, {
                    name: '>=',
                    type: 'number_operator'
                }, {
                    name: '<=',
                    type: 'number_operator'
                }],
                string_operators: [{
                    name: '=',
                    type: 'string_operator'
                }],
                category_operators: [{
                    name: '=',
                    type: 'category_operator'
                }],
                category_paths: [],
                results: [],
                results_render: [],
                isOpen: false,
                last_type: '',
                search: '',
                arrowCounter: 0,
                mode: 'attribute'
            }),
            async created() {


                // remap tags so they have type attribute
                this.tags = this.tags.map(tag => ({
                    name: tag.name,
                    type: 'tag'
                }));

                //convert type string to int
                this.attributes = this.attributes.map(attribute => {
                    return {
                        name: attribute.name,
                        type: parseInt(attribute.type)
                    };
                });
                // remove headings 10 and descriptions 11
                this.attributes = this.attributes.filter(attribute => attribute.type != 10 && attribute.type != 11);
                this.attributes.push(...[{ name: 'title', type: 1 }, { name: 'description', type: 5 }, { name: 'created', type: 3 }, { name: 'author', type: 1 }, { name: 'category', type: 'category' }]);

                // fore each category create string path to it and add it to list
                this.category_paths = this.getCategoryPaths(this.categories);


            },
            mounted() {
                document.addEventListener('click', this.handleClickOutside);
                this.search = this.value;
            },
            destroyed() {
                document.removeEventListener('click', this.handleClickOutside);
            },
            methods: {
                getCategoryPaths(categories, parentPath = '') {
                    let paths = [];
                    categories.forEach(category => {
                        const fullPath = parentPath + '/' + category.name.trim();
                        paths.push(fullPath);
                        if (category.subcategories.length > 0) {
                            paths = paths.concat(this.getCategoryPaths(category.subcategories, fullPath));
                        }
                    });
                    return paths;
                },
                async SetNotification() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/search/save_search'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                query: this.search,
                                marketplace_id: this.marketplace_id
                            })
                        }).then(response => response.json())
                        .then(data => {
                            console.log(data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                },
                getIndexOfWord(wordlist, cursorPosition) {
                    var index = 0;
                    var i = 0;
                    for (i = 0; i < wordlist.length; i++) {
                        index += wordlist[i].length;
                        if (index >= cursorPosition) {
                            return i;
                        }
                        index += 1; //correction for space
                        if (index >= cursorPosition) {
                            return -1;
                        }
                    }

                },
                filterResults(event) {
                    this.mode = 'attribute';
                    keys = this.search.split(' ');
                    word_index = this.getIndexOfWord(keys, event.target.selectionStart);

                    if (word_index == -1) {
                        this.isOpen = false;
                        return;
                    }

                    last_key = keys[word_index];
                    this.results = [];
                    if (last_key.length == 0) {
                        this.isOpen = false;
                        return;
                    }

                    this.results = this.attributes.filter(item => {
                        //cut item
                        cuted_item = item.name.slice(0, last_key.length - 1);
                        if ('.'.concat(cuted_item).toLowerCase() == last_key.toLowerCase()) {
                            return item;
                        }
                    });

                    this.results.push(...this.tags.filter(item => {
                        //cut item
                        cuted_item = item.name.slice(0, last_key.length - 1);
                        if ('#'.concat(cuted_item).toLowerCase() == last_key.toLowerCase()) {
                            return item;
                        }
                    }));
                    //sort categories by lenght of name shortes first
                    this.results.push(...this.category_paths.filter(item => {
                        //cut item
                        cuted_item = item.slice(0, last_key.length);
                        if (cuted_item.toLowerCase().toLowerCase() == last_key.toLowerCase()) {
                            return item;
                        }
                    }).sort((a, b) => a.length - b.length).map(item => ({
                        name: item,
                        type: 'category_path'
                    })));


                    if (this.results.length > 0) {
                        this.results = this.results.slice(0, 5);
                        this.results_render = this.results.map(item => item.name);
                        this.isOpen = true;
                    }
                },
                InsertAtIndex(str, substring, index) {
                    return str.slice(0, index) + substring + str.slice(index);
                },
                PickSelected(selected) {
                    var search_input = this.$refs.search_input;

                    if (this.mode == 'attribute') {

                        keys = this.search.split(' ');

                        word_index = this.getIndexOfWord(keys, search_input.selectionStart);
                        last_key = keys[word_index];

                        if (last_key.length == 0) {
                            this.isOpen = false;
                            return;
                        }

                        switch (selected.type) {
                            case 1: //short text
                            case 2: //number
                            case 3: //date
                            case 5: //text area
                            case 'category':
                                this.search = this.InsertAtIndex(this.search, '.'.concat(selected.name).slice(last_key.length).concat(' '), search_input.selectionStart);
                                this.isOpen = false;
                                this.SetCursorPos(search_input.selectionStart + '#'.concat(selected.name).slice(last_key.length).length + 1);
                                break;
                            case 'tag':
                                this.search = this.InsertAtIndex(this.search, '#'.concat(selected.name).slice(last_key.length).concat(' '), search_input.selectionStart);
                                this.isOpen = false;
                                this.SetCursorPos(search_input.selectionStart + '#'.concat(selected.name).slice(last_key.length).length + 1);
                                break;
                            case 'category_path':
                                this.search = this.InsertAtIndex(this.search, selected.name.slice(last_key.length).concat(' '), search_input.selectionStart);
                                this.isOpen = false;
                                this.SetCursorPos(search_input.selectionStart + selected.name.slice(last_key.length).length + 1);
                        }

                        switch (selected.type) {
                            case 2: //number
                                this.results = this.number_operators;
                                this.results_render = this.number_operators.map(item => item.name);
                                this.mode = 'operator';
                                this.isOpen = true;
                                break;
                            case 1: //short text
                            case 5: //text area
                                this.results = this.string_operators;
                                this.results_render = this.string_operators.map(item => item.name);
                                this.mode = 'operator';
                                this.isOpen = true;
                                break;
                            case 3: //date
                                this.results = this.number_operators;
                                this.results_render = this.number_operators.map(item => item.name);
                                this.mode = 'operator';
                                this.isOpen = true;
                                break;
                            case 'category':
                                this.results = this.category_operators;
                                this.results_render = this.category_operators.map(item => item.name);
                                this.mode = 'operator';
                                this.isOpen = true;
                                break;
                            default:
                                this.mode = 'attribute';
                                this.isOpen = false;
                                break;
                        }

                    } else if (this.mode == 'operator') {

                        if (selected.type == "category_operator") {
                            this.search = this.InsertAtIndex(this.search, ' '.concat(selected.name, ' /'), search_input.selectionStart);
                            this.isOpen = true; //open for path selection
                            this.SetCursorPos(search_input.selectionStart + selected.name.length + 3);

                            // fill results with all category paths
                            this.results = this.category_paths.sort((a, b) => a.length - b.length).map(item => ({
                                name: item,
                                type: 'category_path'
                            }));
                            this.results_render = this.category_paths.sort((a, b) => a.length - b.length).slice(0, 5);
                        } else {
                            this.search = this.InsertAtIndex(this.search, ' '.concat(selected.name, ' '), search_input.selectionStart);
                            this.isOpen = false; //close end of autocompletion
                            this.SetCursorPos(search_input.selectionStart + selected.name.length + 2);
                        }
                        this.mode = 'attribute';



                    }
                },
                OnTab() {
                    selected = this.results[this.arrowCounter];
                    this.PickSelected(selected);
                },
                setResult(result) {
                    selected = this.results.filter(item => item.name == result)[0];
                    this.PickSelected(selected);
                },
                SetCursorPos(pos) {
                    var search_input = this.$refs.search_input;
                    this.$nextTick(() => {
                        search_input.focus();
                        search_input.setSelectionRange(pos, pos);
                    });
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

                    // this.$nextTick(() => {
                    //     document.getElementsByClassName('autocomplete-result is-active')[0].scrollIntoView();
                    // });
                },
                onArrowUp() {
                    if (this.arrowCounter <= 0) {
                        this.arrowCounter = this.results.length - 1;
                    }
                    if (this.arrowCounter > 0) {
                        this.arrowCounter = this.arrowCounter - 1;
                    }
                    // this.$nextTick(() => {
                    //     document.getElementsByClassName('autocomplete-result is-active')[0].scrollIntoView();
                    // });
                },
                OnChange(event) {
                    this.filterResults(event);
                }
            }
        });
        new Vue({
            el: '#search_input'
        });
    });
});