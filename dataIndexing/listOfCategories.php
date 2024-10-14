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
    "classificationCode" => new QueryParameter(
        displayName: "Classification code",
        description: "TraceParts code of the classification (to use in combination with partNumber).",
        required: true,
        type: "string"),
    "cultureInfo" => new QueryParameter(
        displayName: "Culture info",
        description: "Language of the labels.",
        required: true,
        type: "string"),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-categorylist
 */
function getListOfCategories(string $token, string $classificationCode, string $cultureInfo): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Search/CategoryList" .
            "?classificationCode=" . urlencode($classificationCode) .
            "&cultureInfo=" . urlencode($cultureInfo),
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

    <h1>List of products and categories</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-search-categorylist" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Get categories</button>
    </form>

<?php
if (!empty($_GET["classificationCode"]) && !empty($_GET["cultureInfo"])) {
    // session is already started in checkToken.php
    $apiReturn = getListOfCategories($_SESSION["token"], $_GET["classificationCode"], $_GET["cultureInfo"]);

    echo $apiReturn;
}
?>