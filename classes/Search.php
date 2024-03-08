<?php

namespace Marketplace;

class SearchException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

abstract class Token
{
}

class TagToken extends Token
{
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue(): String
    {
        return $this->value;
    }
}

abstract class LogicToken extends Token
{
}

class AndToken extends LogicToken
{
}

class OrToken extends LogicToken
{
}

class NotToken extends LogicToken
{
}

class OpenToken extends LogicToken
{
}

class CloseToken extends LogicToken
{
}


abstract class ComparisonToken extends Token
{
}
class EqualToken extends ComparisonToken
{
}

class ColonToken extends ComparisonToken
{
}

class GreaterToken extends ComparisonToken
{
}

class LessToken extends ComparisonToken
{
}

class GreaterEqualToken extends ComparisonToken
{
}

class LessEqualToken extends ComparisonToken
{
}

class PropertyToken extends Token
{
}

class DefaultPropertyToken extends PropertyToken
{
    public $value;
    public int $type;

    /**
     * @param string $value
     * @param int $type 1 - string, 2 - int, 3 - date, 4 - bool, 5 - text
     */
    public function __construct(string $value, int $type)
    {
        $this->value = $value;
        $this->type = $type;
    }
    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}

class ValueToken extends Token
{
}

class StringToken extends ValueToken
{
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
}



class IntToken extends ValueToken
{
    public $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
    public function getValue(): int
    {
        return $this->value;
    }
}

class DateToken extends ValueToken
{
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
}

class FloatToken extends ValueToken
{
    public $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }
    public function getValue(): float
    {
        return $this->value;
    }
}

class CustomPropertyToken extends PropertyToken
{

    public $value;
    public int $type;

    public function __construct(string $value, int $type)
    {
        $this->value = $value;
        $this->type = $type;
    }
    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}





class Parser
{
    private $tokenObjects = array();
    private $query;

    private $default_properties = [
        "title" => 1,
        "created" => 3,
        "date" => 3,
        "description" => 5,
        "category" => 6,
    ];

    private $custom_properties = [];

    public function __construct($query, $custom_properties)
    {
        $this->query = $query;
        $this->custom_properties = $custom_properties;
        $this->tokenize();
    }

    public function getNextToken()
    {
        return array_shift($this->tokenObjects);
    }

    public function peekNextToken()
    {
        if ($this->tokenObjects) {
            return $this->tokenObjects[0];
        } else {
            return null;
        }
    }

