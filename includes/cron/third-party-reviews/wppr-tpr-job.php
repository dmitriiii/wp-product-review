<?
function wppr_tpr_parse_plan()
{
    $product_post_map = wppr_get_product_post_map('vpn');
    $portal_options = get_field('third_party_review_portals', 'option');
    $count = 0;

    foreach ($product_post_map as $map) {
        $pid = (int) $map['vpnid'];
        $link_map = get_field('third_party_review_portal_links', $map['pid']);
        if (!count($link_map)) continue;
        foreach ($portal_options as $portal_option) {

            if (!$link_map[$portal_option['portal_name'] . '_link']) continue;
            $count++;

            $source = $portal_option['portal_name'];
            $url = $link_map[$portal_option['portal_name'] . '_link'];

            wp_schedule_single_event(time() + 300 * $count, 'wppr_third_party_review_job', [
                $pid,
                $source,
                $url
            ]);
        }
    }
}
add_action('wppr_third_party_reviews_cron', 'wppr_tpr_parse_plan');

function wppr_tpr_parse_job(
    $pid,
    $source,
    $url
) {
    include_once WPPR_PATH . '/includes/class-wppr-review-score.php';
    $parser = new WPPR_TPR_Parser();
    $scores_db = new WPPR_Review_Scores();
    $portal_options = get_field('third_party_review_portals', 'option');
    [$selected_option] = array_values(array_filter($portal_options, function ($opts) use ($source) {
        return $opts['portal_name'] === $source;
    }));

    if (!$selected_option) return;

    $votes_selector = $selected_option['xpath_selector_for_votes'];
    $score_selector = $selected_option['xpath_selector_for_scores'];
    $score_base = $selected_option['scores_base'] ? $selected_option['scores_base'] : 5;

    [$score, $votes] = $parser->parse($url, $score_selector, $votes_selector);

    //var_dump($score);
    //var_dump($votes);

    if ($score == -1 || $votes == -1) throw new Exception("CSS selectors are wrong", 1);

    $score = $score * 100 / $score_base;

    $scores_db->replace([
        'pid' => $pid,
        'rating' => $score,
        'source' => $selected_option['portal_label'],
        'url' => $url,
        'votes' => $votes,
    ]);
}
add_action('wppr_third_party_review_job', 'wppr_tpr_parse_job', 10, 3);


class WPPR_TPR_Parser
{
    function parse($url, $score_selector, $votes_selector)
    {
        $html = $this->getHtml($url);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DomXPath($doc);
        $score = $this->get_score($xpath, $score_selector);
        $votes = $this->get_votes($xpath, $votes_selector);

        return [$score, $votes];
    }

    private function getHtml($url)
    {
        $curl = curl_init();

        if (!$curl) {
            die("Is not working");
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($curl);
        //var_dump(file_get_contents($url));
        //var_dump($html);
        return $html;
    }

    private function get_score($xpath, $score_selector)
    {
        $nodeList = $xpath->query($score_selector);
        //var_dump($nodeList);
        //var_dump($score_selector);
        if (!count($nodeList)) return -1;
        $score = $this->get_float($nodeList[0]->firstChild->textContent);
        return $score;
    }

    private function get_votes($xpath, $votes_selector)
    {
        $nodeList = $xpath->query($votes_selector);
        //var_dump($nodeList);
        //var_dump($votes_selector);
        if (!count($nodeList)) return -1;
        $score = $this->get_int($nodeList[0]->firstChild->textContent);
        return $score;
    }

    private function get_float($str)
    {
        if (strstr($str, ",")) {
            $str = str_replace(".", "", $str); // replace dots (thousand seps) with blancs
            $str = str_replace(",", ".", $str); // replace ',' with '.'
        }

        if (preg_match("#([0-9\.]+)#", $str, $match)) { // search for number that may contain '.'
            return floatval($match[0]);
        } else {
            return floatval($str); // take some last chances with floatval
        }
    }

    private function get_int($str)
    {
        return abs((int) filter_var($str, FILTER_SANITIZE_NUMBER_INT));
    }
}
