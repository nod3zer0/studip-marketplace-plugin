<?

namespace Marketplace;

class DefaultPropertySearchStudIp extends \SearchType
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

        if ($this->property_name == "title") {
            $stm = $db->prepare("SELECT title, title FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (title LIKE :query) LIMIT 5");
        } else if ($this->property_name == "author") {
            $stm = $db->prepare("SELECT   CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname),   CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) FROM mp_demand LEFT JOIN auth_user_md5 ON author_id = user_id WHERE marketplace_id LIKE :marketplace_id AND (  CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) LIKE :query) LIMIT 5");
        } else if ($this->property_name == "description") {
            $stm = $db->prepare("SELECT description, description FROM mp_demand WHERE marketplace_id LIKE :marketplace_id AND (description LIKE :query) LIMIT 5");
        }
        $stm->execute(["marketplace_id" => $this->marketplace_id,  "query" => "%" . $input . "%"]);
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