    public function tokenize()
    {

        $charactersToReplace = ["(", ")", "&", "|", "!", ">=", "<=", ">", "<", "=", ":"];
        $replaceWith = [" ( ", " ) ", " & ", " | ", " ! ", " GE ", " LE ", " G ", " L ", " E ", " : "];
        $result = str_replace($charactersToReplace, $replaceWith, $this->query);
        $tokens =  explode(" ", $result);

        $custom_properties_dict = [];
        //convert custom properties to dict name -> type
        foreach ($this->custom_properties as $property) {
            $custom_properties_dict[$property["name"]] = $property["type"];
        }

        //trim and remove empty tokens
        foreach ($tokens as $key => $value) {
            $tokens[$key] = trim($value);
            if ($tokens[$key] == "") {
                unset($tokens[$key]);
            }
        }
        //reindex array

        $tokens = array_values($tokens);

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i] == "") {
                continue;
            } else if (substr($tokens[$i][0], 0, 1) == "#") //tags
            {
                $this->tokenObjects[] = new TagToken(substr($tokens[$i], 1));
            } else if (strtolower($tokens[$i]) == "and" || $tokens[$i] == "&") //ADN
            {
                $this->tokenObjects[] = new AndToken();
            } else if (strtolower($tokens[$i]) == "or" || $tokens[$i] == "|") //OR
            {
                $this->tokenObjects[] = new OrToken();
            } else if (strtolower($tokens[$i]) == "not" || $tokens[$i] == "!") //NOT
            {
                $this->tokenObjects[] = new NotToken();
            } else if ($tokens[$i] == "(") //(
            {
                $this->tokenObjects[] = new OpenToken();
            } else if ($tokens[$i] == ")") //)
            {
                $this->tokenObjects[] = new CloseToken();
            } else if ($tokens[$i] == "E") //=
            {
                $this->tokenObjects[] = new EqualToken();
            } else if ($tokens[$i] == ":") {
                $this->tokenObjects[] = new ColonToken();
            } else if ($tokens[$i] == "G") //>
            {
                $this->tokenObjects[] = new GreaterToken();
            } else if ($tokens[$i] == "L") //<
            {
                $this->tokenObjects[] = new LessToken();
            } else if ($tokens[$i] == "GE") //>=
            {
                $this->tokenObjects[] = new GreaterEqualToken();
            } else if ($tokens[$i] == "LE") //<=
            {
                $this->tokenObjects[] = new LessEqualToken();
            } else if (preg_match('/^[0-9]+$/', $tokens[$i])) //int
            {
                $this->tokenObjects[] = new IntToken((int)$tokens[$i]);
            } else if (preg_match('/^[0-9]+\.[0-9]+$/', $tokens[$i])) //float
            {
                $this->tokenObjects[] = new FloatToken((float)$tokens[$i]);
            } else if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $tokens[$i])) //date
            {
                $this->tokenObjects[] = new DateToken($tokens[$i]);
            } else if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{2}$/', $tokens[$i])) {

                $this->tokenObjects[] = new DateToken((\DateTime::createFromFormat('d/m/y', $tokens[$i])->format('Y-m-d')));
            } else if ($tokens[$i][0] == '.'  && count($tokens) > ($i) && ($tokens[$i + 1] == ":" || $tokens[$i + 1] == "E" || $tokens[$i + 1] == "G" ||
                $tokens[$i + 1] == "L" || $tokens[$i + 1] == "LE" || $tokens[$i + 1] == "GE")) //string
            {
                $tokens[$i] = substr($tokens[$i], 1); //remove starting dot
                if (isset($this->default_properties[$tokens[$i]])) {
                    $this->tokenObjects[] = new DefaultPropertyToken($tokens[$i], $this->default_properties[$tokens[$i]]);
                } else if (isset($custom_properties_dict[$tokens[$i]])) {
                    $this->tokenObjects[] = new CustomPropertyToken($tokens[$i], $custom_properties_dict[$tokens[$i]]);
                }
            } else if (preg_match('/^[a-zA-Z0-9_*+\/]+$/', $tokens[$i])) {
                if (
                    $this->tokenObjects[array_key_last($this->tokenObjects)] instanceof StringToken
                ) {
                    $this->tokenObjects[array_key_last($this->tokenObjects)]->value .= " " . $tokens[$i];
                } else {
                    $this->tokenObjects[] = new StringToken($tokens[$i]);
                }
            } else {
                throw new SearchException("Invalid symbol: " . $tokens[$i]);
            }
        }
    }
}

class SqlGenerator
{
    private $values = [];
    private $numberOfBrackets = 0;
    private $categories = [];

    private $default_properties_map = [
        "title" => "title",
        "created" => "mkdate",
        "date" => "mkdate",
        "description" => "description",
    ];
    public function generateSQL($query, $custom_properties, $marketplace_id = "", $categories)
    {

        $this->categories = $categories;

        $parser = new Parser($query, $custom_properties);

        $output = "";

        $output = $this->generateStart($parser, $marketplace_id);

        $output .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id";


        if ($this->numberOfBrackets != 0) {
            throw new SearchException("Invalid number of brackets!");
        }

        return [$output, $this->values];
    }

    public function generateStart($parser, $marketplace_id)
    {
        $output = "LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id WHERE ";

        if ($marketplace_id != "") {
            $output .= "mp_marketplace.id = ? AND ";
            $this->values[] = $marketplace_id;
        }

        $output  .= $this->generateExpression($parser);
        return $output;
    }


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

    // return "mp_tag.name LIKE ?";
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



