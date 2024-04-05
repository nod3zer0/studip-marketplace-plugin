$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {




        // tag settings component
        Vue.component('tag_settings', {
            template: `<div>
        <span>
            <input type="text" name="search-query" @focus="OnChange" value="" ref="search_input" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp">
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
            props: ['all_tags', 'picked_tags', 'set_picked_tags_url'],
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
                set_picked_tags() {
                    fetch(this.set_picked_tags_url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                tags: this.picked_tags
                            })
                        })
                        .then(response => {
                            // Handle response
                            console.log('Response:', response.text());
                            //reload page
                            location.reload();
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                        });
                },
                filterResults(event) {

                    this.results = this.tags.filter(tag => tag.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1);
                    //remove already picked tags
                    this.results = this.results.filter(tag => !this.picked_tags.find(picked_tag => picked_tag.id === tag.id));
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
        });

        //search notification settings component

        Vue.component('search_notification_settings', {
            template: `
            <div>
                <div v-for="notification in notifications" :key="notification.id">
                    <h2>{{ notification.marketplace }}</h2>
                    <ul>
                        <li v-for="query in notification.queries" :key="query.id">
                        {{query.query}}
                            <button @click="deleteQuery(notification.id, query.id)">Delete</button>
                        </li>
                    </ul>
                </div>
                <button @click="saveNotifications" value="Save" class="button">Save</button>

            </div>`,

            data: () => ({
                notifications: [],
            }),
            mounted() {
                this.getNotifications();
            },
            methods: {
                deleteQuery(notificationId, queryId) {
                    const notification = this.notifications.find(notification => notification.id === notificationId);
                    notification.queries = notification.queries.filter(query => query.id !== queryId);
                },
                getNotifications() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/user_config/get_search_notifications'))
                        .then(response => response.json())
                        .then(data => {
                            this.notifications = data.notifications;
                        })
                        .catch(error => {
                            console.error('Error fetching notifications:', error);
                        });
                },
                saveNotifications() {
                    fetch(STUDIP.URLHelper.getURL('plugins.php/marketplace/user_config/set_search_notifications'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                notifications: this.notifications
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
                }
            }
        });


        new Vue({
            el: '#notification_settings',
        });
    });
});
//in other file