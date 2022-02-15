<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-privacy-api.php';

class WPPR_Privacy_Tracker_API extends WPPR_Abstract_Privacy_API
{
    function __construct(
        WPPR_Privacy_Tracker $tracker_db,
        WPPR_Privacy_Category $category_db,
        WPPR_Privacy_Tracker_Category $tracker_category_db
    ) {
        $this->tracker_db = $tracker_db;
        $this->category_db = $category_db;
        $this->tracker_category_db = $tracker_category_db;
    }

    function update($raw_trackers = [])
    {
        $trackers = is_array($raw_trackers) ? $raw_trackers : [$raw_trackers];

        $cats = array_reduce($trackers, function ($carr, $tracker) {
            if (!isset($tracker['categories'])) return $carr;
            return array_unique(
                [...$carr, ...$tracker['categories']]
            );
        }, []);

        $this->update_categories($cats);
        $this->update_trackers($trackers);
        $this->bind_categories($trackers);
        $this->unbind_dead_categories($trackers);
    }


    function update_trackers($trackers)
    {
        $this->update_table($trackers, $this->tracker_db);
    }

    function update_categories($cats)
    {
        $this->update_light_table($cats, 'name', $this->category_db);
    }

    function bind_categories($trackers)
    {
        $this->bulk_update_bind_table(
            $trackers,
            'categories',
            'get_all_tracker_binds',
            $this->tracker_category_db,
            $this->category_db
        );
    }

    function unbind_dead_categories($trackers)
    {
        $this->bulk_garbage_bind_table(
            $trackers,
            'categories',
            'get_all_tracker_binds',
            $this->tracker_category_db,
            $this->category_db
        );
    }

    function get_tracker_detail_by_id($tracker_id)
    {
        $tracker = $this->tracker_db->get_by_id($tracker_id);
        $tracker['categories'] = array_map(function ($category) {
            return $category['name'];
        }, $this->get_categories_by_tracker_id($tracker_id));
        return $tracker;
    }

    function get_categories_by_tracker_id($tracker_id)
    {
        return $this->category_db->get_all_by_ids(array_map(function ($bind) {
            return $bind['category_id'];
        }, $this->tracker_category_db->get_all_tracker_binds($tracker_id)));
    }
}
