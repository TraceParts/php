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
    "cultureInfo" => new QueryParameter(
        displayName: "Culture info",
        description: "Language of the labels.",
        required: true,
        type: "string"),
    "classificationCode" => new QueryParameter(
        displayName: "Classification code",
        description: "TraceParts code of the classification (to use in combination with partNumber).",
        required: true,
        type: "string"),
];
$optionalParameters = [
    "categoryCode" => new QueryParameter(
        displayName: "Category code",
        description: "Unique category code in the related classification.",
        required: false,
        type: "string"),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-search-productlist
 */
function getListOfProducts(string $token, string $classificationCode, string $cultureInfo, $categoryCode): ApiResponse
{
    $categoryCodeString = "";
    if (!empty($categoryCode)) {
        $categoryCodeString = "&categoryCode=" . urlencode($categoryCode);
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Search/ProductList" .
            "?classificationCode=" . urlencode($classificationCode) .
            "&cultureInfo=" . urlencode($cultureInfo) .
            $categoryCodeString,
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
    <h1>List of products</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-search-productlist" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <h2>Optional parameters :</h2>
        <?php
        foreach ($optionalParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Get products</button>
    </form>
<?php
if (!empty($_GET["classificationCode"]) && !empty($_GET["cultureInfo"])) {
    $categoryCode = null;
    if (!empty($_GET["categoryCode"])) {
        $categoryCode = $_GET["categoryCode"];
    }

    // session is already started in checkToken.php
    $apiReturn = getListOfProducts($_SESSION["token"], $_GET["classificationCode"], $_GET["cultureInfo"], $categoryCode);

    echo $apiReturn;
}
?>