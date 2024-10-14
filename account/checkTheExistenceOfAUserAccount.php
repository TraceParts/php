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
    "userEmail" => new QueryParameter(
        displayName: "User email",
        description: "Email address linked to the account.",
        required: true,
        type: "string"),
];
?>
<?php
function checkTheExistenceOfAUserAccount(string $token, string $userEmail): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Account/CheckLogin/" . urlencode($userEmail),
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
    <h1>Check the existence of a user account</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-account-checklogin-useremail" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Check existence</button>
    </form>
<?php
if (!empty($_GET["userEmail"])) {
    // session is already started in checkToken.php
    $apiReturn = checkTheExistenceOfAUserAccount($_SESSION["token"], $_GET["userEmail"]);

    if ($apiReturn->httpCode == 200) {
        //Success
        echo("<p class=\"success-message\">Success ! The account " . $_GET["userEmail"] . " exists.</p>");
    }
    echo $apiReturn;
}