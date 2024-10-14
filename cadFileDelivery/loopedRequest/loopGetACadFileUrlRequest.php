<?php
// CheckToken.php starts the session
require_once '../../utils/CheckToken.php';

require_once '../../utils/RootApiUrl.php';
require_once '../../utils/QueryParameter.php';
require_once '../../utils/ApiResponse.php';

include "../../utils/header.html";
include "../../utils/navbar.html";
?>
    <h1>Loop get a CAD file request</h1>
    <form action="" method="post">
        <label for='cadRequestId'>CAD request ID :</label>
        <input name='cadRequestId' id='cadRequestId' type='number'
               title='ID of the request provided by the cadRequest end point' required/><br>
        <button type="submit">Loop request (this can take a while)</button>
    </form>
<?php
//documentation : https://developers.traceparts.com/v2/reference/get_v2-product-cadfileurls
if (!empty($_POST["cadRequestId"])) {
    $timeout = 10; // in minutes
    $interval = 2; // in seconds

    $finalResult = "Timeout reached (" . $timeout . " minutes with " . $interval . " seconds interval). Your model couldn't be generated.";

    $nbrOfIterations = $timeout * 60 / $interval;
    for ($i = 0; $i < $nbrOfIterations; $i++) {
        $curl = curl_init();
        // 📘 Warning! Any tries will be recorded in the Production data.
        curl_setopt_array($curl, [
            CURLOPT_URL => RootApiUrl::$URL . "v2/Product/cadFileUrl" .
                "?cadRequestId=" . urlencode($_POST["cadRequestId"]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $_SESSION["token"]
            ],
        ]);

        $result = new ApiResponse($curl);

        curl_close($curl);

        // httpCode 204 means wait the file is creating
        if ($result->httpCode == 204) {
            sleep($interval);
        } else {
            $finalResult = $result;
            break;
        }
    }
    echo $finalResult;
}