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
    "initialSelectionPath" => new QueryParameter(
        displayName: "Initial selection path",
        description: "Current SelectionPath from partFamilyInfo.",
        required: true,
        type: "string"),
    "symbol" => new QueryParameter(
        displayName: "Symbol",
        description: "Parameter code to update.",
        required: true,
        type: "string"),
    "value" => new QueryParameter(
        displayName: "Value",
        description: "New value to set for the related symbol. When the parameter \"editable\" is set to \"true\", the value must start with =",
        required: true,
        type: "string"),
];
$optionalParameters = [
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
 * @link https://developers.traceparts.com/v2/reference/post_v3-product-updateconfiguration
 */
function postProductConfiguration(string $token, string $partFamilyCode, string $cultureInfo, string $initialSelectionPath, string $symbol, string $value, array $options): ApiResponse
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
        CURLOPT_URL => RootApiUrl::$URL . "v3/Product/UpdateConfiguration" .
            "?partFamilyCode=" . urlencode($partFamilyCode) .
            "&cultureInfo=" . urlencode($cultureInfo) .
            "&initialSelectionPath=" . urlencode($initialSelectionPath) .
            "&symbol=" . urlencode($symbol) .
            "&value=" . urlencode($value) .
            $optionsString,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
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
    <h1>Product configuration</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/post_v3-product-updateconfiguration" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="post">
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
        <button type='submit'>Get product configuration</button>
    </form>
<?php
if (!empty($_POST["partFamilyCode"]) &&
    !empty($_POST["cultureInfo"]) &&
    !empty($_POST["initialSelectionPath"]) &&
    !empty($_POST["symbol"]) &&
    !empty($_POST["value"])) {
    // session is already started in checkToken.php
    $apiReturn = postProductConfiguration(
        $_SESSION["token"],
        $_POST["partFamilyCode"],
        $_POST["cultureInfo"],
        $_POST["initialSelectionPath"],
        $_POST["symbol"],
        $_POST["value"],
        $_POST);

    echo $apiReturn;
}








