<?

namespace search;

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
