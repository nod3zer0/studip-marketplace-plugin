Order by:
<select name="order" style="width: 200px;">
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