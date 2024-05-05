<?

/**
 * view for advanced search
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

use Studip\Button;
use \Marketplace\CustomPropertySearchStudIp;
use \Marketplace\DefaultPropertySearchStudIp;
?>


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
                    <!-- <td> <input type="text" name="title[value]" value="<?= htmlReady($default_property_data["title"]["value"]) ?>"> </td> -->
                    <td>
                        <?
                        print QuickSearch::get('title[value]', new DefaultPropertySearchStudIp($marketplace_id, 'title'))
                            ->defaultValue(htmlReady($default_property_data["title"]["value"]), htmlReady($default_property_data["title"]["value"]))
                            ->render();
                        ?>
                    </td>
                </tr>
                <tr>
                    <td> Description</td>
                    <td> <input type="text" name="description[value]" value="<?= htmlReady($default_property_data["description"]["value"]) ?>"> </td>

                    <!-- <td> -->
                    <?
                    // print QuickSearch::get('description[value]', new DefaultPropertySearchStudIp($marketplace_id, 'description'))
                    //     ->defaultValue(htmlReady($default_property_data["description"]["value"]), htmlReady($default_property_data["description"]["value"]))
                    //     ->render();
                    ?>
                    <!-- </td> -->
                </tr>
                <tr>
                    <td> Author</td>
                    <!-- <td> <input type="text" name="author[value]" value="<?= htmlReady($default_property_data["author"]["value"]) ?>"> </td> -->
                    <td>
                        <?
                        print QuickSearch::get('author[value]', new DefaultPropertySearchStudIp($marketplace_id, 'author'))
                            ->defaultValue(htmlReady($default_property_data["author"]["value"]), htmlReady($default_property_data["author"]["value"]))
                            ->render();
                        ?>
                    </td>
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
                            <input type="date" name="created[value]" value="<?= htmlReady($default_property_data["created"]["value"]) ?>">
                        </span>
                        <span class="date_span_range hidden">
                            from:
                            <input type="date" name="created[range_value_from]" min="0" max="100" value="<?= htmlReady($default_property_data["created"]["value_from"]) ?>">
                            to:
                            <input type="date" name="created[range_value_to]" min="0" max="100" value="<?= htmlReady($default_property_data["created"]["value_to"]) ?>">
                        </span>
                    </td>
                    <?php foreach ($properties as $property) : ?>

                <tr>
                    <? if ($property['type'] == 1 || $property['type'] == 2 || $property['type'] == 3 || $property['type'] == 5) : ?>
                        <td><?php echo $property['name']; ?></td>
                    <? endif; ?>
                    <td>
                        <?php if ($property['type'] === '2') : //number
                        ?>
                            <select name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[compare_type]" ?>" value="<?= htmlReady($custom_property_data[$property['name']]["compare_type"]) ?>">
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
                            <input type="number" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[value]"; ?>" value="<?= htmlReady($custom_property_data[$property['name']]["value"]) ?>">
                        <?php elseif ($property['type'] === '3') : //date
                        ?>
                            <select class="date_select" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[compare_type]"; ?>">
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
                                <input type="date" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[value]"; ?>" value="<?= $custom_property_data[$property['name']]["value"] ?>">
                            </span>
                            <span class="date_span_range hidden">
                                from:
                                <input type="date" id="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "_rangeValue"; ?>" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[range_value_from]"; ?>" min="0" max="100" value="<?= $custom_property_data[$property['name']]["value_from"] ?>">
                                to:
                                <input type="date" id="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "_rangeValue"; ?>" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[range_value_to]"; ?>" min="0" max="100" value="<?= $custom_property_data[$property['name']]["value_to"] ?>">
                            </span>
                        <?php elseif ($property['type'] === '1' || $property['type'] === '5') : ?>
                            <!-- <input type="text" name="<?php echo htmlReady(str_replace(" ", "_", $property['name'])) . "[value]"; ?>" value="<?= $custom_property_data[$property['name']]["value"] ?>"> -->
                            <?
                            print QuickSearch::get(htmlReady(str_replace(" ", "_", $property['name'])) . "[value]", new CustomPropertySearchStudIp($marketplace_id, $property['name']))
                                ->defaultValue($custom_property_data[$property['name']]["value"], $custom_property_data[$property['name']]["value"])
                                ->render();
                            ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h2> Tags </h2>
        <div id="search_tag_select">
            <search_tag_select :selected_tags="'<?= htmlReady($selected_tags) ?>'" :tags_input="'<?= htmlReady($tags) ?>'"></search_tag_select>
        </div>
        <h2> Categories </h2>
        <div id="search_category_select">
            <search_category_select :selected_path="'<?= htmlReady($selected_categories) ?>'" :categories="<?= str_replace("\"", "'", htmlReady($categories))  ?>"></search_category_select>
        </div>
        <?= Button::create('Search') ?>
        Number of results:
        <select name="limit">
            <option value="10" <? if ($limit == 10) : echo "selected";
                                endif; ?>>10</option>
            <option value="20" <? if ($limit == 20) : echo "selected";
                                endif; ?>>20</option>
            <option value="50" <? if ($limit == 50) : echo "selected";
                                endif; ?>>50</option>
            <option value="100" <? if ($limit == 100) : echo "selected";
                                endif; ?>>100</option>
            <option value="1000" <? if ($limit == 1000) : echo "selected";
                                    endif; ?>>1000</option>
        </select>
        <?= $this->render_partial('partials/order'); ?>
    </fieldset>
</form>

<?= $this->render_partial('partials/demand_table'); ?>