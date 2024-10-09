<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = mysqli_real_escape_string($db, $_POST['name']);
                $price = floatval($_POST['price']);
                $size = mysqli_real_escape_string($db, $_POST['size']);
                $stock = intval($_POST['stock']);
                $color = mysqli_real_escape_string($db, $_POST['color']);
                
                $query = "INSERT INTO products (name, price, size, stock, color) VALUES ('$name', $price, '$size', $stock, '$color')";                mysqli_query($db, $query);
                break;
            
            case 'edit':
                $id = intval($_POST['id']);
                $name = mysqli_real_escape_string($db, $_POST['name']);
                $price = floatval($_POST['price']);
                $size = mysqli_real_escape_string($db, $_POST['size']);
                $stock = intval($_POST['stock']);
                $category_id = intval($_POST['category_id']);
                
                $query = "UPDATE products SET name='$name', price=$price, size='$size', stock=$stock, category_id=$category_id WHERE id=$id";
                mysqli_query($db, $query);
                break;
            
            case 'delete':
                $id = intval($_POST['id']);
                $query = "DELETE FROM products WHERE id=$id";
                mysqli_query($db, $query);
                break;
        }
    }
}

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
if ($search) {
    $query .= " AND (p.name LIKE '%$search%' OR p.size LIKE '%$search%')";
}
if ($category_filter) {
    $query .= " AND p.category_id = $category_filter";
}
$result = mysqli_query($db, $query);

// Fetch categories
$categories_result = mysqli_query($db, "SELECT * FROM categories");
$categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jordan Shoes Inventory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<style>
    body{
        font-family: 'Quicksand', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    header {
        background-color: transparent;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }
    header img {
        max-width: 300px;
        height: auto;
    }
    nav {
        margin: 20px 0 0;
        text-align: center;
    }
    nav a {
        font-size: 15px;
        background-color: transparent;
        border-radius: 10px;
        padding: 10px 15px;
        color: black;
        text-decoration: none;
        transition: 0.5s;
        display: inline-block;
        margin: 0 5px;
    }
    nav a:hover {
        color: #aaa;
        transform: translateY(-3px);
    }
    .search-form {
        margin: 20px 0;
        text-align: right;
    }
    .search-form input[type="text"] {
        padding: 10px;
        width: 300px;
    }
    .search-form select {
        padding: 10px;
    }
    .search-form button {
        padding: 10px 20px;
        background-color: red;
        border-radius:  20% 20%;
        color: white;
        border: none;
        cursor: pointer;
    }
</style>
<body>
    <header>
        <center><img src="imh/INVENTORY-10-7-2024.png" alt="Shoe Store Logo" width="450"></center>
        <nav>
            <a href="">Contact</a>
            <a href="">About</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
        <br>
        <center><h2>Add New Shoes</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="add">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="size">Size:</label>
            <input type="text" id="size" name="size" required>
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>
            <label for="stock">Color:</label>
            <input type="texr" id="stock" name="color" required>
            <button type="submit">Add Product</button>
        </form>
        </center> <br>
        <br>
        <h2>Manage Inventory</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Size</th>
                <th>Stock</th>
                <th>Color</th>
                <th>Category</th>
                <th>Edit</th>
            </tr>

            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>₱<?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['size']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                    <td><?php echo htmlspecialchars($product['color']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
                            <input type="text" name="size" value="<?php echo htmlspecialchars($product['size']); ?>" required>
                            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                            <input type="" name="text" value="<?php echo htmlspecialchars($product['color']); ?>" required>
                            <select name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Update</button>
                        </form>

                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>