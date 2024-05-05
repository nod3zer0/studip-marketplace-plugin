<?

namespace search;

/**
 * Class AdvancedSearch
 * @author  Rene Ceska <xceska06@stud.fit.vutbr.cz>
 * This class is used to generate a SQL query for an advanced search
 * The query can search for custom properties, tags, default properties and categories
 * The query can be limited to a specific marketplace
 * The query can be sorted by title, author or creation date
 * The query can be sorted in ascending or descending order
 * The query can be limited to a specific number of results
 */
class AdvancedSearch
{
    private $values = [];

    private $categories = [];

    private $default_properties_map = [
        "title" => "title",
        "created" => "mkdate",
        "date" => "mkdate",
        "description" => "description",
        "author" => "username"
    ];

    /***
     * Generates a SQL query for an advanced search
     * @param $custom_properties array of custom properties
     * @param $tags array of tags
     * @param $default_properties array of default properties
     * @param $selected_category_path string path to selected category
     * @param $categories array of categories
     * @param $marketplace_id string id of marketplace
     * @param $limit int limit of results
     * @param $order string order of results (e.g. mkdate_desc)
     * @return array [sql, values]
     */
    public function generateSQL($custom_properties, $tags, $default_properties, $selected_category_path, $categories, $marketplace_id = "", $limit, $order)
    {
        $this->categories = $categories;
        $output = "LEFT JOIN auth_user_md5 ON author_id = user_id LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id WHERE ";

        if ($marketplace_id != "") {
            $output .= "mp_marketplace.id = ? AND ";
            $this->values[] = $marketplace_id;
        }

        foreach ($default_properties as $default_property) {
            $output .= $this->getDefaultPropertySQL($default_property);
            $output .= " AND ";
        }

        foreach ($custom_properties as $custom_property) {
            $output .= $this->getCustomPropertySQL($custom_property);
            $output .= " AND ";
        }

        foreach ($tags as $tag) {
            $output .= $this->generateTag($tag);
            $output .= " AND ";
        }
        if ($selected_category_path != "") {
            $category_id = $this->parseCategoryId($selected_category_path);
            $output .= $this->generateCategory($category_id);
        }

        // Check if the string ends with "AND"
        if (substr($output, -4) === "AND ") {
            // Remove "AND" from the end of the string
            $output = substr($output, 0, -4);
        }
        //sorting
        //remap attributes to prevent sql injection
        $attribute_map = [
            'title' => 'title',
            'author' => 'auth_user_md5.username',
            'mkdate' => 'mkdate'
        ];
        $order_map = [
            'asc' => 'ASC',
            'desc' => 'DESC'
        ];
        $order = explode('_', $order); // split into attribute and order

        $output .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id   ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?";
        $this->values[] = intval($limit);
        return [$output, $this->values];
    }

    public function parseCategoryId($path)
    {
        $path_array = explode("/", trim($path, '/'));
        $categories_copy = $this->categories;
        $categories_pointer = 0;
        $path_pointer = 0;
        $id = "";
        while ($categories_copy) {
            if ($categories_copy[$categories_pointer]["name"] == $path_array[$path_pointer]) {
                if ($path_pointer == count($path_array) - 1) {
                    $id = $categories_copy[$categories_pointer]["id"];
                    break;
                } else if ($path_pointer > count($path_array) - 1) {
                    throw new SearchException("Category doesn't exist!");
                }

                $categories_copy = $categories_copy[$categories_pointer]["subcategories"];
                $path_pointer++;
                $categories_pointer = 0;
                continue;
            }
            if ($categories_pointer >= count($categories_copy)) {
                throw new SearchException("Category doesn't exist!");
            }
            $categories_pointer++;
        }

        return $id;
    }

    private function getCustomPropertySQL($custom_property)
    {
        $output = "";
        switch ($custom_property["type"]) {
            case 1:
                $output = $this->generateCustomPropertyString($custom_property);
                break;
            case 2:
                $output = $this->generateCustomPropertyInt($custom_property);
                break;
            case 3:
                $output = $this->generateCustomPropertyDate($custom_property);
                break;
            case 4:
                //$this->generateCustomPropertyBool($parser); TODOs
                break;
            case 5:
                $output = $this->generateCustomPropertyString($custom_property);
                break;
        }

        return $output;
    }

    private function getDefaultPropertySQL($default_property)
    {
        $output = "";
        switch ($default_property["type"]) {
            case 1:
                $output = $this->generateDefaultPropertyString($default_property);
                break;
            case 2:
                $output = $this->generateDefaultPropertyInt($default_property);
                break;
            case 3:
                $output = $this->generateDefaultPropertyDate($default_property);
                break;
            case 4:
                //$this->generateCustomPropertyBool($parser); TODOs
                break;
            case 5:
                $output = $this->generateDefaultPropertyString($default_property);
                break;
            case 6:
                $output = $this->generateDefaultPropertyUserName($default_property);
                break;
        }

        return $output;
    }

    private function generateDefaultPropertyUserName($default_property)
    {
        $output = "";

        $output =  "CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) LIKE ?";
        $query = '%' . str_replace('*', '%', $default_property["value"]) . '%';
        $this->values[] = $query;

        return $output;
    }

