 <?php


    enum NodeType
    {
        case LogicT;
        case ParenthesisT;
        case PropertyT;
    }

    abstract class Node
    {
        abstract public function getType(): NodeType;
        abstract public function getSQL(): String;
    }

    class WordNode extends Node
    {
        public $value;

        public function __construct($value)
        {
            $this->value = $value;
        }

        public function getType(): NodeType
        {
            return NodeType::PropertyT;
        }

        public function getSQL(): String
        {
            return $this->value;
        }
    }

    class TagNode extends Node
    {
        public $value;

        public function __construct($value)
        {
            $this->value = $value;
        }

        public function getType(): NodeType
        {
            return NodeType::PropertyT;
        }

        public function getSQL(): String
        {
            return "mp_tag.name LIKE " . $this->value;
        }
    }

    class AndNode extends Node
    {
        public function getType(): NodeType
        {
            return NodeType::LogicT;
        }

        public function getSQL(): String
        {
            return "\AND";
        }
    }

    class OrNode extends Node
    {
        public function getType(): NodeType
        {
            return NodeType::LogicT;
        }

        public function getSQL(): String
        {
            return "OR";
        }
    }

    class NotNode extends Node
    {
        public function getType(): NodeType
        {
            return NodeType::LogicT;
        }

        public function getSQL(): String
        {
            return "NOT";
        }
    }

    class OpenNode extends Node
    {
        public function getType(): NodeType
        {
            return NodeType::ParenthesisT;
        }

        public function getSQL(): String
        {
            return "(";
        }
    }

    class CloseNode extends Node
    {
        public function getType(): NodeType
        {
            return NodeType::ParenthesisT;
        }

        public function getSQL(): String
        {
            return ")";
        }
    }

    class DefaultPropertyNode extends Node
    {
        public $property;
        public $value;

        public function __construct($property, $value)
        {
            $this->property = $property;
            $this->value = $value;
        }

        public function getType(): NodeType
        {
            return NodeType::PropertyT;
        }

        public function getSQL(): String
        {
            return "mp_demand." . $this->property . " LIKE " . $this->value;
        }
    }

    abstract class Token
    {
    }

    class TagToken extends Token
    {
    }

    class AndToken extends Token
    {
    }

    class OrToken extends Token
    {
    }

    class NotToken extends Token
    {
    }

    class OpenToken extends Token
    {
    }

    class CloseToken extends Token
    {
    }

    class CustomStringToken extends Token
    {
    }

    class KeywordToken extends Token
    {
    }

    class EqualToken extends Token
    {
    }

    class QuotationToken extends Token
    {
    }

    class ColonToken extends Token
    {
    }

    class Tokenizer
    {
        private $tokenObjects = [];
        private $query;

        private $default_properties = [
            "title" => "title",
            "date" => "date",
        ];

        public function __construct($query)
        {
            $this->query = $query;
            $this->tokenize();
        }

        public function tokenize()
        {
            $tokens =  explode(" ", $this->query);

            for ($i = 0; $i < count($tokens); $i++) {
                if (substr($tokens[$i][0], 0, 1) == "#") //tags
                {
                   $this->tokenObjects[] = new TagNode(substr($tokens[$i], 1));
                } else if ($tokens[$i] == "\AND") //ADN
                {
                    $this->tokenObjects[] = new AndNode();
                } else if ($tokens[$i] == "OR") //OR
                {
                    $this->tokenObjects[] = new OrNode();
                } else if ($tokens[$i] == "NOT") //NOT
                {
                    $this->tokenObjects[] = new NotNode();
                } else if ($tokens[$i] == "(") //(
                {
                    $this->tokenObjects[] = new OpenNode();
                } else if ($tokens[$i] == ")") //)
                {
                    $this->tokenObjects[] = new CloseNode();
                } else {
                    if (str_contains($tokens[$i], ":")) { //split by :
                        $tmp_token = explode(":", $tokens[$i]);
                        if (isset($this->default_properties[$tmp_token[0]])) {
                            $this->tokenObjects[] = new DefaultPropertyNode($tmp_token[0], $tmp_token[1]);
                        } else {
                            //TODO custom properties
                        }
                        $i++;
                    } else if ($i + 2 < count($tokens) && $tokens[$i + 1][0] == ":") {
                        if (isset($this->default_properties[$tokens[$i]])) {
                            $this->tokenObjects[] = new DefaultPropertyNode($tokens[$i], $tokens[$i + 2]);
                        } else {
                            //TODO custom properties
                        }
                        $i += 2;
                    } else {
                        $this->tokenObjects[] = new WordNode($tokens[$i]);
                    }
                }
            }
        }


        function get_next_token(): ?Node
        {
            if ($this->tokenObjects) {
                $token =  $this->tokenObjects[0];
                array_shift($this->tokenObjects);
                return $token;
            }
            return null;

        }
    }










    function convertQuery($query)
    {
        $query_array = explode(" ", $query);
        $query = "";
        $last_symbol_logic = false;
        foreach ($query_array as $key) {
            if (substr($key, 0, 1) == "#") //tags
            {
                $query .= " " . convert_tag($key);
            } else if ($key == "AND") //ADN
            {
                $query .= " " . convert_and($key);
                $last_symbol_logic = true;
            } else if ($key == "OR") //OR
            {
                $query .= " " . convert_or($key);
                $last_symbol_logic = true;
            } else if ($key == "NOT") //NOT
            {
                $query .= " " . convert_not($key);
                $last_symbol_logic = true;
            } else if ($key == "(") //(
            {
                $query .= " " . convert_open($key);
            } else if ($key == ")") //)
            {
                $query .= " " .  convert_close($key);
            } else if (str_contains($key, "=")) { //property TODO optimize
                $property = explode("=", $key);
                if (isset($default_properties[$property[0]])) {
                    $query .= " " .  convert_default_property($property[0], $property[0]);
                } else {
                    //TODO costum properties
                }
            }

            if (!$last_symbol_logic) {
                $query .= " AND";
            }
            $last_symbol_logic = false;
        }

        return $query;
    }

    function convert_tag($tag): string
    {
        $tag = substr($tag, 1);
        return "mp_tag.name LIKE " . $tag;
    }

    function convert_and($and): string
    {
        return "AND";
    }

    function convert_or($or): string
    {
        return "OR";
    }

    function convert_not($not): string
    {
        return "NOT";
    }

    function convert_open($open): string
    {
        return "(";
    }

    function convert_close($close): string
    {
        return ")";
    }

    function convert_default_property($property, $value): string
    {
        return "mp_demand." . $property . " LIKE " . $value;
    }

    $f = fopen('php://stdin', 'r');

    while ($line = fgets($f)) {
        $tokenizer = new Tokenizer($line);
        while ($token = $tokenizer->get_next_token()) {
            echo $token->getSQL();
        }
    }

    fclose($f);
