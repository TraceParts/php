<?php
// CheckToken.php starts the session
require_once '../utils/CheckToken.php';

require_once '../utils/RootApiUrl.php';
require_once '../utils/QueryParameter.php';
require_once '../utils/ApiResponse.php';

include "../utils/header.html";
include "../utils/navbar.html";
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-supportedlanguages
 */
function getLanguagesList(string $token): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/SupportedLanguages",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $token
        ],
    ]);

    $result = new ApiResponse($curl);

    curl_close($curl);

    return $result;
}

?>
    <h1>Get the available languages for a token.</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-supportedlanguages" target="_blank">Link to the
            documentation (new page)</a></p>

<?php
// session is already started in checkToken.php
$apiReturn = getLanguagesList($_SESSION["token"]);

echo $apiReturn;
?>