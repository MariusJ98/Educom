<?php
//===========================================
// Connect to database
//===========================================
$hostname = "localhost";
$username = "root";
$password = "";
$database = 'marius_webshop';
// Create connection
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully <br>" . PHP_EOL;
}


//================================================================
// Functions related to user submission and user login
//================================================================

// Get request based on webpage
function getRequest(): array
{
    $posted = $_SERVER['REQUEST_METHOD'] === 'POST';
    return [
        'posted' => $posted,
        'page' => strtolower(getRequestVar($posted, 'page', 'home')),
        'id' => getRequestVar(false, 'id', ''),
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
function checkEmail(mysqli $conn, string $email): bool
{
    $email_verification_query = mysqli_prepare($conn, 'SELECT email FROM users WHERE email =?');
    mysqli_stmt_bind_param($email_verification_query, "s", $email);
    mysqli_stmt_execute($email_verification_query);
    $email_verification_result = mysqli_stmt_get_result($email_verification_query);
    return mysqli_fetch_assoc($email_verification_result) != false;
}

// save user to users.txt in email | name | password format
function saveUser(mysqli $conn, string $email, string $name, string $password): void
{
    // hash password for safety, send it to a prepared query
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_submission_query = mysqli_prepare($conn, 'INSERT INTO users (`email`, `name`, `password`) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($user_submission_query, "sss", $email, $name, $hashed_password);
    mysqli_stmt_execute($user_submission_query);
}

// Handle registration form validation
function handleRegistration(mysqli $conn, array $request, array $fields)
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
            if (isset($post_result['email']) && checkEmail($conn, $post_result['email'])) {
                $post_result['ok'] = false;
                $post_result['email_err'] = 'This email is already registered.';
            }
            if ($post_result['ok'] === True) {
                saveUser($conn, $post_result['email'], $post_result['name'], $post_result['password']);
                echo 'Registration successful!';
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
//=================================================================
// Handle verification of user email and password against database
//=================================================================
function handleUserLogin(mysqli $conn, string $email, string $password)
{
    $result = [
        'loginEmail_err' => false,
        'loginPassword_err' => false,
    ];

    // fetch from server
    $verification_query = mysqli_prepare($conn, 'SELECT * FROM users WHERE email =?');
    mysqli_stmt_bind_param($verification_query, "s", $email);
    mysqli_stmt_execute($verification_query);
    $verification_result = mysqli_stmt_get_result($verification_query);
    $database_user = mysqli_fetch_assoc($verification_result);

    // if fetch returns null or false ==> error message
    if (!$database_user) {
        $result['loginEmail_err'] = True;
        echo '<h3>Wrong email</h3>';
        return false;
    }

    $password_result = password_verify($password, $database_user['password']);

    // if password and email have matched, the result is true and the username is saved
    // Set error array to false
    if ($password_result === True) {
        $name = $database_user['name'];
        $result['loginEmail_err'] = false;
        $_SESSION['userName'] = $name;
        $_SESSION['loginMessage'] = 'Login succesful';
        header('location: index.php?page=shoppingcart');
        return true;
    } else {
        $result['loginPassword_err'] = True;
        echo '<h3>Wrong password</h3>';
        return false;
    }

}

// Handle login forms of login page
function handleLogin(mysqli $conn, array $request, array $fields)
{
    if ($request['posted']) {
        $post_result = checkFields($fields);
        if ($post_result['ok']) {
            $login_result = handleUserLogin($conn, $post_result['email'], $post_result['password']);
            if ($login_result === false) {
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

// logout users
function logoutUser()
{
    session_unset();
    $_SESSION['logoutMessage'] = 'Logout succesful';
}