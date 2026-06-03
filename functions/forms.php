<?php
//============================================================
// This file stores functions related to creating forms in HTML
//============================================================
// Load php files
require_once 'user_db.php';


// Backbone function to start forms'
// Provides empty array
function showForm(string $page, string $action, string $method, array $fields, string $submit_caption, array $post_result = []): void
{
    openForm($page, $action, $method);
    showFields($fields, $post_result);
    closeForm($submit_caption);
}

// Open form function using HTML
// Specify Actions in the file itself $action and $method
function openForm(string $page, string $action, string $method): void
{
    echo '<form action="' . $action . '" method="' . $method . '" >' . PHP_EOL
        . '	<input type="hidden" name="page" value="' . $page . '" />' . PHP_EOL;
}

// Check for value or error in $post_result
// pass value to corresponding variable in showField()
function showFields(array $fields, array $post_result): void
{
    foreach ($fields as $name => $type) {
        showField(
            field_name: $name,
            field_type: $type,
            field_value: array_key_exists($name, $post_result) ? $post_result[$name] : '',
            field_error: array_key_exists($name . '_err', $post_result) ? $post_result[$name . '_err'] : ''
        );
    }
}

// Create form fields, allowing multiple types
function showField(string $field_name, string $field_type, string $field_value, string $field_error): void
{
    echo '      <label for="' . $field_name . '">' . $field_name . '</label><br>' . PHP_EOL; // create label for each field
    switch ($field_type) {
        case "textarea":   // In the case you want text area
            echo '      <textarea rows="5" cols="56" name="' . $field_name . '">' . $field_value . '</textarea>' . PHP_EOL;
            break;
        default:	        // In the case it is empty or default
            echo '      <input class="contact_form" type="' . $field_type . '" name="' . $field_name . '" value="' . $field_value . '"/>' . PHP_EOL;
            break;
    }
    if ($field_error)       // Errorous field input error message
    {
        echo '      <span class="input_error">' . $field_error . '</span>' . PHP_EOL;
    }
    echo '<br />' . PHP_EOL;
}


// End the HTML form with a submit button
function closeForm(string $submit_caption): void
{
    echo '  <button type="submit" value="submit">' . $submit_caption . '</button>' . PHP_EOL . '</form>' . PHP_EOL;
}

//========================================================
// Form and File VALIDATION
//========================================================
// Checks the fields of each page with the checkField function
// then saves the outcome in $result or displays an error
//========================================================

function checkFields(array $fields): array
{

    $result = [
        'ok' => true
    ];

    // Validation per field
    foreach (array_keys($fields) as $field_name) {
        $check = checkField($field_name);
        if ($check['ok']) {
            $result[$field_name] = $check['value'];
        } else {
            $result['ok'] = false;
            $result[$field_name . '_err'] = $check['error'];
        }
    }
    return $result;
}

// Validation of one field
function checkField(string $field_name): array
{

    // Set result to false, so it auto fails if it isnt set to ok
    $result = [
        'ok' => false
    ];

    // Are fields present
    if (isset($_POST[$field_name])) {

        // Filter input of special characters
        $value = filter_input(INPUT_POST, $field_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // If field value = empty --> display field is empty
        // Otherwise return OK		
        if (empty($value)) {
            $result['error'] = $field_name . ' is empty.';
        } else {
            $result['ok'] = true;
            $result['value'] = $value;
        }
    } else {
        $result['error'] = $field_name . ' not found.';
    }
    return $result;
}

?>