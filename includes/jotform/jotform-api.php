<?
include_once 'JotForm.php';

function init_jotform_api()
{
  add_action("wp_ajax_get_jotform_sub", "so_wp_ajax_function");
  add_action("wp_ajax_nopriv_get_jotform_sub", "so_wp_ajax_function");

  function so_wp_ajax_function()
  {
    if (!$_GET['id']) {
      wp_send_json_error('No id');
      return;
    }
    try {
      $jotformAPI = new JotForm(get_field('jotform_api_key', 'option'));
      $sub = $jotformAPI->getSubmission($_GET['id']);
      wp_send_json_success($sub);
    } catch (\Throwable $th) {
      wp_send_json_error($th->getMessage());
    }
  }
}

init_jotform_api();
