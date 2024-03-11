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
</script>
<style>
    .hidden {
        display: none;
    }
</style>

<form>
    <div>
        <label for="title">Title</label>
        <input type="text" name="title[value]">
    </div>
    <div>
        <label for="description">Description</label>
        <input type="text" name="description[value]">
    </div>
    <div>
        <label for="created">created</label>
        <select class="date_select" name="<?php echo str_replace(" ", "_", $property['name']) . "[compare_type]"; ?>">
            <option value="to">to</option>
            <option value="from">from</option>
            <option value="equal">equal</option>
            <option value="range">range</option>
        </select>
        <span class="date_span">
            <input type="date" name="created[value]">
        </span>
        <span class="date_span_range hidden">
            from:
            <input type="date" name="created[range_value_from]" min="0" max="100" value="50">
            to:
            <input type="date" name="created[range_value_to]" min="0" max="100" value="50">
        </span>
    </div>
    <?php foreach ($properties as $property) : ?>
        <div id="property">
            <label for="<?php echo str_replace(" ", "_", $property['name']); ?>"><?php echo $property['name']; ?></label>
            <?php if ($property['type'] === '2') : //number
            ?>
                <select name="<?php echo str_replace(" ", "_", $property['name']) . "[compare_type]" ?>">
                    <option value="greater">&gt;</option>
                    <option value="less">
                        &lt; </option>
                    <option value="equal">=</option>
                    <option value="less_equal">
                        &lt;=</option>
                    <option value="greater_equal">&gt;=</option>
                </select>
                <input type="number" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>">
            <?php elseif ($property['type'] === '3') : //date
            ?>
                <select class="date_select" name="<?php echo str_replace(" ", "_", $property['name']) . "[compare_type]"; ?>">
                    <option value="to">to</option>
                    <option value="from">from</option>
                    <option value="equal">equal</option>
                    <option value="range">range</option>
                </select>

                <span class="date_span">
                    <input type="date" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>">
                </span>
                <span class="date_span_range hidden">
                    from:
                    <input type="date" id="<?php echo str_replace(" ", "_", $property['name']) . "_rangeValue"; ?>" name="<?php echo str_replace(" ", "_", $property['name']) . "[range_value_from]"; ?>" min="0" max="100" value="50">
                    to:
                    <input type="date" id="<?php echo str_replace(" ", "_", $property['name']) . "_rangeValue"; ?>" name="<?php echo str_replace(" ", "_", $property['name']) . "[range_value_to]"; ?>" min="0" max="100" value="50">
                </span>
            <?php else : ?>
                <input type="text" name="<?php echo str_replace(" ", "_", $property['name']) . "[value]"; ?>">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <h2> Tags </h2>
    <div id="search_tag_select">
        <search_tag_select :tags_input="'<?= $tags ?>'"></search_tag_select>
    </div>
    <h2> Categories </h2>
    <div id="search_category_select">
        <search_category_select :selected_path="''" :categories="<?= str_replace("\"", "'", $categories)  ?>"></search_category_select>
    </div>
    <button type="submit">Submit</button>
</form>