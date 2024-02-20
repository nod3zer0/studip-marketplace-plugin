<table class="default sortable-table">
    <caption>
        Marketplaces
    </caption>
    <colgroup>
        <col>
        <col style="width: 80px">
        <col style="width: 20%">
        <col style="width: 80px">
    </colgroup>
    <thead>
        <tr>
            <th data-sort="text">Name</th>
        </tr>
    </thead>
    <tbody>
        <? if ($marketplaces) : ?>
            <? foreach ($marketplaces as $marketplace) : ?>
                <tr>
                    <td>
                        <? if ($marketplace->enabled) : ?>
                            <a href="<?= $controller->link_for('overview', []) ?>"><?= $marketplace->name ?></a>
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