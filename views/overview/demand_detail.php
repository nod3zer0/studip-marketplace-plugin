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
            template: `<span><button class="button" v-if="icon == 'false'" @click="setBookmark">{{ bookmarked ? 'Unbookmark' : 'Bookmark' }}</button>
                    <img width="16" height="16" src="/public/assets/images/icons/black/add-circle.svg" v-if="icon == 'true' && bookmarked" @click="setBookmark"/>
                    <img width="16" height="16" src="/public/assets/images/icons/black/remove-circle.svg" v-if="icon == 'true' && !bookmarked" @click="setBookmark"/>`,
            props: ['set_bookmark_url', 'get_bookmark_url', 'icon'],
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
                <?= htmlReady(htmlReady($demand_obj->author->getFullName())) ?>
            </dd>
            <dt>
                Description
            </dt>
            <dd><?= $demand_obj->description ?></dd>
            <dt>
                Contact name
            </dt>
            <dd>
                <?= htmlReady($demand_obj->contact_name) ?>
            </dd>
            <dt>
                Contact email
            </dt>
            <dd>
                <?= htmlReady($demand_obj->contact_mail) ?>
            </dd>


            <dt>
                Tags
            </dt>

            <? foreach ($tags as $tag) : ?>
                <span class="mp_tag"><?= $tag->mp_tag->name ?></span>
            <? endforeach; ?>

            <? foreach ($properties as $property) : ?>

                <? if ($property['type'] == 10) : ?>
                    <h2><?= htmlReady($property['name']) ?></h2>
                <? elseif ($property['type'] == 11) : ?>
                    <p><?= htmlReady($property['name']) ?></p>
                <? elseif ($property['type'] == 5) : ?>
                    <dt>
                        <?= htmlReady($property['name']) ?>
                    </dt>
                    <dd><?= \Studip\Markup::markupToHtml($property['value']) ?></dd>
                <? else : ?>
                    <dt>
                        <?= htmlReady($property['name']) ?>
                    </dt>
                    <dd><?= htmlReady($property['value']) ?></dd>
                <? endif; ?>
            <? endforeach; ?>

            <dt>
                Category
            </dt>
            <dd>
                <?= htmlReady($selected_path) ?>
            </dd>
        </dl>

        <div id="bookmark">
            <bookmark icon="false" :set_bookmark_url="'<?= $controller->link_for('my_bookmarks/set_bookmark', $demand_obj->id) ?>'" :get_bookmark_url="'<?= $controller->link_for('my_bookmarks/get_bookmark', $demand_obj->id) ?>'" />
        </div>

    </section>

    <footer data-dialog-button>

        <a class="button" href="<?= $controller->url_for('overview/response', $demand_obj->id) ?>" data-dialog="reload-on-close"> Respond
        </a>

    </footer>
</fieldset>