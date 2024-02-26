<?

use Studip\Button; ?>
<?= CSRFProtection::tokenTag() ?>

<script>
    STUDIP.Vue.load().then(({
        Vue,
        createApp,
        eventBus,
        store
    }) => {

        Vue.component('bookmark', {
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
            el: '#bookmark',
        });
    });
</script>

<fieldset data-open="bd_basicsettings">
    <section class="contentbox">
        <header>
            <h1>
                Details
            </h1>
        </header>
        <dl>
            <dt>
                Author
            </dt>
            <dd>
                <?= $avatar->getImageTag(Avatar::MEDIUM) ?>
            </dd>
            <dd>
                <?= htmlReady($demand_obj->author->getFullName()) ?>
            </dd>
            <dt>
                Description
            </dt>
            <dd><?= $demand_obj->description ?></dd>


            <dt>
                Tags
            </dt>

            <? foreach ($tags as $tag) : ?>
                <dd><?= $tag->mp_tag->name ?></dd>
            <? endforeach; ?>

            <? foreach ($properties as $property) : ?>
                <dt>
                    <?= $property['name'] ?>
                </dt>
                <dd><?= $property['value'] ?></dd>
            <? endforeach; ?>

        </dl>

        <div id="bookmark">
            <bookmark :set_bookmark_url="'<?= $controller->link_for('my_bookmarks/set_bookmark', $demand_obj->id) ?>'" :get_bookmark_url="'<?= $controller->link_for('my_bookmarks/get_bookmark', $demand_obj->id) ?>'" />
        </div>

    </section>
</fieldset>