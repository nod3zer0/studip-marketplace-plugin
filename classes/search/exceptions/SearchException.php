<?

namespace search;

/**
 * Class SearchException
 * author: Rene Ceska <xceska06@stud.fit.vutbr.cz>
 * This class is special exception for search classes
 */
class SearchException extends \Exception
{
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
