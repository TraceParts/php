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
    "cadRequestId" => new QueryParameter(
        displayName: "CAD request ID",
        description: "ID of the request provided by the cadRequest end point",
        required: true,
        type: "integer"),
];
?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/get_v2-product-cadfileurl
 */
function getACadFileUrl(string $token, int $cadRequestId): ApiResponse
{
    $curl = curl_init();
    // 📘 Warning! Any tries will be recorded in the Production data.
    curl_setopt_array($curl, [
        CURLOPT_URL => RootApiUrl::$URL . "v2/Product/cadFileUrl" .
            "?cadRequestId=" . $cadRequestId,
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
    <h1>Get CAD file URL</h1>
    <p><a href="https://developers.traceparts.com/v2/reference/get_v2-product-cadfileurl" target="_blank">Link
            to the documentation (new page)</a></p>

    <form action="" method="get">
        <h2>Required parameters :</h2>
        <?php
        foreach ($requiredParameters as $key => $value) {
            echo $value->buildQueryParameterHTML($key);
        }
        ?>
        <button type="submit">Get URL</button>
    </form>
<?php
if (!empty($_GET['cadRequestId'])) {
    // session is already started in checkToken.php
    $apiReturn = getACadFileUrl($_SESSION["token"], $_GET['cadRequestId']);

    echo $apiReturn;

    if ($apiReturn->httpCode == 204) {
        echo("<p class='warning-message'>Status code 204 means the file is generating. " .
            "You must repeat this request periodically to see if the file ends its generation.</p>\n");
        echo("<p >You can <a href='loopedRequest/loopGetACadFileUrlRequest.php'>click here to loop this request and get a definitive answer</a> (⚠️ this can take a while).</p>");
    }
}