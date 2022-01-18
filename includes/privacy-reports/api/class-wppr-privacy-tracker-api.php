<?
class WPPR_Privacy_Tracker_API
{
    function __construct(
        WPPR_Privacy_Tracker &$tracker_db,
        WPPR_Privacy_Category &$category_db,
        WPPR_Privacy_Tracker_Category &$tracker_category_db,
    ) {
        $this->tracker_db = $tracker_db;
        $this->category_db = $category_db;
        $this->tracker_category_db = $tracker_category_db;
    }

    function update($trackers = [])
    {
        $cats = array_reduce($trackers, function ($carr, $tracker) {
            if (!isset($tracker['categories'])) return $carr;
            return array_unique(
                [...$carr, ...$tracker['categories']]
            );
        }, []);

        $this->update_categories($cats);
        $this->update_trackers($trackers);
        $this->bind_categories($trackers);
    }


    function update_trackers($trackers)
    {
        foreach ($trackers as $tracker) {
            $this->tracker_db->add($tracker);
        }
    }

    function update_categories($cats)
    {
        $db_cats_name = array_map(function ($cat) {
            return $cat['name'];
        }, $this->category_db->get_all_by_names($cats));

        foreach ($cats as $cat) {
            if (in_array($cat, $db_cats_name)) continue;
            $this->category_db->add($cat);
        }
    }

    function bind_categories($trackers)
    {
        foreach ($trackers as $tracker) {
            $this->bind_category($tracker['id'], $tracker['categories']);
        }
    }

    function bind_category($tracker_id, $cats)
    {
        $binds = $this->tracker_category_db->get_all_tracker_binds($tracker_id);

        $db_cats = array_filter($this->category_db->get_all_by_names($cats), function ($cat) use ($binds) {
            $find = array_filter($binds, function ($bind) use ($cat) {
                return $bind['category_id'] == $cat['id'];
            });
            return empty($find);
        });
        $db_cats_name = array_map(function ($cat) {
            return $cat['name'];
        }, $db_cats);

        foreach ($cats as $cat) {
            if (!in_array($cat, $db_cats_name)) {
                $this->update_categories([$cat]);
                $db_cat = $this->category_db->get_by_name($cat);
                $category_id = $db_cat['id'];
            } else {
                [$db_cat] = array_filter($db_cats, function ($db_cat) use ($cat) {
                    return $cat == $db_cat['name'];
                });
                $category_id = $db_cat['id'];
            }

            $this->tracker_category_db->insert($tracker_id, $category_id);
        }
    }

    function unbind_category($tracker_id, $cats)
    {
        $binds = $this->tracker_category_db->get_all_tracker_binds($tracker_id);

        $db_cats = array_filter($this->category_db->get_all_by_names($cats), function ($cat) use ($binds) {
            $find = array_filter($binds, function ($bind) use ($cat) {
                return $bind['category_id'] == $cat['id'];
            });
            return empty($find);
        });

        $db_cats_name = array_map(function ($cat) {
            return $cat['name'];
        }, $db_cats);

        foreach ($cats as $cat) {
            if (!in_array($cat, $db_cats_name)) {
                $this->update_categories([$cat]);
                $db_cat = $this->category_db->get_by_name($cat);
                $category_id = $db_cat['id'];
            } else {
                [$db_cat] = array_filter($db_cats, function ($db_cat) use ($cat) {
                    return $cat == $db_cat['name'];
                });
                $category_id = $db_cat['id'];
            }

            $this->tracker_category_db->insert($tracker_id, $category_id);
        }
    }
}
