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
$requiredParameters = [
    "yourOwnCode" => new QueryParameter(
        displayName: "Your own code",
        description: "Non public string to call a configuration in the TraceParts database (i.e.: SKU, internal_code, Part_ID).",
        required: true,
        type: "string"),
    "catalog" => new QueryParameter(
        displayName: "Catalog label",
        description: "Catalog label as you have in your own data.",
        required: true,
        type: "string"),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-yourowncode-availability
 */
function checkAvailabilityWithYourOwnCode(string $token, string $catalogLabel, string $yourOwnCode): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Search/YourOwnCode/Availability" .
            "?yourOwnCode=" . urlencode($yourOwnCode) .
            "&catalog=" . urlencode($catalogLabel),
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
    <h1>Check a catalog availability with your own Code.</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-search-yourowncode-availability" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Check availability</button>
    </form>
<?php
if (!empty($_GET["catalog"]) && !empty($_GET["yourOwnCode"])) {
    // session is already started in checkToken.php
    $apiReturn = checkAvailabilityWithYourOwnCode($_SESSION["token"], $_GET["catalog"], $_GET["yourOwnCode"]);

    echo $apiReturn;
}
