 <?php

    $default_properties = [
        "title" => "title",
        "date" => "date",
    ];

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
        echo convertQuery($line);
    }

    fclose($f);
