<?
include_once 'JotForm.php';

function init_jotform_api()
{
  add_action("wp_ajax_get_jotform_sub", "so_wp_ajax_function");
  add_action("wp_ajax_nopriv_get_jotform_sub", "so_wp_ajax_function");

  function so_wp_ajax_function()
  {
    $priv_field = ["19Taxid12", "4aThe71", "4cEmail"];
    if (!$_GET['id']) {
      wp_send_json_error('No id');
      return;
    }
    try {
      $jotformAPI = new JotForm(get_field('jotform_api_key', 'option'));
      $subs = $jotformAPI->getFormSubmissions(210735615804049);
      $sub = NULL;
      foreach ($subs as $fsub) {
        if (count(array_filter($fsub["answers"], function ($answ) {
          return $answ['name'] == 'yourvpn' && $answ['answer'] == $_GET['id'];
        }))) {
          $sub = $fsub;
          break;
        }
      }

      if (!$sub) throw new Exception("Der VPN-Anbieter hat noch keine Daten für die Bewertung bereitgestellt.", 1);
      
      if ($sub && isset($sub['answers']))
        foreach ($sub['answers'] as &$answer) {
          if (in_array($answer['name'], $priv_field) && isset($answer['answer'])) $answer['answer'] = "**Response received, not public for data protection**";
          if (isset($answer['text'])) $answer['text'] = __($answer['text'], 'wp-product-review');
          if (isset($answer['answer']) && is_string($answer['answer'])) $answer['text'] = __($answer['text'], 'wp-product-review');
        }

      wp_send_json_success($sub);
    } catch (\Throwable $th) {
      wp_send_json_error($th->getMessage());
    }
  }
}

init_jotform_api();
