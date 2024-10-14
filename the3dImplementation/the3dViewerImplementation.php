<?php
require_once '../utils/QueryParameter.php';
require_once '../utils/MyDecoder.php';

include "../utils/header.html";
include "../utils/navbar.html";
?>
<?php
$requiredParameters = [
    "elsid" => new QueryParameter(displayName: "EasyLink Solutions ID (ELS ID)",
        description: "Your EasyLink Solutions ID (ELS ID), provided in the email with your Tenant Uid and your API key",
        required: true,
        type: "string"),
    "cultureInfo" => new QueryParameter(displayName: "Culture info",
        description: "Language of the labels.",
        required: true,
        type: "string"),
];
// One of the two pairs must be provided
$firstRequiredPair = [
    "SupplierID" => new QueryParameter(displayName: "Supplier ID",
        description: "ClassificationCode provided by the \"availability\" endpoints",
        required: false,
        type: "string"),
    "PartNumber" => new QueryParameter(displayName: "Part number",
        description: "Identifier of a product (to use in combination with SupplierID). Part number as stored in the TraceParts database.",
        required: false,
        type: "string"),
];
// One of the two pairs must be provided
$secondRequiredPair = [
    "Product" => new QueryParameter(displayName: "Product",
        description: "PartFamilyCode provided by the \"availability\" endpoints",
        required: false,
        type: "string"),
    "SelectionPath" => new QueryParameter(displayName: "Selection path",
        description: "Sequence of parameters which defines a unique configuration for one given partFamilyCode.",
        required: false,
        type: "string"),
];
$optionalRenderingParameters = [
    "SetBackgroundColor" => new QueryParameter(displayName: "Background color (Hexadecimal)",
        description: "Sets a color on the background of the 3D viewer.",
        required: false,
        type: "string",
        defaultValue: "0xFFFFFF"),
    "SetRenderMode" => new QueryParameter(displayName: "Render mode",
        description: "Rendering of the 3D model. Values: “shaded-edged”, “shaded”, “transparent”, “wireframe”, “edged”",
        required: false,
        type: "string",
        defaultValue: "shaded-edged"),
    "EnableMirrorEffect" => new QueryParameter(displayName: "Mirror effect",
        description: "Enable the mirror effect on the XZ plane",
        required: false,
        type: "boolean",
        defaultValue: false),
    "DisplayCoordinateSystem" => new QueryParameter(displayName: "Display coordinate system",
        description: "Enable the mirror effect on the XZ plane",
        required: false,
        type: "boolean",
        defaultValue: false),
    "EnablePresentationMode" => new QueryParameter(displayName: "Presentation mode",
        description: "The model rotates on the Y axis until a user interaction",
        required: false,
        type: "boolean",
        defaultValue: false),
];
$optionalToolbarsParameters = [
    "DisplayUIMenu" => new QueryParameter(displayName: "Display UI menu",
        description: "Display the toolbars (on the bottom and on the right)",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUIContextMenu" => new QueryParameter(displayName: "Display UI context menu",
        description: "Enable the contextual menu with Views and Render sub menus",
        required: false,
        type: "boolean",
        defaultValue: true),
    "MergeUIMenu" => new QueryParameter(displayName: "Display UI context menu",
        description: "Merge the contextual menu inside the main menu",
        required: false,
        type: "boolean",
        defaultValue: false),
    "MenuAlwaysVisible" => new QueryParameter(displayName: "Menu always visible",
        description: "Always display the toolbar",
        required: false,
        type: "boolean",
        defaultValue: false),
    "DisplayUIResetButtonMenu" => new QueryParameter(displayName: "Display UI reset button menu",
        description: "Display the Reset button",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUIScreenshotButtonMenu" => new QueryParameter(displayName: "Display UI screenshot button menu",
        description: "Display the Screenshot button",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUISettingsSubMenu" => new QueryParameter(displayName: "Display UI settings sub menu",
        description: "Display the Settings menu",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUIPresentationModeButtonMenu" => new QueryParameter(displayName: "Display UI presentation button menu",
        description: "Display the Presentation button",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUIViewsSubContextMenu" => new QueryParameter(displayName: "Display UI views sub context menu",
        description: "Display the Views sub menu (for the contextual menu)",
        required: false,
        type: "boolean",
        defaultValue: true),
    "DisplayUIRenderModesSubContextMenu" => new QueryParameter(displayName: "Display UI render modes sub context menu",
        description: "Display the Render sub menu (for the contextual menu)",
        required: false,
        type: "boolean",
        defaultValue: true),
];
?>
<?php
/**
 * @return QueryParameter[]
 */
function getPossibleOptions(): array
{
    global $firstRequiredPair, $secondRequiredPair, $optionalRenderingParameters, $optionalToolbarsParameters;
    return array_merge($firstRequiredPair, $secondRequiredPair, $optionalRenderingParameters, $optionalToolbarsParameters);
}

?>
<?php
/**
 * @link https://developers.traceparts.com/v2/reference/3d-viewer-implementation
 */
function getThe3dImplementationViewerUrl(string $elsid, string $cultureInfo, array $options): string
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
                    $optionsString .= $optionKey . "=" . $stringValue . "&";
                }
            } elseif ($options[$optionKey] != $optionValue->defaultValue) {
                // checks if the given value is different of the default value
                // if everything is good, it is encoded and haded to the $optionsString
                $optionsString .= $optionKey . "=" . urlencode($options[$optionKey]) . "&";
            }
        }
    }

    // THERE IS NO API CALL HERE. This function provides a 3D viewer of the 3D model of one given configuration of a catalog
    $url = "https://www.traceparts.com/els/";
    $url .= urlencode($elsid);
    $url .= "/";
    $url .= urlencode($cultureInfo);
    $url .= "/api/viewer/3d?";
    $url .= $optionsString;
    return $url;
}

?>
<h1>3D viewer implementation.</h1>
<p><a href="https://developers.traceparts.com/v2/reference/3d-viewer-implementation" target="_blank">Link
        to the documentation (new page)</a></p>
<?php
if (!empty($_GET["elsid"]) && !empty($_GET["cultureInfo"])) {
    $the3dImplementationViewerUrl = getThe3dImplementationViewerUrl($_GET["elsid"], $_GET["cultureInfo"], $_GET);
    echo("<iframe src=\"" . $the3dImplementationViewerUrl . "\"></iframe>");
    echo("<p><a href=\"" . $the3dImplementationViewerUrl . "\" target=\"_blank\">" . $the3dImplementationViewerUrl . "</a></p>");
}
?>
<form action="" method="get">
    <h2>Required parameters :</h2>
    <?php
    foreach ($requiredParameters as $key => $value) {
        echo $value->buildQueryParameterHTML($key);
    }
    ?>

    <h2>Creating the 3D viewer URL can manage two ways:</h2>
    <ol>
        <li>Both parameters (SupplierID and PartNumber) have to be used together. In this case, the couple “Product” and
            “SelectionPath” is not to use :
        </li>
        <li>Both parameters (Product and SelectionPath) have to be used together. In this case, the couple “SupplierID”
            and “PartNumber” is not to use :
        </li>
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

    <button type="submit">Get the 3D viewer</button>

    <h2>Optional parameters (rendering) :</h2>
    <?php
    foreach ($optionalRenderingParameters as $key => $value) {
        echo $value->buildQueryParameterHTML($key);
    }
    ?>
    <h2>Optional parameters (toolbars) :</h2>
    <?php
    foreach ($optionalToolbarsParameters as $key => $value) {
        echo $value->buildQueryParameterHTML($key);
    }
    ?>

    <button type="submit">Get the 3D viewer</button>
</form>