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
    "userEmail" => new QueryParameter(displayName: "User email",
        description: "Email address linked to the account.",
        required: true,
        type: "string"),
];
$optionalParameters = [
    "company" => new QueryParameter(displayName: "Company",
        description: "User company.",
        required: false,
        type: "string"),
    "country" => new QueryParameter(displayName: "Country",
        description: "User country. ISO 3166-2 characters.",
        required: false,
        type: "string"),
    "name" => new QueryParameter(displayName: "Name",
        description: "User last name.",
        required: false,
        type: "string"),
    "fname" => new QueryParameter(displayName: "First name",
        description: "User first name.",
        required: false,
        type: "string"),
    "addr1" => new QueryParameter(displayName: "Address 1",
        description: "First field for the user address.",
        required: false,
        type: "string"),
    "addr2" => new QueryParameter(displayName: "Address 2",
        description: "Second field for the user address.",
        required: false,
        type: "string"),
    "addr3" => new QueryParameter(displayName: "Address 3",
        description: "Third field for the user address.",
        required: false,
        type: "string"),
    "city" => new QueryParameter(displayName: "City",
        description: "User city.",
        required: false,
        type: "string"),
    "state" => new QueryParameter(displayName: "State",
        description: "User state, for North America.",
        required: false,
        type: "string"),
    "zipCode" => new QueryParameter(displayName: "Zip code",
        description: "User zip code.",
        required: false,
        type: "string"),
    "phone" => new QueryParameter(displayName: "Phone",
        description: "User phone number.",
        required: false,
        type: "string"),
    "fax" => new QueryParameter(displayName: "Fax",
        description: "User FAX number.",
        required: false,
        type: "string"),
    "tpOptIn" => new QueryParameter(displayName: "TraceParts services information",
        description: "Consent to receive information sent by TraceParts by email about TraceParts services.",
        required: false,
        type: "boolean",
        defaultValue: false),
    "partnersOptIn" => new QueryParameter(displayName: "Partners services information",
        description: "Consent to receive information sent by TraceParts by email about TraceParts’ partners’ services.",
        required: false,
        type: "boolean",
        defaultValue: false),
]
?>
<?php
/**
 * @return QueryParameter[]
 */
function getPossibleOptions(): array
{
    global $optionalParameters;
    return $optionalParameters;
}

?>
<?php
function createAUserAccount(string $token, string $userEmail, array $options): ApiResponse
{
    // get the possible options to compare them later with the given ones
    $possibleOptions = getPossibleOptions();

    $optionsString = "";
    // loop through each possible options
    foreach ($possibleOptions as $optionKey => $optionValue) {
        // check if $optionKey is in the given options
        if (!empty($options[$optionKey])) {
            if ($optionValue->type == "boolean") {
                // given values are all strings so if the $possibleOptions is a boolean it tries to decode the given value
                $boolValue = MyDecoder::decodeBoolValue($options[$optionKey]);
                // the decoded value is null if fails, and it also checks if the given value is different of the default value
                if (!is_null($boolValue) && $boolValue != $optionValue->defaultValue) {
                    // if everything is good, it had this option to the $optionsString
                    $stringValue = $boolValue ? "true" : "false";
                    $optionsString .= ',"' . $optionKey . '":' . $stringValue;
                }
            } elseif ($options[$optionKey] != $optionValue->defaultValue) {
                // checks if the given value is different of the default value
                // if everything is good, it is encoded and haded to the $optionsString
                $optionsString .= ',"' . $optionKey . '":"' . urlencode($options[$optionKey]) . '"';
            }
        }
    }

    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Account/SignUp",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{" .
            '"userEmail":"' . urlencode($userEmail) .
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
    <h1>Create a user account</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/post_v2-account-signup" target="_blank">Link
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
        <button type="submit">Create account</button>
    </form>
<?php
if (!empty($_POST["userEmail"])) {
    // session is already started in checkToken.php
    $apiReturn = createAUserAccount($_SESSION["token"], $_POST["userEmail"], $_POST);

    //example of success return : 3fa85f64-5717-4562-b3fc-2c963f66afa6
    //this example is the example value displayed in the documentation
    if ($apiReturn->httpCode == 201) {
        if (preg_match("/^([a-zA-Z0-9]){8}-(?1){4}-(?1){4}-(?1){4}-(?1){12}$/", $apiReturn->response)) {
            // $apiReturn is like 3fa85f64-5717-4562-b3fc-2c963f66afa6
            echo("<p class='success-message'>Account " . $_POST["userEmail"] . " successfully created !</p>");
        }
    }
    echo $apiReturn;
}