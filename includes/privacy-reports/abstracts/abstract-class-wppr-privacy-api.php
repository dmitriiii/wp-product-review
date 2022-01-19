<?
abstract class WPPR_Abstract_Privacy_API
{

    abstract function update($a);

    protected function update_table(array $data, WPPR_Abstract_Data_Table $table)
    {
        foreach ($data as $el) {
            $table->add($el);
        }
    }

    protected function update_light_table(array $data, string $name_field, WPPR_Abstract_Data_Table $table)
    {
        $db_data_name = array_map(function ($el) use ($name_field) {
            return $el[$name_field];
        }, $table->get_all_by_names($data));

        foreach ($data as $el) {
            if (in_array($el, $db_data_name)) continue;
            $table->add($el);
        }
    }

    protected function bulk_update_bind_table(
        array $data,
        string $fild_name,
        string $get_all_method_name,
        WPPR_Abstract_Bind_Table $bind_table,
        WPPR_Abstract_Data_Table $s_table
    ) {
        foreach ($data as $el) {
            $this->update_bind_table(
                $el['id'],
                $el[$fild_name],
                $get_all_method_name,
                $bind_table,
                $s_table
            );
        }
    }

    protected function update_bind_table(
        int $el_id,
        array $s_data,
        string $get_all_method_name,
        WPPR_Abstract_Bind_Table $bind_table,
        WPPR_Abstract_Data_Table $s_table
    ) {
        $binds = $bind_table->$get_all_method_name($el_id);

        $db_s_data = array_filter($s_table->get_all_by_names($s_data), function ($s_el) use ($binds, &$bind_table) {
            $find = array_filter($binds, function ($bind) use ($s_el, &$bind_table) {
                return $bind[$bind_table->get_right_name()] == $s_el['id'];
            });

            return empty($find);
        });

        $db_s_data_name = array_map(function ($s_el) {
            return $s_el['name'];
        }, $db_s_data);

        foreach ($s_data as $s_el) {
            if (!in_array($s_el, $db_s_data_name)) {
                $this->update_light_table([$s_el], 'name', $s_table);
                $db_s_el = $s_table->get_by_name($s_el);
                $s_el_id = $db_s_el['id'];
            } else {
                [$db_s_el] = [...array_filter($db_s_data, function ($db_s_el) use ($s_el) {
                    return $s_el == $db_s_el['name'];
                })];
                $s_el_id = $db_s_el['id'];
            }

            $bind_table->insert($el_id, $s_el_id);
        }
    }

    protected function bulk_garbage_bind_table(
        array $data,
        string $fild_name,
        string $get_all_method_name,
        WPPR_Abstract_Bind_Table $bind_table,
        WPPR_Abstract_Data_Table $s_table
    ) {
        foreach ($data as $el) {
            $this->garbage_bind_table(
                $el['id'],
                $el[$fild_name],
                $get_all_method_name,
                $bind_table,
                $s_table
            );
        }
    }

    function garbage_bind_table(
        int $el_id,
        array $s_data,
        string $get_all_method_name,
        WPPR_Abstract_Bind_Table $bind_table,
        WPPR_Abstract_Data_Table $s_table
    ) {
        $db_s_data = $s_table->get_all_by_names($s_data);
        $db_s_data_id = array_map(function ($s_el) {
            return $s_el['id'];
        }, $db_s_data);

        $binds = array_filter(
            $bind_table->$get_all_method_name($el_id),
            function ($bind) use ($db_s_data_id, &$bind_table) {
                return !in_array($bind[$bind_table->get_right_name()], $db_s_data_id);
            }
        );

        foreach ($binds as $bind) {
            $bind_table->delete($bind[$bind_table->get_left_name()], $bind[$bind_table->get_right_name()]);
        }
    }
}
