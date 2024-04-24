<?

namespace Marketplace;

class CustomPropertySearchStudIp extends \SearchType
{

    protected $marketplace_id;
    protected $property_name;

    public function __construct($marketplace_id, $property_name)
    {
        $this->marketplace_id = $marketplace_id;
        $this->property_name = $property_name;
    }

    static public function get()
    {
        $class = get_called_class();
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs(func_get_args());
    }
    public function getTitle()
    {
        return " ";
    }

    public function getResults($input, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0)
    {
        $db = \DBManager::get();
        //$results = $db->fetchAll("", );
        //
        //
        // $stm = $db->prepare("SELECT title, description FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (title LIKE :query OR description LIKE :query)");
        $stm = $db->prepare("SELECT mp_property.value, mp_property.value FROM mp_property
                             LEFT JOIN mp_custom_property ON mp_custom_property.id = mp_property.custom_property_id
                             WHERE mp_custom_property.marketplace_id LIKE :marketplace_id
                             AND mp_property.value LIKE :query
                             AND  mp_custom_property.name LIKE :property_name
                               LIMIT 5");
        //$stm = $db->prepare("SELECT title, title FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (title LIKE :query OR description LIKE :query)");
        //$stm->execute(["marketplace_id" => $this->marketplace_id, "property_name" => $this->property_name, "query" => "%" . $input . "%"]);
        //$stm->execute(["marketplace_id" => $this->marketplace_id]);
        $stm->execute(["marketplace_id" => $this->marketplace_id,  "query" => "%" . $input . "%", "property_name" => $this->property_name]);
        $results =  $stm->fetchAll();

        array_push($results, [$input, $input]);

        return   $results;
        //  return [1 => ["test", "testing"], 2 => ["test", "testing",], 3 => ["test", "testing"]];
    }

    public function includePath()
    {
        return __file__;
    }
}
