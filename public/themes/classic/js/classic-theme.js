/**
 * Classic Theme JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme functionality
    initializeTheme();
});

function initializeTheme() {
    console.log('Classic Theme initialized');
    
    // Initialize components
    initializeCart();
    initializeWishlist();
    initializeSearch();
    initializeQuickView();
}

/**
 * Shopping Cart functionality
 */
function initializeCart() {
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });
}

function addToCart(productId) {
    // Show loading state
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Добавление...';
    button.disabled = true;
    
    // Simulate API call
    fetch('/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(data.cart_count);
            
            // Show success feedback
            button.innerHTML = '<i class="fas fa-check"></i> Добавлено';
            button.classList.add('success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('success');
                button.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message || 'Ошибка добавления в корзину');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show error message
        showNotification('Ошибка добавления товара в корзину', 'error');
    });
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        
        // Add animation
        cartCountElement.classList.add('bounce');
        setTimeout(() => {
            cartCountElement.classList.remove('bounce');
        }, 500);
    }
}

/**
 * Wishlist functionality
 */
function initializeWishlist() {
    const wishlistButtons = document.querySelectorAll('.btn-wishlist');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });
}

function toggleWishlist(productId, button) {
    const isActive = button.classList.contains('active');
    const icon = button.querySelector('i');
    
    // Toggle visual state immediately
    if (isActive) {
        icon.className = 'far fa-heart';
        button.classList.remove('active');
    } else {
        icon.className = 'fas fa-heart';
        button.classList.add('active');
    }
    
    // Make API call
    fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId })
    })
    .catch(error => {
        console.error('Error toggling wishlist:', error);
        
        // Revert on error
        if (isActive) {
            icon.className = 'fas fa-heart';
            button.classList.add('active');
        } else {
            icon.className = 'far fa-heart';
            button.classList.remove('active');
        }
    });
}

/**
 * Search functionality
 */
function initializeSearch() {
    const searchForm = document.querySelector('.search-box form');
    const searchInput = document.querySelector('.search-box input');
    
    if (searchForm && searchInput) {
        // Auto-submit on enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });
        
        // Add search suggestions (placeholder for future implementation)
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value;
            if (query.length > 2) {
                // TODO: Implement search suggestions
                console.log('Search suggestions for:', query);
            }
        }, 300));
    }
}

/**
 * Quick View functionality
 */
function initializeQuickView() {
    const quickViewButtons = document.querySelectorAll('.btn-quick-view');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            openQuickView(productId);
        });
    });
}

function openQuickView(productId) {
    // Create and show modal
    const modal = createQuickViewModal(productId);
    document.body.appendChild(modal);
    
    // Fetch product data
    fetch(`/api/products/${productId}/quick-view`)
        .then(response => response.json())
        .then(data => {
            populateQuickViewModal(modal, data);
        })
        .catch(error => {
            console.error('Error loading quick view:', error);
            modal.remove();
        });
}

function createQuickViewModal(productId) {
    const modal = document.createElement('div');
    modal.className = 'quick-view-modal';
    modal.innerHTML = `
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Быстрый просмотр</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Загрузка...
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Close modal events
    modal.querySelector('.close-modal').addEventListener('click', () => modal.remove());
    modal.querySelector('.modal-backdrop').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            modal.remove();
        }
    });
    
    return modal;
}

function populateQuickViewModal(modal, productData) {
    const modalBody = modal.querySelector('.modal-body');
    modalBody.innerHTML = `
        <div class="quick-view-content">
            <div class="product-image">
                <img src="${productData.image || '/images/no-image.png'}" alt="${productData.name}">
            </div>
            <div class="product-details">
                <h4>${productData.name}</h4>
                <p class="product-price">${formatPrice(productData.price)}</p>
                <p class="product-description">${productData.description || ''}</p>
                <div class="product-actions">
                    <button class="btn-add-to-cart" data-product-id="${productData.id}">
                        <i class="fas fa-shopping-cart"></i> В корзину
                    </button>
                    <button class="btn-wishlist" data-product-id="${productData.id}">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Re-initialize buttons for the modal
    initializeCart();
    initializeWishlist();
}

/**
 * Utility functions
 */
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

function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
    }).format(price);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add some basic CSS for notifications and modals
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        border-radius: 0.375rem;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }
    
    .notification-success { background: #059669; }
    .notification-error { background: #dc2626; }
    .notification-info { background: #2563eb; }
    
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    
    .cart-count.bounce {
        animation: bounce 0.5s ease;
    }
    
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }
    
    .btn-add-to-cart.success {
        background: #059669 !important;
    }
    
    .quick-view-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
    }
    
    .modal-backdrop {
        background: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .modal-content {
        background: white;
        border-radius: 0.5rem;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .loading {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
    
    .quick-view-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    @media (max-width: 640px) {
        .quick-view-content {
            grid-template-columns: 1fr;
        }
    }
`;
document.head.appendChild(style);
