STUDIP.Vue.load().then(({
    Vue,
    createApp,
    eventBus,
    store
}) => {

    Vue.component('notification_settings', {
        template: `<div>
        <span>
            <input type="text" name="search-query" required value="" ref="search_input" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp">
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
                        <button @click="set_picked_tags">Save</button>
                        </div>`,
        props: ['get_tags_url', 'get_picked_tags_url', 'set_picked_tags_url'],
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
                fetch(this.get_tags_url)
                    .then(response => response.json())
                    .then(data => {
                        this.tags = data.tags;
                    })
                    .catch(error => {
                        console.error('Error fetching tags:', error);
                    });
            },
            get_picked_tags() {
                fetch(this.get_picked_tags_url)
                    .then(response => response.json())
                    .then(data => {
                        this.picked_tags = data.picked_tags;
                    })
                    .catch(error => {
                        console.error('Error fetching picked tags:', error);
                    });
            },
            set_picked_tags() {
                fetch(this.set_picked_tags_url)
                    .then(response => response.json())
                    .then(data => {
                        this.picked_tags = data.picked_tags;
                    })
                    .catch(error => {
                        console.error('Error setting picked tags:', error);
                    });
            },
            filterResults(event) {

                this.results = this.tags.filter(tag => tag.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1);
                //remove already picked tags
                this.results = this.results.filter(tag => !this.picked_tags.includes(tag));
            },
            OnTab() {
                this.isOpen = false;
                this.picked_tags.push(this.results[this.arrowCounter]);
                this.arrowCounter = -1;
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
                    document.getElementsByClassName('mp_autocomplete-result is-active')[0].scrollIntoView();
                });

            },
            onArrowUp() {
                if (this.arrowCounter > 0) {
                    this.arrowCounter = this.arrowCounter - 1;
                }
                if (this.arrowCounter < 0) {
                    this.arrowCounter = this.results.length - 1;
                }
                this.$nextTick(() => {
                    document.getElementsByClassName('mp_autocomplete-result is-active')[0].scrollIntoView();
                });
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
    })

    new Vue({
        el: '#notification_settings',
    });
});

//in other file