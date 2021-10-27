<?
include_once 'JotForm.php';

function init_jotform_api()
{
  add_action("wp_ajax_get_jotform_sub", "get_jotform_sub");
  add_action("wp_ajax_nopriv_get_jotform_sub", "get_jotform_sub");

  function get_jotform_sub()
  {
    $priv_field = ["19Taxid12", "4aThe71", "4cEmail"];
    if (!$_GET['id']) {
      wp_send_json_error('No id');
      return;
    }
    try {
      $jotformAPI = new JotForm(get_field('jotform_api_key', 'option'));
      $default_answers = $jotformAPI->getFormQuestions(210735615804049);
      $subs = $jotformAPI->getFormSubmissions(210735615804049);
      $sub = null;
      $answers = null;
      foreach ($subs as $fsub) {
        if (count(array_filter($fsub["answers"], function ($answ) {
          return $answ['name'] == 'yourvpn' && $answ['answer'] == $_GET['id'];
        }))) {
          $sub = $fsub;
          break;
        }
      }

      if ($sub && isset($sub['answers']))  $answers = $sub['answers'];
      else $answers = $default_answers;

      foreach ($answers as &$answer) {
        if (in_array($answer['name'], $priv_field) && isset($answer['answer'])) $answer['answer'] = "**Response received, not public for data protection**";
        if (isset($answer['text'])) $answer['text'] = __($answer['text'], 'wp-product-review');
        if (isset($answer['answer']) && is_string($answer['answer'])) $answer['text'] = __($answer['text'], 'wp-product-review');
      }

      wp_send_json_success([...$answers]);
    } catch (\Throwable $th) {
      wp_send_json_error($th->getMessage());
    }
  }
}

init_jotform_api();
