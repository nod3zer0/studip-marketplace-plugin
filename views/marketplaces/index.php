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
                    <? if ($marketplace->enabled) : ?>
                        <td>

                            <a href="<?= $controller->link_for('overview/index/', []) . $marketplace->id ?>"><?= htmlReady($marketplace->name) ?></a>

                        </td>
                    <? endif; ?>
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