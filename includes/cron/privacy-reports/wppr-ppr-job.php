<?
function wppr_ppr_parse_plan()
{
    if (!get_field('enable_third_party_reviews', 'option')) return;

    $product_post_map = wppr_get_product_post_map('vpn');
    $count = 0;
    $day_count = 0;
    $day_group_size = ceil(count($product_post_map) / 20);

    foreach ($product_post_map as $map) {
        $vpn_id = $map['vpnid'];
        $link_map = get_field('third_party_review_portal_links', $map['pid']);

        if (!count($link_map) || !count(array_filter(array_values($link_map), function ($val) {
            return !!$val;
        }))) continue;

        $count++;
        if ($count % ($day_group_size + 1) == 0) {
            $count = 1;
            $day_count++;
        }

        wp_schedule_single_event(time() + 300 * $count + $day_count * 86400, 'wppr_third_party_review_job', [
            $vpn_id
        ]);
    }
}
add_action('wppr_third_party_reviews_cron', 'wppr_tpr_parse_plan');


function wppr_tpr_parse_job(
    $vpn_id
) {
    [$product_post_map] = array_values(array_filter(wppr_get_product_post_map('vpn'), function ($map) use ($vpn_id) {
        return $map['vpnid'] == $vpn_id;
    }));
    $portal_options = get_field('third_party_review_portals', 'option');
    $link_map = get_field('third_party_review_portal_links', $product_post_map['pid']);

    foreach ($portal_options as $portal_option) {

        if (!$link_map[$portal_option['portal_name'] . '_link']) continue;

        try {
            wppr_tpr_parse(
                $vpn_id,
                $portal_option['portal_name'],
                $link_map[$portal_option['portal_name'] . '_link']
            );
        } catch (\Throwable $th) {
        }
    }
}
add_action('wppr_third_party_review_job', 'wppr_tpr_parse_job', 10, 3);


function wppr_tpr_parse(
    $vpn_id,
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

    try {
        [$score, $votes] = $parser->parse($url, $score_selector, $votes_selector);
    } catch (\Throwable $th) {
        return;
    }
    

    if ($score == -1 || $votes == -1) throw new Exception("CSS selectors are wrong", 1);

    $score = $score * 100 / $score_base;

    $scores_db->replace([
        'pid' => $vpn_id,
        'rating' => $score,
        'source' => $selected_option['portal_label'],
        'url' => $url,
        'votes' => $votes,
    ]);
}


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
        return $html;
    }

    function getHtmlExt($url) {
        $url = urlencode($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.proxycrawl.com/?token=5iuX-qEmhDS-X12ENoIrEA&url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function get_score($xpath, $score_selector)
    {
        $nodeList = $xpath->query($score_selector);
        if (!count($nodeList)) return -1;
        $score = $this->get_float($nodeList[0]->firstChild->textContent);
        return $score;
    }

    private function get_votes($xpath, $votes_selector)
    {
        $nodeList = $xpath->query($votes_selector);
        if (!count($nodeList)) return -1;
        $score = $this->get_int($nodeList[0]->firstChild->textContent);
        return $score;
    }

    private function get_float($str)
    {
        if (strstr($str, ",")) {
            $str = str_replace(".", "", $str);
            $str = str_replace(",", ".", $str);
        }

        if (preg_match("#([0-9\.]+)#", $str, $match)) {
            return floatval($match[0]);
        } else {
            return floatval($str);
        }
    }

    private function get_int($str)
    {
        return abs((int) filter_var($str, FILTER_SANITIZE_NUMBER_INT));
    }
}
