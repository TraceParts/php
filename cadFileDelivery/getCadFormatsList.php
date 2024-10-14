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
$requiredParameters = [
    "cultureInfo" => new QueryParameter(displayName: "Culture info",
        description: "Language for the labels of the CAD formats.",
        required: true,
        type: "string"),
];
?>
<?php
/**
 * @return QueryParameter[]
 */
function getPossibleOptions(): array
{
    global $firstRequiredPair, $secondRequiredPair;
    return array_merge($firstRequiredPair, $secondRequiredPair);
}

?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v3-product-caddataavailability
 */
function getCadFormatsList(string $token, string $cultureInfo, array $options): ApiResponse
{
    // get the possible options to compare them later with the given ones
    $possibleOptions = getPossibleOptions();

    $optionsString = "";
    // loop through each possible options
    foreach ($possibleOptions as $optionKey => $optionValue) {
        // check if $optionKey is in the given options
        if (!empty($options[$optionKey]) && $options[$optionKey] != $optionValue->defaultValue) {
            // checks if the given value is different of the default value
            // if everything is good, it is encoded and haded to the $optionsString
            $optionsString .= '&' . $optionKey . "=" . urlencode($options[$optionKey]);
        }
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v3/Product/CadDataAvailability" .
            "?cultureInfo=" . urlencode($cultureInfo) .
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
    <h1>Get CAD formats list</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v3-product-caddataavailability" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Request to get CAD formats list can manage two ways:</h2>
        <ol>
            <li>partFamilyCode and selectionPath (without selectionPath, the default configuration is used)</li>
            <li>classificationCode and partNumber (both parameters are required with this way)</li>
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

        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Get formats list</button>
    </form>
<?php
if (!empty($_GET["cultureInfo"])) {
    // session is already started in checkToken.php
    $apiReturn = getCadFormatsList($_SESSION["token"], $_GET["cultureInfo"], $_GET);

    echo $apiReturn;
}
