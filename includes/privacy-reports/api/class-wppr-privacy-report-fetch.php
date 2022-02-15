<?
class WPPR_Privacy_Report_Fetch
{
    static private $url = "https://reports.exodus-privacy.eu.org/api/";

    static public function get_reports($app_name)
    {
        return self::get_data(self::$url . 'search/'. $app_name .'/details');
    }

    static public function get_trackers()
    {
        return self::get_data(self::$url . 'trackers');
    }

    static private function get_data($endpoint) {
        $token = get_field('exodus_api_key', 'option');
        if (!$token) return [];

        $req = curl_init($endpoint);

        if (!$req) {
            die("Is not working");
        }

        curl_setopt($req, CURLOPT_HTTPHEADER, [
            'Authorization: Token ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($req, CURLOPT_HTTPGET, true);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($req);
        if (!$resp) return [];
        else {
            $data = json_decode($resp, true);
            if (array_key_exists('dateil', $data)) return [];
            return $data;
        }
    }
}