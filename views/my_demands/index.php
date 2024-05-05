<?

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
?>

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
            <th data-sort="digit">Created on</th>
            <th data-sort="text">Edit</th>
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
                    <td>
                        <? if ($demand_obj->hasPermission()) : ?>
                            <? $actions = ActionMenu::get(); ?>
                            <? $actions->addLink(
                                $controller->url_for('overview/create_demand/') . $demand_obj->mp_marketplace->id . "/" . $demand_obj->id,
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
                <td colspan="3">
                    You did not create any demands yet
                </td>
            </tr>

        <? endif; ?>
    </tbody>
</table>