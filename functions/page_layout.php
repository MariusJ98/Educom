<?php
//===============================================================
// Functions related to page layouts 
//===============================================================
require_once './functions/forms.php';
require_once './functions/user_db.php';

function showPageContent($conn)
{         // load page content based on page name
    // fetch url link if it doesnt have 'page' redirect to home
    $request = getRequest();
    $page = $request['page'];
    $fields = getFieldPerPage($request['page']);

    if ($page === 'logout'){  // this is not a good way to do the loading of pages, you should redirect to a user homepage
        logoutUser();
        header('location: index.php?page=home');
    }

    beginPage($page);               // load beginning of webpage

    switch ($page) {
        case 'home':
        case 'about':               // load about and home page the same way
            showTextPerPage($page);
            break;
        case 'contact':             // Load contact form
            showTextPerPage($page);
            handleContactForm($request, $fields);
            break;
        case 'login':               // Open login page
            showTextPerPage($page);
            handleLogin($request, $fields);
            break;
        case 'register':            // open register page
            showTextPerPage($page);
            handleRegistration($conn, $request, $fields);
            break;
        case 'logout':              // logout
            // logoutUser();
            break;            
        default:                    // if none match, redirect to home
            showTextPerPage('home');

    }
    endPage();                      // load end of webpage
}

//===========================================
function beginPage($page)
{          // Loads all the starting page functions
    startDoc();
    startHeader();
    showHeaderContent($page);
    importStyleSheet();
    endHeader();
    startBody();
    showLoginStatus();
    showPageTitle($page);
    showNavMenu();
    setMainPageStart();
}

function endPage()
{               // Function to add all closing statements to page
    setMainPageEnd();
    showFooter();
    endBody();
    endDoc();
}

//========================================
// Start document functions
//========================================

function startDoc()
{                // Start document type
    echo "<!DOCTYPE html>\n<html>";
}

function startHeader()
{             // Header start
    echo "<head class='page'>";
}

function showHeaderContent($page)
{  // Page title        
    echo "<title class='header'>$page - Marius' website</title>";
}

function importStyleSheet()
{        // import css styling sheet
    echo "<link rel='stylesheet' type='text/css' href='./styles.css' />";
}

function endHeader()
{               // Header end
    echo "</head>";
}

function showNavMenu()
{              // Per key:value in the page array, display them as a list item
    echo '<nav><ul class="menu nav_link">';
    foreach (getMenuItems() as $page => $label) {
        echo '<li><a href="?page=' . $page . '">' . $label . '</a></li>';
    }
    echo '</ul></nav>';
    echo '<div class="menuBottomBar"></div>';
}

function startBody()
{               // Body start
    echo "<body>";
}

function showLoginStatus(){
    if (isset($_SESSION['logoutMessage'])){
        echo $_SESSION['logoutMessage'];
        unset($_SESSION['logoutMessage']);
    }
    if (isset($_SESSION['loginMessage'])){
    echo $_SESSION['loginMessage'];
    unset($_SESSION['loginMessage']);
    }    
}

function setMainPageStart()
{        // Set main text of page
    echo '<main>';
}

//=========================================================================
// Functions to show body and main type content '
//=========================================================================

function showPageTitle($page)
{      //Web page title
    echo ucfirst("<h1 class='header'>$page</h1>");
}

function showBodyContent()
{         // body content for page
    echo "<h2> Test pagina voor php content </h2>";
}

// save forms per page in arrays
function getFieldPerPage(string $page): array
{
    switch ($page) {
        case 'login':
            return [
                'email' => 'email',
                'password' => 'password',
            ];
        case 'register':
            return [
                'name' => 'text',
                'email' => 'email',
                'password' => 'password',
                'verifypassword' => 'password',
            ];
        case 'contact':
        default:
            return [
                'name' => 'text',
                'email' => 'email',
                'message' => 'textarea',
            ];
    }
}

function getMenuItems(): array
{ // Save pages in arrays

    if (isset($_SESSION['userName'])) {
        return [
            'home'      => 'Home',
            'about'     => 'About',
            'contact'   => 'Contact',
            'logout'    => 'Logout ' .$_SESSION['userName'] ,
        ];
    } else {
        return [
            'home' => 'Home',
            'about' => 'About',
            'contact' => 'Contact',
            'login' => 'Login',
            'register' => 'Register',
        ];
    }
}

// dump variables on screen function
function dump(string $var_name, mixed $var_value, bool $as_code = false): void
{
    echo '<h3>' . $var_name . '</h3><' . ($as_code ? 'code' : 'pre') . '>';
    is_array($var_value) ? print_r($var_value) : var_dump($var_value);
    echo '</' . ($as_code ? 'code' : 'pre') . '>';
}

//========================================================================
// PAGE END functions
//========================================================================
function showFooter()
{              // footnote
    echo "<footer class='footer'>&copy Marius " . date("Y") . "</footer>";
}

function setMainPageEnd()
{          // End <main> section of page
    echo '</main>';
}

function endBody()
{                 // end of HTML body
    echo "</body>";
}

function endDoc()
{                  // end HTML document
    echo "</html>";
}

//======================================================================
// Function to display text in the <body> and <main> part of the page
//======================================================================

function showTextPerPage(string $page)
{

    switch ($page) {
        case 'home':
            echo '<p>Hoi! Welkom op mijn website!</p>';
            break;
        case 'about':
            echo '<p class="paragraph">Hallo! Ik ben Marius.<br>
                Ik ben sinds 11 Mei gestart bij Educom in Arnhem.<br>
                Op deze website leer ik o.a. HTML, CSS, en PHP.<br>
                In mijn vrije tijd speel ik graag bordspellen met mijn vrienden.
                </p>';
            break;
        case 'contact':
            echo '<p> Vul het contact formulier hier in</p>';
            break;
        case 'login':
            echo '<p>Log in:</p>';
            break;
        case 'register':
            echo '<p>Maak een account aan:</p>';
            break;
        default:
            echo '';
    }

}

?>