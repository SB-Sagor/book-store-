<?php

# Form validation function
function is_empty($var, $field_name, $redirect_to, $message_key = "error", $extra = "")
{
    if (empty($var)) {
        $error_msg = urlencode("The $field_name is required");
        $query = "$message_key=$error_msg" . ($extra ? "&$extra" : "");
        header("Location: $redirect_to?$query");
        exit;
    }
}
