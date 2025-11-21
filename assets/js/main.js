// DOM Elements
const productsGrid = document.querySelector('.products-grid');

// Fetch and display products
async function loadProducts() {
    try {
        const response = await fetch('includes/get_products.php');
        const products = await response.json();
        
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Display products in the grid
function displayProducts(products) {
    if (!productsGrid) return;

    products.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Create product card element
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    
    card.innerHTML = `
        <img src="${product.image || 'assets/images/default-product.jpg'}" alt="${product.name}">
        <h3>${product.name}</h3>
        <p>${product.description}</p>
        <p class="price">₹${product.price}</p>
        ${product.is_organic ? '<span class="organic-badge">Organic</span>' : ''}
        <button onclick="addToCart(${product.id})" class="btn">Add to Cart</button>
    `;
    
    return card;
}

// Cart functionality
async function addToCart(productId) {
    try {
        const response = await fetch('includes/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Product added to cart!', 'success');
            updateCartCount();
        } else {
            showNotification('Failed to add product to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding to cart', 'error');
    }
}

// Update cart count in navigation
async function updateCartCount() {
    try {
        const response = await fetch('includes/get_cart_count.php');
        const data = await response.json();
        
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = data.count;
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Search and filter functionality
function setupSearch() {
    const searchInput = document.querySelector('#search-input');
    if (!searchInput) return;

    searchInput.addEventListener('input', debounce(async (e) => {
        const searchTerm = e.target.value;
        try {
            const response = await fetch(`includes/search_products.php?term=${searchTerm}`);
            const products = await response.json();
            
            productsGrid.innerHTML = '';
            displayProducts(products);
        } catch (error) {
            console.error('Error searching products:', error);
        }
    }, 300));
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    setupSearch();
    updateCartCount();
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// Add corresponding CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 4px;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    }

    .notification.success {
        background-color: #4CAF50;
    }

    .notification.error {
        background-color: #f44336;
    }

    .notification.info {
        background-color: #2196F3;
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

    .organic-badge {
        background-color: #8BC34A;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8em;
    }

    .error {
        border-color: #f44336 !important;
    }
`;

document.head.appendChild(style); 