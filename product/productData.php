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
    "partFamilyCode" => new QueryParameter(
        displayName: "Part family code",
        description: "TraceParts code of the product family.",
        required: true,
        type: "string"),
    "cultureInfo" => new QueryParameter(
        displayName: "Culture info",
        description: "Language of the texts.",
        required: true,
        type: "string"),
];
$optionalParameters = [
    "selectionPath" => new QueryParameter(
        displayName: "Selection path",
        description: "Selected configuration (to use in combination with partFamilyCode. If not provided, the product is loaded with default configuration).",
        required: false,
        type: "string"),
    "cadDetailLevel" => new QueryParameter(
        displayName: "CAD detail level",
        description: "Integer related to the level of detail included in the CAD model.",
        required: false,
        type: "string",
        defaultValue: "-1"),
];
$deprecatedParameters = [
    "currentStepNumber" => new QueryParameter(
        displayName: "[DEPRECATED] Current step number",
        description: "[DEPRECATED] Current step of configuration.",
        required: false,
        type: "integer",
        defaultValue: 0),
];
?>
<?php
/**
 * @return QueryParameter[]
 */
function getPossibleOptions(): array
{
    global $optionalParameters, $deprecatedParameters;
    return array_merge($optionalParameters, $deprecatedParameters);
}

?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v3-product-configure
 */
function getProductData(string $token, string $partFamilyCode, string $cultureInfo, array $options): ApiResponse
{
    // get the possible options to compare them later with the given ones
    $possibleOptions = getPossibleOptions();

    $optionsString = "";
    // loop through each possible options
    foreach ($possibleOptions as $optionKey => $optionValue) {
        // check if $optionKey is in the given options
        if (!empty($options[$optionKey])) {
            if ($optionValue->type == "integer") {
                // given values are all strings so if the $possibleOptions is an integer it tries to decode the given value
                $intValue = MyDecoder::decodeIntValue($options[$optionKey]);
                // the decoded value is null if fails, and it also checks if the given value is different of the default value
                if (!is_null($intValue) && $intValue != $optionValue->defaultValue) {
                    // if everything is good, it had this option to the $optionsString
                    $optionsString .= '&' . $optionKey . "=" . $intValue;
                }
            } elseif ($options[$optionKey] != $optionValue->defaultValue) {
                // checks if the given value is different of the default value
                // if everything is good, it is encoded and haded to the $optionsString
                $optionsString .= '&' . $optionKey . "=" . urlencode($options[$optionKey]);
            }
        }
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v3/Product/Configure" .
            "?partFamilyCode=" . urlencode($partFamilyCode) .
            "&cultureInfo=" . urlencode($cultureInfo) .
            $optionsString,
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
    <h1>Product data</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v3-product-configure" target="_blank">Link
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
        <h2>Deprecated parameters :</h2>
        <?php
        foreach ($deprecatedParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type='submit'>Get product data</button>
    </form>

<?php
if (!empty($_GET["partFamilyCode"]) && !empty($_GET["cultureInfo"])) {
    // session is already started in checkToken.php
    $apiReturn = getProductData($_SESSION["token"],
        $_GET["partFamilyCode"],
        $_GET["cultureInfo"],
        $_GET);

    echo $apiReturn;
}