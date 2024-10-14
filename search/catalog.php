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
    "catalog" => new QueryParameter(
        displayName: "Catalog label",
        description: "Catalog label as you have in your own data.",
        required: true,
        type: "string"),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-catalog-availability
 */
function checkAvailabilityWithCatalogLabel(string $token, string $catalogLabel): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Search/Catalog/Availability" .
            "?catalog=" . urlencode($catalogLabel),
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
    <h1>Check catalog availability with a catalog label.</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-search-catalog-availability" target="_blank">Link
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
if (!empty($_GET["catalog"])) {
    // session is already started in checkToken.php
    $apiReturn = checkAvailabilityWithCatalogLabel($_SESSION["token"], $_GET["catalog"]);

    echo $apiReturn;
}