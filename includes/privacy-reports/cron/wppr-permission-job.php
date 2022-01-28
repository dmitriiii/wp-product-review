<?
function wppr_permission_cron()
{
    if (!get_field('enable_privacy_reports', 'option')) return;

    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-permission.php';
    $permission_db = new WPPR_Privacy_Permission();
    $perms_map = json_decode(file_get_contents(WPPR_PATH . '/includes/privacy-reports/consts/android-permissions.json'), true);
    
    foreach ($perms_map as $value) {
        $permission_db->add($value);
    }
}

add_action('wppr_privacy_permission_cron', 'wppr_permission_cron');