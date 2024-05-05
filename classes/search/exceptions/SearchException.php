<?

namespace search;

/**
 * Class SearchException
 * This class is special exception for search classes
 * @author  Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class SearchException extends \Exception
{
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