    public function generateAnd($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " AND ";
        $output .= $this->generateExpression($parser);
        return $output;
    }

    public function generateOr($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " OR ";
        $output .= $this->generateExpression($parser);
        return $output;
    }

    public function generateNot($parser)
    {
        $output = "";
        $parser->getNextToken();
        $output .= " NOT ";
        $output .= $this->generateExpression($parser);
        return $output;
    }

    public function generateOpen($parser)
    {
        $this->numberOfBrackets++;
        $output = "";
        $parser->getNextToken();
        $output .= " ( ";
        $output .= $this->generateExpression($parser);
        return $output;
    }

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




    public function generateCustomPropertyString($parser)
    {
        $output = "EXISTS (
            SELECT 1
            FROM mp_property
            LEFT JOIN mp_custom_property ON mp_property.custom_property_id = mp_custom_property.id
            WHERE ( mp_custom_property.name LIKE ?  AND MATCH(mp_property.value) AGAINST(? IN BOOLEAN MODE) ) AND mp_demand.id = mp_property.demand_id)";
        $this->values[] = $parser->getNextToken()->getValue();

        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After property must follow `:` or `=`!");
        }
        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof ValueToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
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
        //SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id LEFT JOIN mp_property ON mp_property.demand_id=mp_demand.id LEFT JOIN mp_custom_property ON mp_custom_property.id=mp_property.custom_property_id WHERE (mp_custom_property.name LIKE "prop4" AND STR_TO_DATE(mp_property.value, "%Y-%m-%d") = DATE("2024-02-20"))
        if ($parser->peekNextToken() instanceof LogicToken) {
            $output .= $this->generateLogic($parser);
        } else if (!$parser->peekNextToken()) { // check NULL
            return $output;
        } else {
            $output .= " AND " . $this->generateExpression($parser);
        }
        return $output;
    }

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

    public function generateDefaultPropertyString($parser)
    {
        $output = "MATCH(mp_demand." . $this->default_properties_map[$parser->getNextToken()->getValue()] . ") AGAINST(? IN BOOLEAN MODE) ";


        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new SearchException("After string property must follow `:` or `=` !");
        }
        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof ValueToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
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

    public function generateDefaultPropertyDate($parser)
    {
        $output = "(DATE(FROM_UNIXTIME(mp_demand." . $this->default_properties_map[$parser->getNextToken()->getValue()] . "))";

        if ($parser->peekNextToken() instanceof ColonToken) {
            $output .= " = DATE(?)";
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


    public function generateDefaultProperty($parser)
    {
        $output = "";

        switch ($parser->peekNextToken()->getType()) {
            case 1:
                $output = $this->generateDefaultPropertyString($parser);
                break;
            case 2:
                // $output = $this->generateDefaultPropertyInt($parser); TODO
                break;
            case 3:
                $output = $this->generateDefaultPropertyDate($parser);
                break;
            case 4:
                //$this->generateDefaultPropertyBool($parser); TODO
                break;
            case 5:
                $output = $this->generateDefaultPropertyString($parser);
                break;
            case 6:
                $output = $this->generateDefaultPropertyCategory($parser);
                break;
        }

        return $output;
    }

    public function parseCategoryId($path)
    {
        $path_array = explode("/", $path);
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
            if ($categories_pointer >= count($path_array)) {
                throw new SearchException("Category doesn't exist!");
            }
            $categories_pointer++;
        }

        return $id;
    }

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


    public function generateString($parser)
    {
        //TODO rewrite to fulltext search
        $output = "(INSTR(mp_demand.title, ? ) > 0 OR INSTR(mp_demand.description , ? ) > 0 )";
        $value = $parser->getNextToken()->getValue();
        $this->values[] = $value;
        $this->values[] = $value;

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


$f = fopen('php://stdin', 'r');

while ($line = fgets($f)) {
    $generator = new SqlGenerator();
    $result = $generator->generateSQL($line, ["test3" => 3]);
    echo $result[0];
    echo "\n";
    foreach ($result[1] as $value) {
        echo " " . $value;
    }
}

fclose($f);
