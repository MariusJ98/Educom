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

if (!$conn)
  {
    die("Connection failed: " . mysqli_connect_error($conn));
  }
  else
  {
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
function checkEmail(mysqli $conn, string $email): bool
{
    $email_verification_query = mysqli_prepare($conn, 'SELECT user_email FROM users WHERE user_email =?');
    mysqli_stmt_bind_param($email_verification_query, "s", $email);
    mysqli_stmt_execute($email_verification_query);
    $email_verification_result = mysqli_stmt_get_result($email_verification_query);
    echo mysqli_num_rows($email_verification_result);
    return mysqli_fetch_assoc($email_verification_result) != false;
}

// save user to users.txt in email | name| password format
function saveUser(mysqli $conn, string $email, string $name, string $password): void
{
    //file_put_contents('users.txt', $email . '|' . $name . '|' . $hashed_password . PHP_EOL, FILE_APPEND);  // deprecated file saving
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_submission_query = mysqli_prepare($conn, 'INSERT INTO users (user_email, user_name, user_password) VALUES (?, ?, ?)');
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
            }
            else {
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