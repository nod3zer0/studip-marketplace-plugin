<?

namespace Marketplace;

class SimpleTypeSearch extends \SearchType
{

    protected $marketplace_id;

    public function __construct($marketplace_id)
    {
        $this->marketplace_id = $marketplace_id;
    }

    static public function get()
    {
        $class = get_called_class();
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs(func_get_args());
    }
    public function getTitle()
    {
        return _("Search in titles and descriptions");
    }

    public function getResults($input, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0)
    {
        $db = \DBManager::get();
        //$results = $db->fetchAll("", );

        // $stm = $db->prepare("SELECT title, description FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (title LIKE :query OR description LIKE :query)");
        $stm = $db->prepare("SELECT title, title FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (title LIKE :query OR description LIKE :query) LIMIT 5");
        $stm->execute(["marketplace_id" => $this->marketplace_id, "query" => "%" . $input . "%"]);

        $results =  $stm->fetchAll();




        return   $results;
        //  return [1 => ["test", "testing"], 2 => ["test", "testing",], 3 => ["test", "testing"]];
    }

    public function includePath()
    {
        return __file__;
    }
}
