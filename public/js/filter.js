document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    let currentCategory = '';
    let currentLevel = '';
    let currentPrice = '';
    let currentSort = '';
    let isLoading = false;

    // Determine current page type
    const currentPageType = getCurrentPageType();

    // Initialize loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-indicator';
    loadingIndicator.innerHTML = '<div class="spinner"></div><p>Đang tải...</p>';

    // Add loading indicator to the appropriate container
    const itemsContainer = document.getElementById(currentPageType + '-container');
    if (itemsContainer) {
        itemsContainer.parentNode.insertBefore(loadingIndicator, itemsContainer.nextSibling);
    }

    // Function to determine current page type
    function getCurrentPageType() {
        const path = window.location.pathname;
        if (path.includes('/services')) {
            return 'services';
        } else if (path.includes('/products')) {
            return 'products';
        } else if (path.includes('/news')) {
            return 'news';
        }
        return 'services'; // Default to services
    }

    // Category filter handling
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            currentCategory = this.value;
            currentPage = 1;
            fetchItems();
        });
    }

    // Level filter handling (for services)
    const levelFilter = document.getElementById('levelFilter');
    if (levelFilter) {
        levelFilter.addEventListener('change', function() {
            currentLevel = this.value;
            currentPage = 1;
            fetchItems();
        });
    }

    // Price filter handling (for products)
    const priceFilter = document.getElementById('priceFilter');
    if (priceFilter) {
        priceFilter.addEventListener('change', function() {
            currentPrice = this.value;
            currentPage = 1;
            fetchItems();
        });
    }

    // Tab filter handling
    const filterTabs = document.querySelectorAll('.filter-tab');
    if (filterTabs.length > 0) {
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                filterTabs.forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');

                // Get sort value from data attribute
                currentSort = this.getAttribute('data-sort');
                currentPage = 1;
                fetchItems();
            });
        });
    }

    // Pagination handling
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            currentPage = url.searchParams.get('page') || 1;
            fetchItems();

            // Scroll to top of items section
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) {
                filterHeader.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

    // Function to fetch items via AJAX
    function fetchItems() {
        if (isLoading) return;
        isLoading = true;

        // Show loading indicator
        loadingIndicator.classList.add('active');

        // Build URL with filter parameters
        const url = new URL(window.location.origin + window.location.pathname);

        // Add category filter if set
        if (currentCategory) {
            url.searchParams.set('category_id', currentCategory);
        }

        // Add level filter for services
        if (currentLevel && currentPageType === 'services') {
            url.searchParams.set('level', currentLevel);
        }

        // Add price filter for products
        if (currentPrice && currentPageType === 'products') {
            url.searchParams.set('price', currentPrice);
        }

        // Add sort filter if set
        if (currentSort) {
            url.searchParams.set('sort', currentSort);
        }

        // Add page number
        url.searchParams.set('page', currentPage);

        // Add format parameter to indicate AJAX request
        url.searchParams.set('format', 'json');

        // Update browser URL without reloading
        updateBrowserUrl();

        // Fetch filtered items
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update items container
            if (itemsContainer && data.html) {
                itemsContainer.innerHTML = data.html;
            }

            // Update pagination
            const paginationContainer = document.querySelector('.pagination-container');
            if (paginationContainer && data.pagination) {
                paginationContainer.innerHTML = data.pagination;
            }

            // Update filter count
            const filterCount = document.querySelector('.filter-count');
            if (filterCount && data.count) {
                let itemType = 'dịch vụ';
                if (currentPageType === 'products') {
                    itemType = 'sản phẩm';
                } else if (currentPageType === 'news') {
                    itemType = 'bài viết';
                }
                filterCount.textContent = `Hiển thị ${data.count.visible} / ${data.count.total} ${itemType}`;
            }
        })
        .catch(error => {
            console.error(`Error fetching ${currentPageType}:`, error);
        })
        .finally(() => {
            // Hide loading indicator
            loadingIndicator.classList.remove('active');
            isLoading = false;
        });
    }

    // Function to update browser URL without page reload
    function updateBrowserUrl() {
        const url = new URL(window.location.href);

        // Update or remove category parameter
        if (currentCategory) {
            url.searchParams.set('category_id', currentCategory);
        } else {
            url.searchParams.delete('category_id');
        }

        // Update or remove level parameter (for services)
        if (currentLevel && currentPageType === 'services') {
            url.searchParams.set('level', currentLevel);
        } else {
            url.searchParams.delete('level');
        }

        // Update or remove price parameter (for products)
        if (currentPrice && currentPageType === 'products') {
            url.searchParams.set('price', currentPrice);
        } else {
            url.searchParams.delete('price');
        }

        // Update or remove sort parameter
        if (currentSort) {
            url.searchParams.set('sort', currentSort);
        } else {
            url.searchParams.delete('sort');
        }

        // Update or remove page parameter
        if (currentPage > 1) {
            url.searchParams.set('page', currentPage);
        } else {
            url.searchParams.delete('page');
        }

        // Update browser history without reloading
        window.history.pushState({}, '', url);
    }

    // Initialize: Set initial values from URL
    const urlParams = new URLSearchParams(window.location.search);

    // Set initial category
    if (urlParams.has('category_id')) {
        currentCategory = urlParams.get('category_id');
        if (categoryFilter) {
            categoryFilter.value = currentCategory;
        }
    }

    // Set initial level (for services)
    if (urlParams.has('level') && currentPageType === 'services') {
        currentLevel = urlParams.get('level');
        if (levelFilter) {
            levelFilter.value = currentLevel;
        }
    }

    // Set initial price (for products)
    if (urlParams.has('price') && currentPageType === 'products') {
        currentPrice = urlParams.get('price');
        if (priceFilter) {
            priceFilter.value = currentPrice;
        }
    }

    // Set initial sort
    if (urlParams.has('sort')) {
        currentSort = urlParams.get('sort');
        filterTabs.forEach(tab => {
            if (tab.getAttribute('data-sort') === currentSort) {
                filterTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            }
        });
    }
});
