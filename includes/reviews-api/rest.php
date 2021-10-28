<?
function init_reviews_rest_api()
{
    add_action("wp_ajax_get_ext_reviews", "get_ext_reviews");
    add_action("wp_ajax_nopriv_get_ext_reviews", "get_ext_reviews");

    function get_ext_reviews()
    {
        if (!$_GET['id']) {
            wp_send_json_error('No id');
            return;
        }
        try {
            wp_send_json_success([
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "play.google.com",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://play.google.com/store/apps/details?id=ch.protonvpn.android&hl=de&gl=US",
                    "rating" => 100,
                    "votes" => 29065,
                    "lastUpdate" => "2021-10-27T03:35:41.209Z"
                ],
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "apple.com",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://apps.apple.com/de/app/protonvpn-unbeschr%C3%A4nktes-vpn/id1437005085",
                    "rating" => 76,
                    "votes" => 298,
                    "lastUpdate" => "2021-10-27T03:35:41.209Z"
                ],
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "facebook.com",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://www.facebook.com/ProtonVPN/",
                    "rating" => 10,
                    "votes" => 143,
                    "lastUpdate" => "2021-10-05T18:35:37.758Z"
                ],
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "trustpilot.com",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://at.trustpilot.com/review/protonvpn.com",
                    "rating" => 58,
                    "votes" => 120,
                    "lastUpdate" => "2021-10-27T03:35:41.209Z"
                ],
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "g2.com",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://www.g2.com/products/protonvpn/reviews",
                    "rating" => 86,
                    "votes" => 72,
                    "lastUpdate" => "2021-09-08T04:05:40.931Z"
                ],
                [
                    "item" => "cf-bqfHk5wkBamG2GMeQOgCK",
                    "source" => "mozilla.org",
                    "locale" => "de",
                    "group" => null,
                    "url" => "https://addons.mozilla.org/de/firefox/addon/protonvpn/",
                    "rating" => 25,
                    "votes" => 3,
                    "lastUpdate" => "2021-09-29T03:45:40.177Z"
                ]
            ]);
        } catch (\Throwable $th) {
            wp_send_json_error($th->getMessage());
        }
    }
}

init_reviews_rest_api();
