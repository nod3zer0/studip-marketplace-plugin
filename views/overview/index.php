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
        </tr>
    </thead>
    <tbody>
        <? if ($all_demands) : ?>
            <? foreach ($all_demands as $demand_obj) : ?>
                <tr>
                    <td> <?= $demand_obj->title ?></td>
                    <td><?= htmlReady($demand_obj->author->getFullName()) ?></td>
                    <td> <?= strftime('%x', $demand_obj->mkdate) ?></td>
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