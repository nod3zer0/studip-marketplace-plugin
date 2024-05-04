<?

namespace search;

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
        "author" => 7,
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
        $replaceWith = [" ( ", " ) ", " & ", " | ", " ! ", " ~GE ", " ~LE ", " ~G ", " ~L ", " ~E ", " : "];
        $result = str_replace($charactersToReplace, $replaceWith, $this->query);
        $tokens =  explode(" ", $result);

        $custom_properties_dict = [];
        //convert custom properties to dict name -> type
        foreach ($this->custom_properties as $property) {
            $custom_properties_dict[strtolower($property["name"])] = $property["type"];
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
            } else if ($tokens[$i] == "~E") //=
            {
                $this->tokenObjects[] = new EqualToken();
            } else if ($tokens[$i] == ":") {
                $this->tokenObjects[] = new ColonToken();
            } else if ($tokens[$i] == "~G") //>
            {
                $this->tokenObjects[] = new GreaterToken();
            } else if ($tokens[$i] == "~L") //<
            {
                $this->tokenObjects[] = new LessToken();
            } else if ($tokens[$i] == "~GE") //>=
            {
                $this->tokenObjects[] = new GreaterEqualToken();
            } else if ($tokens[$i] == "~LE") //<=
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
            } else if ($tokens[$i][0] == '.'  && count($tokens) > ($i)) //string
            {
                $tokens[$i] = substr($tokens[$i], 1); //remove starting dot
                if (isset($this->default_properties[strtolower($tokens[$i])])) {
                    $this->tokenObjects[] = new DefaultPropertyToken(strtolower($tokens[$i]), $this->default_properties[strtolower($tokens[$i])]);
                } else if (isset($custom_properties_dict[strtolower($tokens[$i])])) {
                    $this->tokenObjects[] = new CustomPropertyToken(strtolower($tokens[$i]), $custom_properties_dict[strtolower($tokens[$i])]);
                } else {
                    throw new SearchException("Invalid property: " . $tokens[$i]);
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
