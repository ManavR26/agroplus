<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

// Store the previous page in session
$_SESSION['previous_page'] = 'customer_dashboard.php';

// Get all products from database
$sql = "SELECT p.*, u.username as farmer_name 
        FROM products p 
        LEFT JOIN users u ON p.farmer_id = u.id 
        WHERE p.stock > 0";

$result = mysqli_query($conn, $sql);
$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Get all categories
$categories = [];
$cat_sql = "SELECT DISTINCT category FROM products";
$cat_result = mysqli_query($conn, $cat_sql);
while($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row['category'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add this meta tag to prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Update the back button handling script -->
    <script>
        // Handle back button
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };

        // Redirect to customer dashboard on back button
        window.history.pushState({page: 'products'}, '', '');
        window.onpopstate = function() {
            window.location.href = '<?php echo $_SESSION['previous_page']; ?>';
        };

        // Alternative method using beforeunload
        window.addEventListener('beforeunload', function(e) {
            if (window.performance && window.performance.navigation.type === 2) {
                window.location.href = '<?php echo $_SESSION['previous_page']; ?>';
            }
        });
    </script>

    <style>
        .products-container {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .products-header {
            text-align: center;
            margin-bottom: 40px;
            color: #2e7d32;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 1.2em;
            color: #4CAF50;
            font-weight: bold;
            margin: 10px 0;
        }

        .organic-badge {
            background: #8BC34A;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
            font-size: 0.9em;
        }

        .add-to-cart-btn {
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .add-to-cart-btn:hover {
            background: #45a049;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease-out;
        }

        .success-notification {
            background: #4CAF50;
        }

        .error-notification {
            background: #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="products-container" role="main">
        <header class="products-header">
            <h1>Available Products</h1>
            <p>Browse through our selection of fresh agricultural products</p>
        </header>

        <section class="products-grid" aria-label="Product list">
            <?php if(empty($products)): ?>
                <p>No products found.</p>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <?php 
                    $stored = (string)$product['image'];
                    $src = '';
                    if($stored !== ''){
                        if(strpos($stored, 'assets/') === 0 || strpos($stored, 'uploads/') === 0){
                            $src = $stored;
                        } else {
                            if(file_exists('assets/images/products/' . $stored)){
                                $src = 'assets/images/products/' . $stored;
                            } elseif(file_exists('uploads/products/' . $stored)){
                                $src = 'uploads/products/' . $stored;
                            }
                        }
                    }
                    if($src === ''){ $src = 'assets/images/default-product.jpg'; }
                    $fullDesc = (string)$product['description'];
                    ?>
                    <article class="product-card" itemscope itemtype="http://schema.org/Product">
                        <figure>
                            <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" itemprop="image">
                        </figure>
                        <h3 itemprop="name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if($product['is_organic']): ?>
                            <span class="organic-badge">Organic</span>
                        <?php endif; ?>
                        <p class="product-description" data-full="<?php echo htmlspecialchars($fullDesc); ?>" itemprop="description"></p>
                        <button type="button" class="read-more-btn" aria-expanded="false" hidden>Read more</button>
                        <p class="product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">₹<span itemprop="price"><?php echo htmlspecialchars($product['price']); ?></span></p>
                        <p>Stock: <span itemprop="inventoryLevel"><?php echo htmlspecialchars($product['stock']); ?></span> units</p>
                        <p>Seller: <span itemprop="brand"><?php echo htmlspecialchars($product['farmer_name']); ?></span></p>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="add-to-cart-btn">Add to Cart</button>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <div id="notification" class="notification"></div>

    <script>
        function addToCart(productId) {
            fetch('includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.success ? 'Product added to cart!' : data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    updateCartCount();
                }
            })
            .catch(error => {
                showNotification('Error adding to cart', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}-notification`;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        function updateCartCount() {
            fetch('includes/get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count;
                    }
                });
        }
    </script>

    <script>
        // Read More / Read Less for product descriptions
        (function(){
            const MAX_LEN = 160;
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                const p = card.querySelector('.product-description');
                const btn = card.querySelector('.read-more-btn');
                if(!p) return;
                const full = p.getAttribute('data-full') || '';
                if(full.length <= MAX_LEN){
                    p.textContent = full;
                    if(btn) btn.hidden = true;
                    return;
                }
                const shortText = full.slice(0, MAX_LEN) + '...';
                p.textContent = shortText;
                if(btn){
                    btn.hidden = false;
                    btn.addEventListener('click', () => {
                        const expanded = btn.getAttribute('aria-expanded') === 'true';
                        const nextExpanded = !expanded;
                        btn.setAttribute('aria-expanded', String(nextExpanded));
                        if(nextExpanded){
                            p.textContent = full;
                            btn.textContent = 'Read less';
                        } else {
                            p.textContent = shortText;
                            btn.textContent = 'Read more';
                        }
                    });
                }
            });
        })();
    </script>
</body>
</html> 