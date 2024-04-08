<?

use Studip\Button; ?>


<input type="hidden" id="attributes_url" value="<?= $controller->link_for('search/get_attributes', $marketplace_id) ?>">

<form class="default collapsable" action="<?= $controller->link_for('search/index', $marketplace_id) ?>" method="get">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <div>
            <label class="required">
                search
            </label>
            <div id="search_input">
                <search_input :categories="<?= str_replace("\"", "'", $categories) ?>" :value="'<?= $query ?>'" :marketplace_id="'<?= $marketplace_id ?>'" :attributes_url="'<?= $controller->link_for('search/get_attributes', $marketplace_id) ?>'" />

                <!-- <input type="text" name="search-query" required value="" id="search_input" v-model="search" @input="OnChange" @keydown.tab.prevent="OnTab" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp"> -->

                <!-- <ul v-show="isOpen" class="autocomplete-results">
                    <li :class="{ 'is-active': i === arrowCounter }" @click="setResult(result)" v-for="(result, i) in results_render" :key="i" class="autocomplete-result">
                        {{ result }}
                    </li>
                </ul>  -->

            </div>
            <?= Button::create('Search') ?>
            <select name="limit" style="width: 60px;">
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
        </div>
    </fieldset>


    <footer data-dialog-button>

    </footer>
</form>

<?= $this->render_partial('partials/demand_table'); ?>