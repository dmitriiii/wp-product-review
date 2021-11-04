<?
function wppr_tpr_parse()
{
    $inst = new WPPR_TPR_parser("https://www.capterra.com.de/software/166743/nordvpn");
    $inst->parse();
}


class WPPR_TPR_parser
{
    private $url = '';

    function __construct($url)
    {
    }

    function parse()
    {
        $doc = new DOMDocument();
        $doc->loadHTMLFile("https://www.capterra.com.de/software/166743/nordvpn");
        $xpath = new DomXPath($doc);
        $nodeList = $xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," review-stars__text ")]');
        if (!count($nodeList)) return;
        $score = $this->get_score($xpath);
        $votes = $this->get_votes($xpath);
        //var_dump($score);
        //var_dump($votes);
    }

    private function get_score($xpath)
    {
        $nodeList = $xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," review-stars__text ")]');
        if (!count($nodeList)) return -1;
        $score = $this->get_float($nodeList[0]->firstChild->textContent);
        return $score;
    }

    private function get_votes($xpath)
    {
        $nodeList = $xpath->query('.//*[@id="productHeaderInfo"]//*[contains(concat(" ",normalize-space(@class)," ")," review-stars ")]/following-sibling::span');
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

    private function write()
    {
    }
}
