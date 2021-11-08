<?
function wppr_tpr_parse_plan()
{
    include_once WPPR_PATH . '/includes/class-wppr-review-score.php';
    $scores_db = new WPPR_Review_Scores();

    $map = get_field('third_party_review_portals', 'option');

    foreach ($map as $data) {
        $parser = new WPPR_TPR_Parser();
        $pid = 6;
        $source = $data['portal_name'];
        $url = 'https://www.capterra.com.de/software/166743/nordvpn';
        $votes_selector = $data['xpath_selector_for_votes'];
        $score_selector = $data['xpath_selector_for_scores'];
        
        $score_base = $data['scores_base'] ? $data['scores_base'] : 5;

        [$score, $votes] = $parser->parse($url, $score_selector, $votes_selector);

        $score = $score * 100 / $score_base;

        $scores_db->replace([
            'pid' => $pid,
            'rating' => $score,
            'source' => $source,
            'url' => $url,
            'votes' => $votes,
        ]);

    }

}

add_action( 'wppr_third_party_reviews_cron', 'wppr_tpr_parse_plan' );

class WPPR_TPR_Parser
{
    function parse($url, $score_selector, $votes_selector)
    {
        $doc = new DOMDocument();
        $doc->loadHTMLFile($url);
        $xpath = new DomXPath($doc);
        $score = $this->get_score($xpath, $score_selector);
        $votes = $this->get_votes($xpath, $votes_selector);

        return [$score, $votes];
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