    private function generateDefaultPropertyString($default_property)
    {
        $output = "";
        if (strlen(str_replace('*', '', $default_property["value"])) <= 3) {
            $output = "mp_demand." . $this->default_properties_map[$default_property["name"]] . " LIKE ?";
            $this->values[] = str_replace('*', '%', $default_property["value"]);
        } else {
            $output = "MATCH(mp_demand." . $this->default_properties_map[$default_property["name"]] . ") AGAINST (? IN BOOLEAN MODE)";
            $this->values[] = $default_property["value"];
        }

        return $output;
    }

    private function generateDefaultPropertyInt($default_property)
    {
        $output = "mp_demand." . $this->default_properties_map[$default_property["name"]];
        if ($default_property["compare_type"] == "equal") {
            $output .= " = ?";
        } else if ($default_property["compare_type"] == "greater") {
            $output .= " > ?";
        } else if ($default_property["compare_type"] == "less") {
            $output .= " < ?";
        } else if ($default_property["compare_type"] == "greater_equal") {
            $output .= " >= ?";
        } else if ($default_property["compare_type"] == "less_equal") {
            $output .= " <= ?";
        }
        $this->values[] = $default_property["value"];
        return $output;
    }

    private function generateDefaultPropertyDate($default_property)
    {
        $output = "(DATE(FROM_UNIXTIME(mp_demand." . $this->default_properties_map[$default_property["name"]] .  "))";
        if ($default_property["compare_type"] == "equal") {
            $output .= " = DATE(?))";
        } else if ($default_property["compare_type"] == "to") {
            $output .= " < DATE(?))";
        } else if ($default_property["compare_type"] == "from") {
            $output .= " > DATE(?))";
        } else if ($default_property["compare_type"] == "range") {
            $output .= " BETWEEN DATE(?) AND DATE(?))";
        }

        if ($default_property["compare_type"] == "range") {
            $this->values[] = $default_property["value_from"];
            $this->values[] = $default_property["value_to"];
        } else {

            $this->values[] = $default_property["value"];
        }

        return $output;
    }

    private function generateCustomPropertyString($custom_property)
    {
        $output = "";

        if (strlen(str_replace('*', '', $custom_property["value"])) <= 3) {
            $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE ( mp_custom_property.name LIKE ?  AND mp_property.value LIKE ? ) AND mp_demand.id = mp_property.demand_id)";
            $this->values[] = $custom_property["name"];
            $this->values[] = str_replace('*', '%', $custom_property["value"]);
        } else {
            $output = "EXISTS (
                SELECT 1
                FROM mp_property
                LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
                WHERE ( mp_custom_property.name LIKE ?  AND MATCH(mp_property.value) AGAINST(? IN BOOLEAN MODE) ) AND mp_demand.id = mp_property.demand_id)";
            $this->values[] = $custom_property["name"];
            $this->values[] = $custom_property["value"];
        }

        return $output;
    }

    private function generateCustomPropertyInt($custom_property)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE mp_demand.id = mp_property.demand_id AND ( mp_custom_property.name LIKE ?  AND mp_property.value";
        $this->values[] = $custom_property["name"];

        if ($custom_property["compare_type"] == "equal") {
            $output .= " = ? ))";
        } else if ($custom_property["compare_type"] == "greater") {
            $output .= " > ? ))";
        } else if ($custom_property["compare_type"] == "less") {
            $output .= " < ? ))";
        } else if ($custom_property["compare_type"] == "greater_equal") {
            $output .= " >= ? ))";
        } else if ($custom_property["compare_type"] == "less_equal") {
            $output .= " <= ? ))";
        }

        $this->values[] = $custom_property["value"];

        return $output;
    }


    private function generateCustomPropertyDate($custom_property)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE mp_demand.id = mp_property.demand_id AND ( mp_custom_property.name LIKE ?  AND STR_TO_DATE(mp_property.value, \"%Y-%m-%d\")";

        $this->values[] = $custom_property["name"];

        if ($custom_property["compare_type"] == "equal") {
            $output .= " = DATE(?)))";
        } else if ($custom_property["compare_type"] == "to") {
            $output .= " < DATE(?)))";
        } else if ($custom_property["compare_type"] == "from") {
            $output .= " > DATE(?)))";
        } else if ($custom_property["compare_type"] == "range") {
            $output .= " BETWEEN DATE(?) AND DATE(?)))";
        }

        if ($custom_property["compare_type"] == "range") {
            $this->values[] = $custom_property["value_from"];
            $this->values[] = $custom_property["value_to"];
        } else {

            $this->values[] = $custom_property["value"];
        }

        return $output;
    }

    private function generateTag($tag)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_tag_demand
            LEFT JOIN mp_tag ON mp_tag_demand.tag_id = mp_tag.id
            WHERE mp_tag.id LIKE ? AND mp_demand.id = mp_tag_demand.demand_id
        ) ";
        $this->values[] = $tag["id"];
        return $output;
    }

    private function generateCategory($category_id)
    {

        $output = "EXISTS (
            SELECT 1
            FROM mp_category_demand
            WHERE mp_category_demand.demand_id = mp_demand.id
            AND mp_category_demand.category_id LIKE ?
        ) ";
        $this->values[] = $category_id;
        return $output;
    }
}
