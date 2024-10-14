<?php

class QueryParameter
{
    /**
     * @var string the name you want to display (ex: parameterName => displayName='Parameter name')
     */
    public string $displayName;

    /**
     * @var string parameter's description like it's written in the doc
     */
    public string $description;

    /**
     * @var bool either the parameter is a required field or not
     */
    public bool $required;

    /**
     * @var string parameter's type LIKE gettype() returns NOT LIKE it's written in the doc
     * @link https://www.php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues
     */
    public string $type;

    /**
     * @var mixed optional default value. Written with the good type (ex for an int : 1 not '1' | ex for a bool : true not "true")
     */
    public mixed $defaultValue;

    /**
     * @param string $displayName the name you want to display (ex: parameterName => displayName='Parameter name')
     * @param string $description parameter's description like it's written in the doc
     * @param bool $required either the parameter is a required field or not
     * @param string $type parameter's type like gettype() returns : https://www.php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues
     * @param mixed|null $defaultValue optional default value. Written with the good type (ex for an int : 1 not '1' | ex for a bool : true not "true")
     */
    public function __construct(string $displayName, string $description, bool $required, string $type, mixed $defaultValue = null)
    {
        $this->displayName = $displayName;
        $this->description = $description;
        $this->required = $required;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }

    public function buildQueryParameterHTML($parameterId): string
    {
        $outputString = "";
        $required = ($this->required ? "required" : "");
        if ($this->type == "boolean") {
            $outputString = "<p>$this->displayName ($this->description) :</p>\n";
            $outputString .= "<label for='$parameterId'>$this->displayName :</label>\n";
            $outputString .= "<input type='radio' name='$parameterId' id='{$parameterId}True' value='true' $required" . ($this->defaultValue ? " checked" : "") . ">\n";
            $outputString .= "<label for='{$parameterId}True'>Yes</label>\n";
            $outputString .= "<input type='radio' name='$parameterId' id='{$parameterId}False' value='false' $required" . (!$this->defaultValue ? " checked" : "") . ">\n";
            $outputString .= "<label for='{$parameterId}False'>No</label>\n";
            if (!is_null($this->defaultValue)) {
                $defaultValue = $this->defaultValue ? "true" : "false";
                $outputString .= "<p>Default to $defaultValue</p>\n";
            }
        } else {
            $outputString = "<label for='$parameterId'>$this->displayName :</label>\n";
            $type = ($this->type == "integer" ? "number" : "text");
            $defaultValue = !is_null($this->defaultValue) ? "value='$this->defaultValue'" : "";
            $outputString .= "<input name='$parameterId' id='$parameterId' type='$type' title='$this->description' $defaultValue $required/>";
            if (!is_null($this->defaultValue)) {
                $outputString .= "<p>Default to $this->defaultValue</p>\n";
            }
        }
        $outputString .= "<br>\n\n";
        return $outputString;
    }
}