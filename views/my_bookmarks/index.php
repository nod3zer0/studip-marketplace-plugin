<div id="bookmark_public">
    <table class="default sortable-table">
        <caption>
            <?= $marketplace_comodity_name_plural ?>
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
                <th data-sort="date">Created on</th>
                <th data-sort="text">Marketplace</th>
                <th data-sort="text">Edit</th>
                <th data-sort="text">Bookmark</th>
            </tr>
        </thead>
        <tbody>
            <? if ($all_demands) : ?>
                <? foreach ($all_demands as $demand_obj) : ?>
                    <tr>
                        <td>
                            <a data-dialog href="<?= $controller->link_for('overview/demand_detail', $demand_obj->id) ?>"><?= htmlReady($demand_obj->title) ?></a>
                        </td>
                        <td> <?= strftime('%x', htmlReady($demand_obj->mkdate)) ?></td>
                        <td> <a href="<?= $controller->link_for('overview/index', $demand_obj->mp_marketplace->id) ?>"> <?= htmlReady($demand_obj->mp_marketplace->name) ?> </a></td>
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
                        <td>
                            <bookmark_public icon="false" :set_bookmark_url="'<?= $controller->link_for('my_bookmarks/set_bookmark', $demand_obj->id) ?>'" :get_bookmark_url="'<?= $controller->link_for('my_bookmarks/get_bookmark', $demand_obj->id) ?>'"> </bookmark_public>
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
</div>