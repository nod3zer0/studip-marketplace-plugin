<?

use Studip\Button; ?>


<input type="hidden" id="attributes_url" value="<?= $controller->link_for('search/get_attributes', $marketplace_id) ?>">

<form class="default collapsable" action="<?= $controller->link_for('search/index', $marketplace_id) ?>" method="get">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <div>
            <label class="required">
                search
            </label>
            <div id="search_input">
                <search_input :value="'<?= $query ?>'" :marketplace_id="'<?= $marketplace_id ?>'" :attributes_url="'<?= $controller->link_for('search/get_attributes', $marketplace_id) ?>'" />

                <!-- <input type="text" name="search-query" required value="" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp"> -->

                <!-- <ul v-show="isOpen" class="autocomplete-results">
                    <li :class="{ 'is-active': i === arrowCounter }" @click="setResult(result)" v-for="(result, i) in results_render" :key="i" class="autocomplete-result">
                        {{ result }}
                    </li>
                </ul>  -->

            </div>
            <?= Button::create('Search') ?>

        </div>
    </fieldset>


    <footer data-dialog-button>

    </footer>
</form>

<table class="default sortable-table">
    <caption>
        Demands
    </caption>
    <colgroup>
        <col>
        <col style="width: 80px">
        <col style="width: 20%">
        <col style="width: 80px">
    </colgroup>
    <thead>
        <tr>
            <th data-sort="text">Title</th>
            <th data-sort="text">Author</th>
            <th data-sort="digit">Created on</th>
            <? if (!$marketplace_id) : ?>
                <th data-sort="text">Marketplace</th>
            <? endif; ?>
            <th data-sort="text">Edit</th>
        </tr>
    </thead>
    <tbody>
        <? if ($all_demands) : ?>
            <? foreach ($all_demands as $demand_obj) : ?>
                <tr>
                    <td>
                        <a data-dialog href="<?= $controller->link_for('overview/demand_detail', $demand_obj->id) ?>"><?= $demand_obj->title ?></a>
                    </td>
                    <td><?= htmlReady($demand_obj->author->getFullName()) ?></td>
                    <td> <?= strftime('%x', $demand_obj->mkdate) ?></td>
                    <? if (!$marketplace_id) : ?>
                        <td><?= htmlReady($demand_obj->marketplace_id->name) ?></td>
                    <? endif; ?>
                    <td>
                        <? if ($demand_obj->hasPermission()) : ?>
                            <? $actions = ActionMenu::get(); ?>
                            <? $actions->addLink(
                                $controller->url_for('overview/create_demand/') . $marketplace_id . "/" . $demand_obj->id,
                                'Edit',
                                Icon::create('edit'),
                                ['data-dialog' => true]
                            ); ?>
                            <?= $actions ?>
                        <? endif; ?>
                    </td>
                </tr>
            <? endforeach; ?>
        <? else : ?>
            <tr>
                <td colspan="4">
                </td>
            </tr>

        <? endif; ?>
    </tbody>
</table>