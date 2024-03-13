<?

use Studip\Button; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var selects = document.querySelectorAll('.date_select');


        selects.forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                var rangeInput = this.parentElement.querySelector('.date_span');
                var dateInput = this.parentElement.querySelector('.date_span_range');

                if (this.value === 'range') {
                    rangeInput.classList.add('hidden');
                    dateInput.classList.remove('hidden');
                } else {
                    rangeInput.classList.remove('hidden');
                    dateInput.classList.add('hidden');
                }
            });
        });
    });
    $(document).ready(function() {
        var selects = document.querySelectorAll('.date_select');
        selects.forEach(function(selectElement) {
            var rangeInput = selectElement.parentElement.querySelector('.date_span');
            var dateInput = selectElement.parentElement.querySelector('.date_span_range');
            if (selectElement.value === 'range') {
                rangeInput.classList.add('hidden');
                dateInput.classList.remove('hidden');
            } else {
                rangeInput.classList.remove('hidden');
                dateInput.classList.add('hidden');
            }
        });
    });
</script>
<style>
    .hidden {
        display: none;
    }
</style>

<form>
    <fieldset>
        <table>
            <tbody>
                <tr>
                    <td> Title</td>
                    <td> <input type="text" name="title[value]" value="<?= $default_property_data["title"]["value"] ?>"> </td>
                </tr>
                <tr>
                    <td> Description</td>
                    <td> <input type="text" name="description[value]" value="<?= $default_property_data["description"]["value"] ?>"> </td>
                </tr>
                <tr>
                    <td>created</td>
                    <td>
                        <select class="date_select" name="created[compare_type]" ; ?>">
                            <option value="to" <? if ($default_property_data['created']["compare_type"] == "to") : echo "selected";
                                                endif; ?>>to</option>
                            <option value="from" <? if ($default_property_data['created']["compare_type"] == "from") : echo "selected";
                                                    endif; ?>>from</option>
                            <option value="equal" <? if ($default_property_data['created']["compare_type"] == "equal") : echo "selected";
                                                    endif; ?>>equal</option>
                            <option value="range" <? if ($default_property_data['created']["compare_type"] == "range") : echo "selected";
                                                    endif; ?>>range</option>
                        </select>

                        <span class="date_span">
                            <input type="date" name="created[value]" value="<?= $default_property_data["created"]["value"] ?>">
                        </span>
                        <span class="date_span_range hidden">
                            from:
                            <input type="date" name="created[range_value_from]" min="0" max="100" value="<?= $default_property_data["created"]["value_from"] ?>">
                            to:
                            <input type="date" name="created[range_value_to]" min="0" max="100" value="<?= $default_property_data["created"]["value_to"] ?>">
                        </span>
                    </td>
                    <?php foreach ($properties as $property) : ?>

                <tr>
                    <td><?php echo $property['name']; ?></td>
                    <td>
                        <?php if ($property['type'] === '2') : //number
                        ?>
                            <select name="<?php echo str_replace(" ", "_", $property['name']) . "[compare_type]" ?>" value="<?= $custom_property_data[$property['name']]["compare_type"] ?>">
                                <option value="greater" <? if ($custom_property_data[$property['name']]["compare_type"] == "greater") : echo "selected";
                                                        endif; ?>>&gt;</option>
                                <option value="less" <? if ($custom_property_data[$property['name']]["compare_type"] == "less") : echo "selected";
                                                        endif; ?>>
                                    &lt; </option>
                                <option value="equal" <? if ($custom_property_data[$property['name']]["compare_type"] == "equal") : echo "selected";
                                                        endif; ?>>=</option>
                                <option value="less_equal" <? if ($custom_property_data[$property['name']]["compare_type"] == "less_equal") : echo "selected";
                                                            endif; ?>>
                                    &lt;=</option>
                                <option value="greater_equal" <? if ($custom_property_data[$property['name']]["compare_type"] == "greater_equal") : echo "selected";
                                                                endif; ?>>&gt;=</option>
                            </select>
                            <input type="number" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>" value="<?= $custom_property_data[$property['name']]["value"] ?>">
                        <?php elseif ($property['type'] === '3') : //date
                        ?>
                            <select class="date_select" name="<?php echo str_replace(" ", "_", $property['name']) . "[compare_type]"; ?>">
                                <option value="to" <? if ($custom_property_data[$property['name']]["compare_type"] == "to") : echo "selected";
                                                    endif; ?>>to</option>
                                <option value="from" <? if ($custom_property_data[$property['name']]["compare_type"] == "from") : echo "selected";
                                                        endif; ?>>from</option>
                                <option value="equal" <? if ($custom_property_data[$property['name']]["compare_type"] == "equal") : echo "selected";
                                                        endif; ?>>equal</option>
                                <option value="range" <? if ($custom_property_data[$property['name']]["compare_type"] == "range") : echo "selected";
                                                        endif; ?>>range</option>
                            </select>

                            <span class="date_span">
                                <input type="date" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>" value="<?= $custom_property_data[$property['name']]["value"] ?>">
                            </span>
                            <span class="date_span_range hidden">
                                from:
                                <input type="date" id="<?php echo str_replace(" ", "_", $property['name']) . "_rangeValue"; ?>" name="<?php echo str_replace(" ", "_", $property['name']) . "[range_value_from]"; ?>" min="0" max="100" value="<?= $custom_property_data[$property['name']]["value_from"] ?>">
                                to:
                                <input type="date" id="<?php echo str_replace(" ", "_", $property['name']) . "_rangeValue"; ?>" name="<?php echo str_replace(" ", "_", $property['name']) . "[range_value_to]"; ?>" min="0" max="100" value="<?= $custom_property_data[$property['name']]["value_to"] ?>">
                            </span>
                        <?php else : ?>
                            <input type="text" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>" value="<?= $custom_property_data[$property['name']]["value"] ?>">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h2> Tags </h2>
        <div id="search_tag_select">
            <search_tag_select :selected_tags="'<?= $selected_tags ?>'" :tags_input="'<?= $tags ?>'"></search_tag_select>
        </div>
        <h2> Categories </h2>
        <div id="search_category_select">
            <search_category_select :selected_path="'<?= $selected_categories ?>'" :categories="<?= str_replace("\"", "'", $categories)  ?>"></search_category_select>
        </div>
        <?= Button::create('Search') ?>
    </fieldset>
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