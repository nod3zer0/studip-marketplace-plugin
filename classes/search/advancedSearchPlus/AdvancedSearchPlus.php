<?

/**
 * Advanced search plus
 * transpiles user query to SQL query
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */

namespace search;

class AdvancedSearchPlus
{
    /**
     * @param $query string search query
     * @param $custom_properties array of custom properties
     * @param $marketplace_id string id of marketplace
     * @param $categories array of categories
     * @param $limit int limit of results
     * @param $order string order of results (e.g. mkdate_desc)
     * @return array [sql, values]
     */
    public function generateSQL($query, $custom_properties, $marketplace_id = "", $categories, $limit, $order)
    {
        $generator = new SqlGenerator();

        return $generator->generateSQL($query, $custom_properties, $marketplace_id,  $categories, $limit, $order);
    }
}
//TODO