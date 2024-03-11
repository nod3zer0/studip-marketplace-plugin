$(document).ready(function() {
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {
        Vue.component('search_tag_select', {
            props: ['tags_input', 'selected_tags'],
            template: ` <div>
                                    <div class="tags">
                                        <div class="mp_tag" v-for="(tag, index) in tags" :key="index" @click="toggleTag(index)" :class="{ selected: selectedTags.includes(tag) }">
                                            {{ tag }}
                                        </div>
                                    </div>
                                    <input type="hidden" name="selected_tags" v-model="selectedTags.join(',')">
                                </div>`,
            data: () => ({
                selectedTags: [],
                tags: []
            }),
            mounted() {
                this.tags = this.tags_input.split(',');
                this.loadTags();
            },
            methods: {
                toggleTag(index) {
                    const tag = this.tags[index];
                    const selectedIndex = this.selectedTags.indexOf(tag);
                    if (selectedIndex === -1) {
                        this.selectedTags.push(tag);
                    } else {
                        this.selectedTags.splice(selectedIndex, 1);
                    }
                },
                loadTags() {
                    this.selectedTags = this.selected_tags.split(',');
                }
            }
        });

        new Vue({
            el: '#search_tag_select',
        });
    });

});