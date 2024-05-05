<?

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
?>

<script>
    function deleteDemands() {
        var selectedDemands = document.querySelectorAll('input[name="selected_demands[]"]:checked');
        var demandIds = [];
        selectedDemands.forEach(function(demand) {
            demandIds.push(demand.value);
        });

        if (demandIds.length === 0) {
            alert("Please select at least one demand to delete.");
            return;
        }

        // Assuming your endpoint for deleting demands is '/delete_demands'
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= $controller->link_for('overview/delete_demands/') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                // Handle success response here
                // For example, reload the page
                location.reload();
            } else {
                console.error(xhr.statusText);
                // Handle error response here
                // For example, show an error message
                alert("Failed to delete demands. Please try again later.");
            }
        };
        xhr.onerror = function() {
            console.error(xhr.statusText);
            // Handle error response here
            // For example, show an error message
            alert("Failed to delete demands. Please try again later.");
        };
        xhr.send(JSON.stringify({
            demand_ids: demandIds
        }));
    }
</script>
<div id="bookmark_public">
    <? if ($GLOBALS['user']->perms == 'root') : ?>
        <button class="button" style="float: right;" onclick="deleteDemands()">Delete</button>
    <? endif; ?>
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
                <th data-sort="text">Bookmark</th>
                <? if ($GLOBALS['user']->perms == 'root') : ?>
                    <th data-sort="text"></th>
                <? endif; ?>
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
                        <td>

                            <bookmark_public icon="false" :set_bookmark_url="'<?= $controller->link_for('my_bookmarks/set_bookmark', $demand_obj->id) ?>'" :get_bookmark_url="'<?= $controller->link_for('my_bookmarks/get_bookmark', $demand_obj->id) ?>'"> </bookmark_public>

                        </td>
                        <? if ($GLOBALS['user']->perms == 'root') : ?>
                            <td>
                                <input type="checkbox" name="selected_demands[]" value="<?= $demand_obj->id ?>">
                            </td>
                        <? endif; ?>
                    </tr>
                <? endforeach; ?>
            <? else : ?>
                <tr>
                    <td colspan="6">
                        No commodities found
                    </td>
                </tr>

            <? endif; ?>
        </tbody>
        <tfoot>


        </tfoot>
    </table>
</div>