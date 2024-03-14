<?php
// Step 4: Output Filtered Results in a Table

// Connect to the database (Replace placeholders with actual database credentials)
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database';

$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables for form inputs
$customer = $product = $price = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $customer = htmlspecialchars($_POST['customer']);
    $product = htmlspecialchars($_POST['product']);
    $price = $_POST['price'];

    // Build SQL query based on form inputs
    $sql = "SELECT customers.name AS customer, products.name AS product, sales.price, sales.sale_date 
            FROM sales
            INNER JOIN customers ON sales.customer_id = customers.id
            INNER JOIN products ON sales.product_id = products.id
            WHERE 1 ";

    if (!empty($customer)) {
        $sql .= "AND customers.name LIKE '%$customer%' ";
    }
    if (!empty($product)) {
        $sql .= "AND products.name LIKE '%$product%' ";
    }
    if (!empty($price)) {
        $sql .= "AND sales.price = '$price' ";
    }

    // Execute SQL query
    $result = $mysqli->query($sql);

    // Initialize array to store filtered results
    $filteredResults = array();

    // Fetch filtered results and store in array
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $filteredResults[] = $row;
        }
    }
}

// Close database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Data Filter</title>
</head>
<body>
    <h2>Sales Data Filter</h2>
    <form method="post">
        <label for="customer">Customer:</label>
        <input type="text" name="customer" id="customer" value="<?php echo $customer; ?>">
        <br>
        <label for="product">Product:</label>
        <input type="text" name="product" id="product" value="<?php echo $product; ?>">
        <br>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" value="<?php echo $price; ?>">
        <br>
        <button type="submit">Filter</button>
    </form>

    <hr>

    <h3>Filtered Results:</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Product</th>
                <th>Price</th>
                <th>Sale Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Output filtered results in table
            if (!empty($filteredResults)) {
                foreach ($filteredResults as $result) {
                    echo "<tr>";
                    echo "<td>" . $result['customer'] . "</td>";
                    echo "<td>" . $result['product'] . "</td>";
                    echo "<td>" . $result['price'] . "</td>";
                    echo "<td>" . $result['sale_date'] . "</td>";
                    echo "</tr>";
                }

                // Calculate total price of filtered entries
                $totalPrice = array_sum(array_column($filteredResults, 'price'));

                // Output total price row
                echo "<tr><td colspan='4'><strong>Total Price:</strong> $totalPrice</td></tr>";
            } else {
                echo "<tr><td colspan='4'>No results found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

