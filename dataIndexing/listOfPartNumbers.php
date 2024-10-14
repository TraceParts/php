<?php
// CheckToken.php starts the session
require_once '../utils/CheckToken.php';

require_once '../utils/RootApiUrl.php';
require_once '../utils/QueryParameter.php';
require_once '../utils/ApiResponse.php';
require_once '../utils/MyDecoder.php';

include "../utils/header.html";
include "../utils/navbar.html";
?>
<?php
$requiredParameters = [
    "partFamilyCode" => new QueryParameter(
        displayName: "Part family code",
        description: "TraceParts code of the product family.",
        required: true,
        type: "string"),
];
$optionalParameters = [
    "returnYourOwnCodes" => new QueryParameter(
        displayName: "Return your own codes",
        description: "If available, your own codes (i.e.: SKU, internal_code, Part_ID) are returned.",
        required: false,
        type: "boolean",
        defaultValue: false),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v1-search-partnumberlist
 */
function getListOfPartNumbers(string $token, string $partFamilyCode, ?bool $returnYourOwnCodes): ApiResponse
{
    $returnYourOwnCodesString = "";
    if (!empty($returnYourOwnCodes)) {
        // !empty checks for null and false so $returnYourOwnCodes is true here
        $returnYourOwnCodesString = "&returnYourOwnCodes=true";
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v1/Search/PartNumberList" .
            "?partFamilyCode=" . urlencode($partFamilyCode) .
            $returnYourOwnCodesString,
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
    <h1>List of part numbers</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v1-search-partnumberlist" target="_blank">Link
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
        <button type="submit">Get part numbers</button>
    </form>
<?php
if (!empty($_GET["partFamilyCode"])) {
    $returnYourOwnCodes = MyDecoder::decodeBoolValue($_GET["returnYourOwnCodes"]);
    // session is already started in checkToken.php
    $apiReturn = getListOfPartNumbers($_SESSION["token"], $_GET["partFamilyCode"], $returnYourOwnCodes);

    echo $apiReturn;
}
?>