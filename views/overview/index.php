<table class="default sortable-table">
    <caption>
        Texts
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
            <th data-sort="text">Type</th>
            <th data-sort="text">Author</th>
            <th data-sort="digit">Created on</th>
        </tr>
    </thead>
    <tbody>
        <? if ($all_texts) : ?>
            <? foreach ($all_texts as $text_obj) : ?>
                <tr>
                    <td></td>
                    <td><?= $text_obj->getTypeDescription(); ?></td>
                    <td>


                    <td></td>
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