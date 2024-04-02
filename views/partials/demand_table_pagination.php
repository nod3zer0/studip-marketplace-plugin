<?

use Studip\Button; ?>
<form action="<?= $controller->link_for('overview/index/') . $marketplace_id . "/?page=" . $page ?>" method="get">
    Sort by:
    <select name="order">
        <option value="mkdate_asc" <? if ($order == "mkdate_asc") : echo "selected";
                                    endif; ?>>Created ascending</option>
        <option value="mkdate_desc" <? if ($order == "mkdate_desc") : echo "selected";
                                    endif; ?>>Created descending</option>
        <option value="title_asc" <? if ($order == "title_asc") : echo "selected";
                                    endif; ?>>Title ascending</option>
        <option value="title_desc" <? if ($order == "title_desc") : echo "selected";
                                    endif; ?>>Title descending</option>
        <option value="author_asc" <? if ($order == "author_asc") : echo "selected";
                                    endif; ?>>Author ascending</option>
        <option value="author_desc" <? if ($order == "author_desc") : echo "selected";
                                    endif; ?>>Author descending</option>
    </select>
    <?= Button::create('Sort') ?>
</form>

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
            <th data-sort="text">Author</th>
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
                    <td><?= htmlReady($demand_obj->author->getFullName()) ?></td>
                    <td> <?= htmlReady(strftime('%x', $demand_obj->mkdate)) ?></td>
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
    <tfoot>


        <tr>
            <td colspan="7" style="text-align: right">
                <?= $GLOBALS['template_factory']->render(
                    'shared/pagechooser',
                    [
                        'perPage'      => get_config('ENTRIES_PER_PAGE'),
                        'num_postings' => $number_of_demands,
                        'page'         =>  $page,
                        'pagelink'     =>  $controller->link_for($pagination_url, []) . $marketplace_id . '/?page=%s&order=' . $order,
                    ]
                ) ?>
            </td>
        </tr>
    </tfoot>
</table>