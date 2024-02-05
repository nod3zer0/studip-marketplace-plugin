<?php
namespace Marketplace;

    // enum NodeType
    // {
    //     case LogicT;
    //     case ParenthesisOpenT;
    //     case ParenthesisCloseT;
    //     case PropertyT;
    // }

    abstract class Node
    {
        abstract public function getType(): String;
        abstract public function getSQL(): String;
    }

    class WordNode extends Node
    {
        public $value;

        public function __construct($value)
        {
            $this->value = $value;
        }

        public function getType(): String
        {
            return "PropertyT";
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

        public function getType(): String
        {
            return "PropertyT";
        }

        public function getSQL(): String
        {
            return "mp_tag.name LIKE \"" . $this->value . "\"";
        }
    }

    class AndNode extends Node
    {
        public function getType(): String
        {
            return "LogicT";
        }

        public function getSQL(): String
        {
            return "AND";
        }
    }

#t OR #f ( title:eeee OR title:fffff)

    class OrNode extends Node
    {
        public function getType(): String
        {
            return "LogicT";
        }

        public function getSQL(): String
        {
            return "OR";
        }
    }

    class NotNode extends Node
    {
        public function getType(): String
        {
            return "LogicT";
        }

        public function getSQL(): String
        {
            return "NOT";
        }
    }

    class OpenNode extends Node
    {
        public function getType(): String
        {
            return "ParenthesisOpenT";
        }

        public function getSQL(): String
        {
            return "(";
        }
    }

    class CloseNode extends Node
    {
        public function getType(): String
        {
            return "ParenthesisCloseT";
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

        public function getType(): String
        {
            return "PropertyT";
        }

        public function getSQL(): String
        {
            return "mp_demand." . $this->property . " LIKE \"" . $this->value . "\"";
        }
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
                } else if ($tokens[$i] == "AND") //ADN
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

        function peek_next_token(): ?Node
        {
            if ($this->tokenObjects) {
                return $this->tokenObjects[0];
            }
            return null;
        }
    }





    class SqlGenerator
    {
        public function generateSQL($query)
        {
            $tokenizer = new Tokenizer($query);
            $sql = "SELECT * FROM mp_demand LEFT JOIN mp_tag_demand ON mp_demand.id=mp_tag_demand.demand_id LEFT JOIN mp_tag ON mp_tag_demand.tag_id=mp_tag.id WHERE ";
            while ($token = $tokenizer->get_next_token()) {
                $next_token = $tokenizer->peek_next_token();
                if ($next_token && $next_token->getType() == "LogicT") { //do not add AND if logic token
                    $sql .= " " . $token->getSQL() . " " . $next_token->getSQL();
                    $tokenizer->get_next_token();//skip logic token
                } else if ($token->getType() == "ParenthesisOpenT") {
                    $sql .= " " . $token->getSQL();
                } else if ($next_token && $next_token->getType() == "ParenthesisCloseT") {
                    $sql .= " " . $token->getSQL();
                }else if($token->getType() == "LogicT") {
                    $sql .= " ". $token->getSQL();
                } else if ($next_token) {
                    $sql .= " ". $token->getSQL() . " AND";
                }else {
                    $sql .= " ". $token->getSQL();
                }
            }
            return $sql;
        }
    }

    $f = fopen('php://stdin', 'r');

    while ($line = fgets($f)) {
        $generator = new SqlGenerator();
        echo $generator->generateSQL($line);
    }

    fclose($f);
