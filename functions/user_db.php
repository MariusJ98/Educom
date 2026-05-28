<?php
//================================================================
// Functions related to user submission and user login
//================================================================

// Get request based on webpage
function getRequest(): array
{
    $posted = $_SERVER['REQUEST_METHOD'] === 'POST';
    return [
        'posted' => $posted,
        'page' => strtolower(getRequestVar($posted, 'page', 'contact'))
    ];
}

// Get request type POST/GET
function getRequestVar(bool $from_post, string $varname, string $default): string
{
    $result = filter_input(
        $from_post ? INPUT_POST : INPUT_GET,
        $varname,
        FILTER_SANITIZE_FULL_SPECIAL_CHARS
    );
    return (is_null($result) || $result === false) ? $default : $result;
}

// Verify email in user database

function checkEmail(string $email): bool
{
    $lines = file('users.txt');
    foreach ($lines as $line) {
        [$stored_email,] = explode('|', $line, 2);
        if (strtolower($stored_email) === strtolower($email)) {
            return true;
        }
    }
    return false;
}

// save user to users.txt in email | name| password format
function saveUser(string $email, string $name, string $password): void
{
    file_put_contents('users.txt', $email . '|' . $name . '|' . $password . PHP_EOL, FILE_APPEND);

}

// Handle registration form validation
function handleRegistration(array $request, array $fields)
{
    if ($request['posted']) {
        $post_result = checkFields($fields);

        if ($post_result['ok']) {
            if (isset($post_result['password'], $post_result['verifypassword'])) {
                if ($post_result['password'] !== $post_result['verifypassword']) {
                    $post_result['ok'] = false;
                    $post_result['verifypassword_err'] = 'Passwords do not match.';
                }
            }
            if (isset($post_result['email']) && checkEmail($post_result['email'])) {
                $post_result['ok'] = false;
                $post_result['email_err'] = 'This email is already registered.';
            }
            if ($post_result['ok'] === True) {
                saveUser($post_result['email'], $post_result['name'], $post_result['password']);
                echo 'Registration successful!';
            }
        } else {
            showForm(
                action: 'index.php',
                page: $request['page'],
                method: 'POST',
                fields: $fields,
                submit_caption: 'Sign Up!',
                post_result: $post_result
            );
        }
    } else {
        showForm(
            page: $request['page'],
            action: 'index.php',
            method: 'POST',
            fields: $fields,
            submit_caption: 'Sign Up!'
        );
    }
}

// Handle verification of user email and password against database
function handleUserLogin(string $email, string $password)
{
    $result = [
        'loginEmail_err' => false,
        'loginPassword_err' => false,
    ];
    $lines = file('users.txt');
    foreach ($lines as $line) {
        [$stored_email, $stored_name, $stored_password] = explode('|', $line, 3);
        if (strtolower($email) === strtolower($stored_email)) {
            if ((trim($password)) === (trim($stored_password))) {
                $name = $stored_name;
                $result['loginEmail_err'] = false;
                break;
            } else {
                $result['loginPassword_err'] = True;
            }
        } else {
            $result['loginEmail_err'] = True;
        }
    }

    if ($result['loginEmail_err'] === false && $result['loginPassword_err'] === false) {
        $_SESSION['userName'] = $name;
        $_SESSION['loginMessage'] = 'Login succesful';
        header('location: index.php?page=home');
    } elseif ($result['loginEmail_err'] === false && $result['loginPassword_err'] === True) {
        echo 'Wrong password';
    } elseif ($result['loginEmail_err'] === True && $result['loginPassword_err'] === false) {
        echo 'Wrong email';
    }
}

// Handle login forms of login page
function handleLogin(array $request, array $fields)
{
    if ($request['posted']) {
        $post_result = checkFields($fields);
        if ($post_result['ok']) {
            handleUserLogin($post_result['email'], $post_result['password']);
        } else {
            showForm(
                action: 'index.php',
                page: $request['page'],
                method: 'POST',
                fields: $fields,
                submit_caption: 'Login',
                post_result: $post_result
            );
        }
    } else {
        showForm(
            page: $request['page'],
            action: 'index.php',
            method: 'POST',
            fields: $fields,
            submit_caption: 'Login'
        );
    }
}

// write contact forms to txt file
function saveContact(string $name, string $email, string $message)
{
    file_put_contents('contact.txt', $name . '|' . $email . '|' . $message . PHP_EOL, FILE_APPEND);
}

// handle contact form validation
function handleContactForm(array $request, array $fields)
{
    if ($request['posted']) {
        $post_result = checkFields($fields);
        if ($post_result['ok']) {
            saveContact($post_result['name'], $post_result['email'], $post_result['message']);
            echo 'thanks for your message';
        } else {
            showForm(
                action: 'index.php',
                page: $request['page'],
                method: 'POST',
                fields: $fields,
                submit_caption: 'Send',
                post_result: $post_result
            );
        }
    } else {
        showForm(
            page: $request['page'],
            action: 'index.php',
            method: 'POST',
            fields: $fields,
            submit_caption: 'Send'
        );
    }
}

function logoutUser(){
    session_unset();
    $_SESSION['logoutMessage'] = 'Logout succesful';
}