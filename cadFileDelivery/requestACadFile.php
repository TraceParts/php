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
    "userEmail" => new QueryParameter(displayName: "User email",
        description: "Email address associated to the CAD request event.",
        required: true,
        type: "string"),
    "cultureInfo" => new QueryParameter(displayName: "Culture info",
        description: "Language for the labels of the CAD formats.",
        required: true,
        type: "string"),
    "cadFormatId" => new QueryParameter(displayName: "CAD format ID",
        description: "TraceParts ID of the CAD format.",
        required: true,
        type: "integer"),
];
// One of the two pairs must be provided
$firstRequiredPair = [
    "partFamilyCode" => new QueryParameter(displayName: "Part family code",
        description: "TraceParts code of the product family.",
        required: false,
        type: "string"),
    "selectionPath" => new QueryParameter(displayName: "Selection path",
        description: "Selected configuration (to use in combination with partFamilyCode. If not provided, the product is loaded with default configuration).",
        required: false,
        type: "string"),
];
// One of the two pairs must be provided
$secondRequiredPair = [
    "classificationCode" => new QueryParameter(displayName: "Classification code",
        description: "TraceParts code of the classification (to use in combination with partNumber).",
        required: false,
        type: "string"),
    "partNumber" => new QueryParameter(displayName: "Part number",
        description: "Identifier of a product (to use in combination with classificationCode). Part number as stored in the TraceParts database.",
        required: false,
        type: "string"),
];
$optionalParameters = [
    "cadDetailLevelId" => new QueryParameter(displayName: "CAD detail level ID",
        description: "TraceParts ID of the optional detail level for the CAD model.",
        required: false,
        type: "string"),

];
$deprecatedParameters = [
    "languageId" => new QueryParameter(displayName: "[DEPRECATED] Language ID",
        description: "[DEPRECATED] TraceParts ID of the language (obsolete - please use cultureInfo).",
        required: false,
        type: "string"),

];
?>
<?php
/**
 * @return QueryParameter[]
 */
function getPossibleOptions(): array
{
    global $firstRequiredPair, $secondRequiredPair, $optionalParameters, $deprecatedParameters;
    return array_merge($firstRequiredPair, $secondRequiredPair, $optionalParameters, $deprecatedParameters);
}

?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/post_v3-product-cadrequest
 */
function requestACadFile(string $token, string $userEmail, string $cultureInfo, int $cadFormatId, array $options): ApiResponse
{
    // get the possible options to compare them later with the given ones
    $possibleOptions = getPossibleOptions();

    $optionsString = "";
    // loop through each possible options
    foreach ($possibleOptions as $optionKey => $optionValue) {
        // check if $optionKey is in the given options
        if (!empty($options[$optionKey] && $options[$optionKey] != $optionValue->defaultValue)) {
            // checks if the given value is different of the default value
            // if everything is good, it is encoded and haded to the $optionsString
            $optionsString .= ',"' . $optionKey . '":' . urldecode($options[$optionKey]);
        }
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v3/Product/cadRequest",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{" .
            '"userEmail":"' . urlencode($userEmail) . '",' .
            '"cultureInfo":"' . urlencode($cultureInfo) . '",' .
            '"cadFormatId":"' . $cadFormatId . '"' .
            $optionsString .
            "}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $token,
            "content-type: application/*+json"
        ],
    ]);

    $result = new ApiResponse($curl);

    curl_close($curl);

    return $result;
}

?>
    <h1>Request a CAD file</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/post_v3-product-cadrequest" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="post">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>

        <h2>Request to get a CAD file list can be done with:</h2>
        <ol>
            <li>partFamilyCode and selectionPath (without selectionPath, the default configuration is used)</li>
            <li>classificationCode and partNumber (both parameters are required)</li>
        </ol>
        <h2>First required pair :</h2>
        <?php
        foreach ($firstRequiredPair as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <h2>Second required pair :</h2>
        <?php
        foreach ($secondRequiredPair as $key => $value) {
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
        <button type="submit">Get CAD file list</button>
    </form>
<?php
if (!empty($_POST["userEmail"]) && !empty($_POST["cultureInfo"]) && !empty($_POST["cadFormatId"])) {
    // user wants to request a cad file -> not just the page loading

    // session is already started in checkToken.php
    $apiReturn = requestACadFile(
        $_SESSION["token"],
        $_GET["userEmail"],
        $_GET["cultureInfo"],
        $_GET["cadFormatId"],
        $_GET);

    echo $apiReturn;
}
