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
        <input type="text" name="search-query" required value="" ref="search_input" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp">
        <ul v-show="isOpen" class="autocomplete-results">
            <li :class="{ 'is-active': i === arrowCounter }" @click="setResult(result)" v-for="(result, i) in results_render" :key="i" class="autocomplete-result">
                {{ result }}
            </li>
        </ul>
        <input type="button" value="Notify on new demands" @click="SetNotification()">
        </span>
        `,
            props: ['attributes_url', 'marketplace_id'],
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
                results: [],
                results_render: [],
                isOpen: false,
                last_type: '',
                search: '',
                arrowCounter: 0,
                mode: 'attribute'
            }),
            async created() {
                this.loadTags();
                await this.loadAttributes();
                this.loadAttributes();
            },
            mounted() {
                document.addEventListener('click', this.handleClickOutside);
            },
            destroyed() {
                document.removeEventListener('click', this.handleClickOutside);
            },
            methods: {
                loadTags() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/search/get_tags'))
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update properties with data from the response
                                this.tags = data.map(tag => ({
                                    name: tag.name,
                                    type: 'tag'
                                }));
                            } else {
                                console.error('Invalid properties data received:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching properties:', error);
                        });
                },
                async loadAttributes() {
                    fetch(document.getElementById('attributes_url').value)
                        .then(response => response.json())
                        .then(data => {
                            // Check if data is an array
                            if (Array.isArray(data)) {
                                // Update properties with data from the response
                                this.attributes = data.map(attribute => ({
                                    name: attribute.name,
                                    type: parseInt(attribute.type)
                                }));
                            } else {
                                console.error('Invalid properties data received:', data);
                            }
                            this.attributes.push(...[{ name: 'title', type: 1 }, { name: 'description', type: 5 }, { name: 'created', type: 3 }]);
                        })
                        .catch(error => {
                            console.error('Error fetching properties:', error);
                        });

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
                        if ('.'.concat(cuted_item) == last_key.toLowerCase()) {
                            return item;
                        }
                    });

                    this.results.push(...this.tags.filter(item => {
                        //cut item
                        cuted_item = item.name.slice(0, last_key.length - 1);
                        if ('#'.concat(cuted_item) == last_key.toLowerCase()) {
                            return item;
                        }
                    }));
                    if (this.results.length > 0) {
                        this.results_render = this.results.map(item => item.name);
                        this.isOpen = true;
                    }

                    //this.results.push(this.tags.filter(item => '#'.concat(item).toLowerCase().indexOf(last_key[0].toLowerCase()) > -1));
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
                                this.search = this.InsertAtIndex(this.search, '.'.concat(selected.name).slice(last_key.length).concat(' '), search_input.selectionStart);
                                this.isOpen = false;
                                this.SetCursorPos(search_input.selectionStart + '#'.concat(selected.name).slice(last_key.length).length + 1);
                                break;
                            case 'tag':
                                this.search = this.InsertAtIndex(this.search, '#'.concat(selected.name).slice(last_key.length).concat(' '), search_input.selectionStart);
                                this.isOpen = false;
                                this.SetCursorPos(search_input.selectionStart + '#'.concat(selected.name).slice(last_key.length).length + 1);
                                break;
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
                        }

                    } else if (this.mode == 'operator') {
                        this.search = this.InsertAtIndex(this.search, ' '.concat(selected.name, ' '), search_input.selectionStart);
                        this.mode = 'attribute';
                        this.isOpen = false;
                        this.SetCursorPos(search_input.selectionStart + selected.name.length + 2);
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

                    this.$nextTick(() => {
                        document.getElementsByClassName('autocomplete-result is-active')[0].scrollIntoView();
                    });
                },
                onArrowUp() {
                    if (this.arrowCounter <= 0) {
                        this.arrowCounter = this.results.length - 1;
                    }
                    if (this.arrowCounter > 0) {
                        this.arrowCounter = this.arrowCounter - 1;
                    }
                    this.$nextTick(() => {
                        document.getElementsByClassName('autocomplete-result is-active')[0].scrollIntoView();
                    });
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