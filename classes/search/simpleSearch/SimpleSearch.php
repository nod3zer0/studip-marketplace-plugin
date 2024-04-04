<?

namespace search;

/**
 * Class SimpleSearch
 * author: Rene Ceska <xceksa06@stud.fit.vutbr.cz>
 * This class is used to generate a SQL query for a simple search
 * The query searches for a given query in the title and description of a demand
 * The query can be limited to a specific marketplace
 * The query can be sorted by title, author or creation date
 * The query can be sorted in ascending or descending order
 * The query can be limited to a specific number of results
 */
class SimpleSearch
{
    /**
     * @param $query string search query
     * @param $marketplace_id string id of marketplace
     * @param $limit int limit of results
     * @param $order string order of results (e.g. mkdate_desc)
     */
    public function generateSQL(string $query, string $marketplace_id, int $limit, string $order)
    {
        $output = "LEFT JOIN auth_user_md5 ON author_id = user_id  LEFT JOIN mp_marketplace ON mp_demand.marketplace_id = mp_marketplace.id WHERE ";
        $values = [];
        if ($marketplace_id != "") {
            $output .= "mp_marketplace.id = ? AND ";
            $values[] = $marketplace_id;
        }
        if (strlen($query) < 3) {
            $output .= "mp_demand.title LIKE ? OR mp_demand.description LIKE ? ";
            $values[] = "%" . $query . "%";
            $values[] = "%" . $query . "%";
        } else {
            $output .= "MATCH(mp_demand.title) AGAINST(? IN BOOLEAN MODE) ";
            $values[] = $query;
            $output .= " OR MATCH(mp_demand.description) AGAINST(? IN BOOLEAN MODE) ";
            $values[] = $query;
        }

        //sorting
        //remap attributes to prevent sql injection
        $attribute_map = [
            'title' => 'title',
            'author' => 'auth_user_md5.username',
            'mkdate' => 'mkdate'
        ];
        $order_map = [
            'asc' => 'ASC',
            'desc' => 'DESC'
        ];
        $order = explode('_', $order); // split into attribute and order


        $output .= " Group by mp_demand.id, mp_demand.title, mp_demand.mkdate, mp_demand.chdate, mp_demand.author_id, mp_demand.id ORDER BY " . $attribute_map[$order[0]] . " " . $order_map[$order[1]] . " LIMIT ?";
        $values[] = intval($limit);
        return [$output, $values];
    }
}
