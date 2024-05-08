<?

/**
 * Advanced search plus - generator
 * generates SQL query from user query
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace search;

class SqlGenerator
{
    private $values = [];
    private $numberOfBrackets = 0;
    private $categories = [];

    // map of default properties to database columns
    private $default_properties_map = [
        "title" => "title",
        "created" => "mkdate",
        "date" => "mkdate",
        "description" => "description",
        "author" => "username",
    ];

    /**
     * @param $query string search query
     * @param $custom_properties array of custom properties
     * @param $marketplace_id string id of marketplace
     * @param $categories array of categories
     * @param $limit int limit of results
     * @param $order string order of results (e.g. mkdate_desc)
     * @return array [sql, values]
     */
    public function generateSQL($query, $custom_properties, $marketplace_id = "", $categories, $limit, $order)
    {

        $this->categories = $categories;

        //replace spaces in custom_properties with _
        foreach ($custom_properties as $key => $value) {
            $custom_properties[$key]["name"] = str_replace(" ", "_", $value["name"]);
        }
        $parser = new Parser($query, $custom_properties);

        $output = "";

        $output = $this->generateStart($parser, $marketplace_id);

        // maps proerties to database columns for sorting (to prevent SQL injection)
        $attribute_map = [
            'title' => 'title',
            'author' => 'auth_user_md5.username',
            'mkdate' => 'mkdate'
        ];
        // map to prevent SQL injection
        $order_map = [
            'asc' => 'ASC',
            'desc' => 'DESC'
        ];
        $order = explode('_', $order); // split into attribute and order


        $output .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?";
        $this->values[] = intval($limit);

        if ($this->numberOfBrackets != 0) {
            throw new SearchException("Invalid number of brackets!");
        }

        return [$output, $this->values];
    }
    /**
     * Generates start of SQL query
     * @param $parser Parser
     * @param $marketplace_id string id of marketplace
     * @return string
     */
    public function generateStart($parser, $marketplace_id)
    {
        $output = "LEFT JOIN auth_user_md5 ON author_id = user_id LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id WHERE ";

        if ($marketplace_id != "") {
            $output .= "mp_marketplace.id = ? AND ";
            $this->values[] = $marketplace_id;
        }

        $output  .= $this->generateExpression($parser);
        return $output;
    }

    /**
     * Generates expression of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateExpression($parser)
    {
        $output = "";
        $token = $parser->peekNextToken();
        if ($token instanceof TagToken) {
            $output .= $this->generateTag($parser);
        } else if ($token instanceof DefaultPropertyToken) {
            $output .= $this->generateDefaultProperty($parser);
        } else if ($token instanceof StringToken) {
            $output .= $this->generateString($parser);
        } else if ($token instanceof CustomPropertyToken) {
            $output .= $this->generateCustomProperty($parser);
        } else if ($token instanceof OpenToken) {
            $output .= $this->generateOpen($parser);
        } else if ($token instanceof CloseToken) {
            $output .= $this->generateClose($parser);
        } else if ($token instanceof NotToken) {
            $output .= $this->generateNot($parser);
        } else if (!$token) {
            return $output;
        } else {
            throw new SearchException("Invalid symbol: " . get_class($parser->peekNextToken()));
        }
        return $output;
    }

    /**
     * Generates tag part of SQL query
     * @param $parser Parser
     * @return string
     */
    public function generateTag($parser)
    {



        $output = "EXISTS (
            SELECT 1
            FROM mp_tag_demand
            LEFT JOIN mp_tag ON mp_tag_demand.tag_id = mp_tag.id
            WHERE mp_tag.name LIKE ? AND mp_demand.id = mp_tag_demand.demand_id
        ) ";
        $this->values[] = $parser->getNextToken()->getValue();

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
    /**
     * Generates logic part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateLogic($parser)
    {

        $output = "";
        $token = $parser->peekNextToken();
        if ($token instanceof AndToken) {
            $output .= $this->generateAnd($parser);
        } else if ($token instanceof OrToken) {
            $output .= $this->generateOr($parser);
        } else if ($token instanceof NotToken) {
            $output .= "AND " . $this->generateNot($parser);
        } else if ($token instanceof OpenToken) {
            $output .= $this->generateOpen($parser);
        } else if ($token instanceof CloseToken) {
            $output .= $this->generateClose($parser);
        } else {
            throw new SearchException("Invalid placement of symbol: " . get_class($token->getValue()));
        }

        return $output;
    }


    /**
     * Generates AND part of SQL query
     * @param $parser Parser
     * @return string
     */
    public function generateAnd($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " AND ";
        $output .= $this->generateExpression($parser);
        return $output;
    }
    /**
     * Generates OR part of SQL query
     * @param $parser Parser
     * @return string
     */
    public function generateOr($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " OR ";
        $output .= $this->generateExpression($parser);
        return $output;
    }
    /**
     * Generates NOT part of SQL query
     * @param $parser Parser
     * @return string
     */
    public function generateNot($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " NOT ";
        $output .= $this->generateExpression($parser);
        return $output;
    }
    /**
     * Generates open bracket part of SQL query
     * @param $parser Parser
     * @return string
     */
    public function generateOpen($parser)
    {
        $this->numberOfBrackets++;
        $output = "";
        $parser->getNextToken();
        $output .= " ( ";
        $output .= $this->generateExpression($parser);
        return $output;
    }
    /**
     * Generates close bracket part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateClose($parser)
    {
        if ($this->numberOfBrackets <= 0) {
            throw new SearchException("Invalid number of brackets!");
        }
        $this->numberOfBrackets--;
        $output = "";
        $parser->getNextToken();
        $output .= " ) ";
        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }



    /**
     * Generates custom property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateCustomPropertyString($parser)
    {
        $this->values[] = $parser->getNextToken()->getValue();
        $output = "";



        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After property must follow `:` or `=`!");
        }
        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof ValueToken) {
            //fulltext only works on strings with length >= 3, remove * characters for strlen
            if (strlen(str_replace('*', '', $parser->peekNextToken()->getValue())) <= 3) {
                $output = "EXISTS (
                SELECT 1
                FROM mp_property
                LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
                WHERE ( mp_custom_property.name LIKE ?  AND mp_property.value LIKE ? ) AND mp_demand.id = mp_property.demand_id)";
                //like uses & instead of * for wildcard
                $this->values[] = str_replace('*', '%', $parser->getNextToken()->getValue());
            } else {
                $output = "EXISTS (
                SELECT 1
                FROM mp_property
                LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
                WHERE ( mp_custom_property.name LIKE ?  AND MATCH(mp_property.value) AGAINST(? IN BOOLEAN MODE) ) AND mp_demand.id = mp_property.demand_id)";
                $this->values[] = $parser->getNextToken()->getValue();
            }
        } else {
            throw new SearchException("After `:` must follow searched key word!");
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
    /**
     * Generates custom property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateCustomPropertyInt($parser)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE mp_demand.id = mp_property.demand_id AND ( mp_custom_property.name LIKE ?  AND mp_property.value";
        $this->values[] = $parser->getNextToken()->getValue();

        if ($parser->peekNextToken() instanceof ColonToken) {
            $output .= " = ? )";
        } else if ($parser->peekNextToken() instanceof EqualToken) {
            $output .= " = ? )";
        } else if ($parser->peekNextToken() instanceof GreaterToken) {
            $output .= " > ? )";
        } else if ($parser->peekNextToken() instanceof LessToken) {
            $output .= " < ? )";
        } else if ($parser->peekNextToken() instanceof GreaterEqualToken) {
            $output .= " >= ? )";
        } else if ($parser->peekNextToken() instanceof LessEqualToken) {
            $output .= " <= ? )";
        } else {
            throw new SearchException("After property must follow comparison symbol (= , < , >, etc...)!");
        }
        // close EXISTS

        $output .= ")";

        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof IntToken || $parser->peekNextToken() instanceof FloatToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
        } else {
            throw new SearchException("Number properties only accept numbers!");
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
    /**
     * Generates custom property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateCustomPropertyDate($parser)
    {

        $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE mp_demand.id = mp_property.demand_id AND ( mp_custom_property.name LIKE ?  AND STR_TO_DATE(mp_property.value, \"%Y-%m-%d\")";

        $this->values[] = $parser->getNextToken()->getValue();

        if ($parser->peekNextToken() instanceof ColonToken) {
            $output .= " = DATE(?))";
        } else if ($parser->peekNextToken() instanceof EqualToken) {
            $output .= " = DATE(?))";
        } else if ($parser->peekNextToken() instanceof GreaterToken) {
            $output .= " > DATE(?))";
        } else if ($parser->peekNextToken() instanceof LessToken) {
            $output .= " < DATE(?))";
        } else if ($parser->peekNextToken() instanceof GreaterEqualToken) {
            $output .= " >= DATE(?))";
        } else if ($parser->peekNextToken() instanceof LessEqualToken) {
            $output .= " <= DATE(?))";
        } else {
            throw new SearchException("After date property must follow comparison symbol (= , < , >, etc...)!");
        }
        // close EXISTS
        $output .= ")";

        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof DateToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
        } else {
            throw new SearchException("After date property must follow date!");
        }
        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }
        return $output;
    }
    /**
     * Generates custom property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateCustomProperty($parser)
    {
        $output = "";
        switch ($parser->peekNextToken()->getType()) {
            case 1:
                $output = $this->generateCustomPropertyString($parser);
                break;
            case 2:
                $output = $this->generateCustomPropertyInt($parser);
                break;
            case 3:
                $output = $this->generateCustomPropertyDate($parser);
                break;
            case 4:
                //$this->generateCustomPropertyBool($parser); TODOs
                break;
            case 5:
                $output = $this->generateCustomPropertyString($parser);
                break;
        }

        return $output;
    }
    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    private function generateDefaultPropertyUserName($parser)
    {
        $output = "";
        $parser->getNextToken()->getValue(); //skip property name
        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After string property must follow `:` or `=` !");
        }
        $parser->getNextToken();

        //here is no fulltext index, so we can't use MATCH AGAINST
        if ($parser->peekNextToken() instanceof ValueToken) {

            $output =  "( CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) LIKE ? ) ";
            //like uses & instead of * for wildcard
            $query = '%' . str_replace('*', '%',  $parser->getNextToken()->getValue()) . '%';
            $this->values[] = $query;
        } else {
            throw new SearchException("After string property must follow string!");
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateDefaultPropertyString($parser)
    {
        $property_name = $this->default_properties_map[$parser->getNextToken()->getValue()];
        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After string property must follow `:` or `=` !");
        }
        $parser->getNextToken();


        if ($parser->peekNextToken() instanceof ValueToken) {

            //fulltext only works on strings with length >= 3, remove * characters for strlen
            if (strlen(str_replace('*', '', $parser->peekNextToken()->getValue())) <= 3) {
                $output = "mp_demand." . $property_name . " LIKE ? ";
                //like uses & instead of * for wildcard
                $this->values[] = str_replace('*', '%', $parser->getNextToken()->getValue());
            } else {
                $output = "MATCH(mp_demand." .    $property_name . ") AGAINST(? IN BOOLEAN MODE) ";
                $this->values[] =  $parser->getNextToken()->getValue();
            }
        } else {
            throw new SearchException("After string property must follow string!");
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateDefaultPropertyDate($parser)
    {
        $output = "(DATE(FROM_UNIXTIME(mp_demand." . $this->default_properties_map[$parser->getNextToken()->getValue()] . "))";

        if ($parser->peekNextToken() instanceof ColonToken) {
            $output .= " = DATE(?))";
        } else if ($parser->peekNextToken() instanceof EqualToken) {
            $output .= " = DATE(?))";
        } else if ($parser->peekNextToken() instanceof GreaterToken) {
            $output .= " > DATE(?))";
        } else if ($parser->peekNextToken() instanceof LessToken) {
            $output .= " < DATE(?))";
        } else if ($parser->peekNextToken() instanceof GreaterEqualToken) {
            $output .= " >= DATE(?))";
        } else if ($parser->peekNextToken() instanceof LessEqualToken) {
            $output .= " <= DATE(?))";
        } else {
            throw new SearchException("After date property must follow comparison symbol (=, >, <, etc..)!" . $parser->peekNextToken());
        }

        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof DateToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
        } else {
            throw new SearchException("After date property must follow date!" . $parser->peekNextToken());
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }

    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateDefaultProperty($parser)
    {
        $output = "";

        switch ($parser->peekNextToken()->getType()) {
            case 1:
                $output = $this->generateDefaultPropertyString($parser);
                break;
            case 2:
                // $output = $this->generateDefaultPropertyInt($parser); NOTE there is no int default property
                break;
            case 3:
                $output = $this->generateDefaultPropertyDate($parser);
                break;
            case 4:
                //$this->generateDefaultPropertyBool($parser); NOTE there is no int default property
                break;
            case 5:
                $output = $this->generateDefaultPropertyString($parser);
                break;
            case 6:
                $output = $this->generateDefaultPropertyCategory($parser);
                break;
            case 7:
                $output = $this->generateDefaultPropertyUserName($parser);
                break;
        }

        return $output;
    }
    /**
     * Parses category id from path
     * @param $path string path to category
     * @return string id of last category
     * @throws SearchException
     */
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
    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateDefaultPropertyCategory($parser)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_category_demand
            WHERE mp_category_demand.demand_id = mp_demand.id
            AND mp_category_demand.category_id LIKE ?
        ) ";
        //remove category token
        $parser->getNextToken();
        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After category property must follow `:` or `=` !");
        }
        $parser->getNextToken(); // remove colon

        if ($parser->peekNextToken() instanceof ValueToken) {
            $this->values[] =  self::parseCategoryId($parser->getNextToken()->getValue());
        } else {
            throw new SearchException("After string property must follow string!");
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }

    /**
     * Generates default property part of SQL query
     * @param $parser Parser
     * @return string
     * @throws SearchException
     */
    public function generateString($parser)
    {

        if (strlen(str_replace('*', '', $parser->peekNextToken()->getValue())) <= 3) {
            $output = "mp_demand.title LIKE ? OR mp_demand.description LIKE ?";
            //like uses & instead of * for wildcard
            $value =  str_replace('*', '%', $parser->getNextToken()->getValue());
            $this->values[] =  $value;
            $this->values[] =  $value;
        } else {
            $output = "MATCH(mp_demand.title) AGAINST(? IN BOOLEAN MODE) OR MATCH(mp_demand.description) AGAINST(? IN BOOLEAN MODE) ";
            $value =    $parser->getNextToken()->getValue();
            $this->values[] =  $value;
            $this->values[] =  $value;
        }

        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }

        return $output;
    }
}
