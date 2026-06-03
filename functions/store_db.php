<?php
require_once './functions/page_layout.php';
require_once './functions/user_db.php';
require_once './functions/forms.php';

echo "<link rel='stylesheet' type='text/css' href='./stylesheet/styles.css' />";
// fetch products from database and show on page
function showShopContents(mysqli $conn)
{
    $query = 'SELECT * FROM `product`';
    $product_query = mysqli_query($conn, $query);

    echo '<div class="store">';

    while ($row = mysqli_fetch_assoc($product_query)) {
        echo '<div class="store_item">';
        echo '<form method="post" action="index.php">';

        echo '<a href="?page=product&id='. $row['id']. '"><img class="store_small" src=' . $row['imgURL'] . '></a><br>';

        echo ' ' . $row['name'] . '<br>';
        echo ' €' . $row["price"] . "<br>";
        if (isset($_SESSION['userName'])) {
            echo '<input type="number" name="quantity" value="1" size="2" />
            <input type="submit" value="Add to Cart" />
            <input type="hidden" name="page" value = "shop"/>
            <input type="hidden" name="product_id" value="' . $row['id'] . '" >';
        }
        echo '</form>';
        echo '</div>';
    }
    echo '</div>';
}

// adds value to cart
function addToCart(int $product_id, int $quantity)
{
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// adds product to cart
function handleAddToCart(array $request)
{
    if ($request['posted']) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        addToCart($product_id, $quantity);
        header('location: index.php?page=shop');
    }
}

// fetch prices from database
function getPrices(mysqli $conn)
{
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $type_placeholders = str_repeat('i', count($product_ids));
    $prepared_product_fetch = mysqli_prepare($conn, 'SELECT * FROM product WHERE id IN (' . $placeholders . ')');
    mysqli_stmt_bind_param($prepared_product_fetch, $type_placeholders, ...$product_ids);
    mysqli_stmt_execute($prepared_product_fetch);
    $result = mysqli_stmt_get_result($prepared_product_fetch);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;

}

// show cart items
function showCart(mysqli $conn)
{
    $total_price = 0;
    $products = getPrices($conn);
    echo '<table>
        <tr>
            <th>img</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
        </tr>';

    foreach ($products as $row) {
        echo '<tr>';
        $quantity = $_SESSION['cart'][$row['id']];
        $subtotal = $quantity * $row['price'];
        $total_price += $subtotal;
        echo '<td><img class="store_item" src=' . $row['imgURL'] . '></td>';
        echo '<td> ' . $row['name'] . '</td>';
        echo '<td> €' . $row["price"] . '</td>';
        echo '<td>' . $quantity . '</td>';
        echo '<td> €' . $subtotal . '</td>';
        echo '<td><form method="post" action="index.php">
                <input type="submit" name="removeFromCart" value="Remove from cart" />
                <input type="hidden" name="product_id" value="' . $row['id'] . '" >
                <input type="hidden" name="page" value="shoppingcart">
                </form></td>';
        echo '</tr>';
    }

    echo '</table>';
    echo 'Total price: ' . $total_price . '';

    echo '<form method="post" action="index.php">';
    echo '<input type="submit" name="checkout" value="Checkout" /> 
            <input type="hidden" name="page" value = "shoppingcart"/>';
    // call function to send cart checkout to database
    echo '<input type="submit" name="emptyCart" value="Empty cart" /> 
            <input type="hidden" name="page" value = "shoppingcart"/>';
    // call code that executes unset($_SESSION['cart'])
    echo '</form>';

}

// remove item from shopping cart variable
function removeFromCart(string $product_id)
{
    unset($_SESSION['cart'][$product_id]);
}

// performs the removal from shopping cart
function handleRemoveFromCart(array $request)
{
    if ($request['posted']) {
        if (isset($_POST['removeFromCart'])) {
            $product_id = $_POST['product_id'];
            removeFromCart(($product_id));
            header('location: index.php?page=shoppingcart');
        }
    }
}

// empties the cart session variable
function emptyCart()
{
    unset($_SESSION['cart']);
}

// performs the complete removal of items from cart
function handleEmptyCart(array $request)
{
    if ($request['posted']) {
        if (isset($_POST['emptyCart'])) {
            emptyCart();
            header('location: index.php?page=shoppingcart');
        }
    }
}

function showProductDetails(mysqli $conn, array $request){
    
    $product_id = $request['id'];

    $product_id_query = mysqli_prepare($conn, 'SELECT * FROM product WHERE `id`=?');
    mysqli_stmt_bind_param($product_id_query, "i", $product_id);
    mysqli_stmt_execute($product_id_query);
    $product_fetch_result = mysqli_stmt_get_result($product_id_query);
    
    $product = mysqli_fetch_assoc($product_fetch_result);
    // Display items
    echo '<h2> ' . $product['name'] . '</h2><br>';
    echo '<img class="store_item_detail" src=' . $product['imgURL'] . '><br>';
    echo '<h4> €' . $product["price"] . "</h4><br>";
    echo '<p>'. $product['description'] . '</p>';


    if (isset($_SESSION['userName'])) {
        echo '<form method="post" action="index.php">';
        echo '<input type="number" name="quantity" value="1" size="2" />
        <input type="submit" value="Add to Cart" />
        <input type="hidden" name="page" value = "product"/>
        <input type="hidden" name="product_id" value="' . $product['id'] . '" >';
        echo '</form>';
        }
        
}