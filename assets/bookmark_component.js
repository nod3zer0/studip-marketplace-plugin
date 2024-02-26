STUDIP.Vue.load().then(({
    Vue,
    createApp,
    eventBus,
    store
}) => {

    Vue.component('bookmark_public', {
        template: `<button @click="setBookmark">{{ bookmarked ? 'Unbookmark' : 'Bookmark' }}</button>`,
        props: ['set_bookmark_url', 'get_bookmark_url'],
        data: () => ({
            bookmarked: false
        }),
        mounted() {
            this.getBookmark();
        },
        methods: {
            setBookmark() {
                this.bookmarked = !this.bookmarked;
                fetch(STUDIP.URLHelper.getURL(this.set_bookmark_url.concat("/").concat(this.bookmarked)))
                    .then(response => response.json())
                    .then(data => {
                        // Check if data is an array
                        if (Array.isArray(data)) {
                            // Update properties with data from the response
                            this.bookmarked = data.bookmarked;
                        } else {
                            console.error('Invalid properties data received:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching properties:', error);
                    });
            },
            getBookmark() {
                fetch(STUDIP.URLHelper.getURL(this.get_bookmark_url))
                    .then(response => response.json())
                    .then(data => {
                        this.bookmarked = data.bookmarked;
                    })
                    .catch(error => {
                        console.error('Error fetching properties:', error);
                    });
            },
        },
    })

    new Vue({
        el: '#bookmark_public',
    });
});

//in other file