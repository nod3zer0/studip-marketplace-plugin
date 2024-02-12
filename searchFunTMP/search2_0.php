<?php

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

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
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

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
}





class Parser
{
    private $tokenObjects = array();
    private $query;

    private $default_properties = [
        "title" => "title",
        "date" => "date",
    ];

    private $custom_properties = [
        "test1" => "test1",
        "test2" => "test2",
    ];

    public function __construct($query)
    {
        $this->query = $query;
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

        $charactersToReplace = ["(", ")", "&", "|", "!", ">", "<", ">=", "<=", "=", ":"];
        $replaceWith = [" ( ", " ) ", " & ", " | ", " ! ", " > ", " < ", " >= ", " <= ", " = ", " : "];
        $result = str_replace($charactersToReplace, $replaceWith, $this->query);
        $tokens =  explode(" ", $result);


        for ($i = 0; $i < count($tokens); $i++) {

            $tokens[$i] = trim($tokens[$i]);
            if ($tokens[$i] == "") {
                continue;
            } else if (substr($tokens[$i][0], 0, 1) == "#") //tags
            {
                $this->tokenObjects[] = new TagToken(substr($tokens[$i], 1));
            } else if ($tokens[$i] == "AND" || $tokens[$i] == "&") //ADN
            {
                $this->tokenObjects[] = new AndToken();
            } else if ($tokens[$i] == "OR" || $tokens[$i] == "|") //OR
            {
                $this->tokenObjects[] = new OrToken();
            } else if ($tokens[$i] == "NOT" || $tokens[$i] == "!") //NOT
            {
                $this->tokenObjects[] = new NotToken();
            } else if ($tokens[$i] == "(") //(
            {
                $this->tokenObjects[] = new OpenToken();
            } else if ($tokens[$i] == ")") //)
            {
                $this->tokenObjects[] = new CloseToken();
            } else if ($tokens[$i] == "=") //=
            {
                $this->tokenObjects[] = new EqualToken();
            } else if ($tokens[$i] == ":") {
                $this->tokenObjects[] = new ColonToken();
            } else if ($tokens[$i] == ">") //>
            {
                $this->tokenObjects[] = new GreaterToken();
            } else if ($tokens[$i] == "<") //<
            {
                $this->tokenObjects[] = new LessToken();
            } else if ($tokens[$i] == ">=") //>=
            {
                $this->tokenObjects[] = new GreaterEqualToken();
            } else if ($tokens[$i] == "<=") //<=
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
            } else if (preg_match('/^[a-zA-Z0-9_]+$/', $tokens[$i])) //string
            {
                if (isset($this->default_properties[$tokens[$i]])) {
                    $this->tokenObjects[] = new DefaultPropertyToken($tokens[$i]);
                } else if (isset($this->custom_properties[$tokens[$i]])) {
                    $this->tokenObjects[] = new CustomPropertyToken($tokens[$i]);
                } else {
                    $this->tokenObjects[] = new StringToken($tokens[$i]);
                }
            } else {
                throw new Exception("Invalid token: " . $tokens[$i]);
            }
        }
    }
}

class SqlGenerator
{
    public $values = [];
    public $numberOfBrackets = 0;
    public function generate($query)
    {
        $parser = new Parser($query);

        $output = "";

        $output = $this->generateStart($parser);

        return [$output, $this->values];
    }

    public function generateStart($parser)
    {
        $output = "LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id LEFT JOIN mp_property ON mp_property.demand_id=mp_demand.id LEFT JOIN mp_custom_property ON mp_custom_property.id=mp_property.custom_property_id WHERE ";
        $output  .= $this->generateExpression($parser);
        return $output;
    }


    public function generateExpression($parser)
    {
        echo "generateExpression\n";
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
        } else if (!$token) {
            return $output;
        } else {
            throw new Exception("Invalid token: " . $parser->peekNextToken()->getValue());
        }
        return $output;
    }

    // return "mp_tag.name LIKE ?";
    public function generateTag($parser)
    {
        echo "generateTag\n";
        $output = "mp_tag.name LIKE ? ";
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
        echo "generateLogic\n";

        $output = "";
        $token = $parser->peekNextToken();
        if ($token instanceof AndToken) {
            $output .= $this->generateAnd($parser);
        } else if ($token instanceof OrToken) {
            $output .= $this->generateOr($parser);
        } else if ($token instanceof NotToken) {
            $output .= $this->generateNot($parser);
        } else if ($token instanceof OpenToken) {
            $output .= $this->generateOpen($parser);
        } else if ($token instanceof CloseToken) {
            $output .= $this->generateClose($parser);
        } else {
            throw new Exception("Invalid token: " . $token->getValue());
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
        echo "generateOr\n";
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
        echo "generateOpen\n";
        $this->numberOfBrackets++;
        $output = "";
        $parser->getNextToken();
        $output .= " ( ";
        $output .= $this->generateExpression($parser);
        return $output;
    }

    public function generateClose($parser)
    {
        echo "generateClose\n";
        if ($this->numberOfBrackets == 0) {
            throw new Exception("Invalid token: " . $parser->peekNextToken()->getValue());
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

    public function generateDefaultProperty($parser)
    {
        $output = "mp_demand." . $parser->getNextToken()->getValue() . " LIKE ? ";


        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new Exception("Invalid token: " . $parser->peekNextToken());
        }
        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof ValueToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
        } else {
            throw new Exception("Invalid token: " . $parser->peekNextToken());
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

    public function generateCustomProperty($parser)
    {
        $output = "( mp_custom_property.name LIKE ? AND mp_property.value LIKE ? )";
        $this->values[] = $parser->getNextToken()->getValue();

        if (!($parser->peekNextToken() instanceof ColonToken) && !($parser->peekNextToken() instanceof EqualToken)) {
            throw new Exception("Invalid token: " . $parser->peekNextToken());
        }
        $parser->getNextToken();

        if ($parser->peekNextToken() instanceof ValueToken) {
            $this->values[] =  $parser->getNextToken()->getValue();
        } else {
            throw new Exception("Invalid token: " . $parser->peekNextToken());
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
        //TODO
        return "";
    }
}


$f = fopen('php://stdin', 'r');

while ($line = fgets($f)) {
    $generator = new SqlGenerator();
    echo $generator->generate($line)[0];
}

fclose($f);
