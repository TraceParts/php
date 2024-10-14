<?php
session_start();
?>

<?php
require_once '../utils/RootApiUrl.php';
require_once '../utils/QueryParameter.php';
require_once '../utils/ApiResponse.php';

include "../utils/header.html";
include "../utils/navbar.html";
?>
<?php
$requiredParameters = [
    "tenantUid" => new QueryParameter(displayName: "Tenant Unique ID",
        description: "Tenant Unique ID provided in the email giving you access to our API. 
        It should look like this : '00000000-0000-0000-0000-000000000000'.",
        required: true,
        type: "string"),
    "apiKey" => new QueryParameter(displayName: "API key",
        description: "API key provided in the email giving you access to our API. 
        It should have a length between 4 and 50 characters.",
        required: true,
        type: "string"),
]
?>
<?php
/**
 * ⚠️This token gives direct access to our API with the associated credentials. Never let someone other that the owner of this credentials get this token.⚠️
 * @link https://developers.traceparts.com/v2/reference/post_v2-requesttoken
 */
function getAToken(string $tenantUid, string $apiKey): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/RequestToken",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{" .
            '"tenantUid":"' . urlencode($tenantUid) . '",' .
            '"apiKey":"' . urlencode($apiKey) . '"' .
            "}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "content-type: application/*+json"
        ],
    ]);

    $result = new ApiResponse($curl);

    curl_close($curl);

    return $result;
}

?>
<?php
$tenantUidFormat = "^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$";
function checkTenantUidFormat(string $tenantUid): bool|int
{
    global $tenantUidFormat;
    return preg_match("/$tenantUidFormat/", $tenantUid);
}

$apiKeyFormat = "^\w{4,50}$";
function checkApiKeyFormat(string $apiKey): bool|int
{
    global $apiKeyFormat;
    return preg_match("/$apiKeyFormat/", $apiKey);
}

?>
    <h1>Get a token</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/post_v2-requesttoken" target="_blank">Link to the
            documentation (new page)</a></p>

    <form action="" method="post">
        <p>You need to enter your Tenant Uid and your API key to generate the token.</p>
        <?php if (!empty($_SESSION["token"])) : ?>
            <p><strong>You already have a token so you have access to the other pages.<br>
                    You can still generate a new one if you want by filling the form below.</strong></p>
        <?php endif; ?>
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Get a token</button>
    </form>
<?php
if (!empty($_POST["tenantUid"])) {
    // check if the Tenant Uid has a valid format
    if (!checkTenantUidFormat($_POST["tenantUid"])) {
        // the Tenant Uid HAS NOT a valid format
        echo("<p class=\"warning-message\">The Tenant Uid format is not valid. Reminder, it should look like this : '00000000-0000-0000-0000-000000000000'.</p><br>");
    } else {
        // the Tenant Uid has a valid format
        $tenantUid = $_POST["tenantUid"];
    }
}
?>
<?php
if (!empty($_POST["apiKey"])) {
    // check if the API key has a valid format
    if (!checkApiKeyFormat($_POST["apiKey"])) {
        // the API key HAS NOT a valid format
        echo("<p class=\"warning-message\">The API Key format is not valid. Reminder, it should have a length between 4 and 50 characters.</p><br>");
    } else {
        // the API key has a valid format
        $apiKey = $_POST["apiKey"];
    }
}
?>
<?php
if (!empty($tenantUid) && !empty($apiKey)) {
    $apiReturn = getAToken($tenantUid, $apiKey);
    if ($apiReturn->httpCode == 200) {
        $decodedApiReturn = json_decode($apiReturn->response, true);
        if (array_key_exists("token", $decodedApiReturn)) {
            // the JSON string contains the token
            $_SESSION["token"] = $decodedApiReturn["token"];
            echo("<p class=\"success-message\">Token successfully generated !</p>");
        }
    }
    echo $apiReturn;
}