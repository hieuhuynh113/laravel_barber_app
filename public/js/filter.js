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

    // Auto filter handling
    const autoFilters = document.querySelectorAll('.auto-filter');
    if (autoFilters.length > 0) {
        autoFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                // Reset page to 1 when filter changes
                currentPage = 1;

                // Fetch items immediately when filter changes
                fetchItems();
            });
        });
    }

    // Clear all filters button
    const clearAllFiltersBtn = document.getElementById('clearAllFilters');
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function() {
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"].auto-filter').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Uncheck all radio buttons
            document.querySelectorAll('input[type="radio"].auto-filter').forEach(radio => {
                radio.checked = false;
            });

            // Reset page to 1
            currentPage = 1;

            // Fetch items with cleared filters
            fetchItems();
        });
    }

    // Tab filter handling removed

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

        // Scroll to top of services container
        if (itemsContainer) {
            window.scrollTo({
                top: itemsContainer.offsetTop - 100,
                behavior: 'smooth'
            });
        }

        // Build URL with filter parameters
        const url = new URL(window.location.origin + window.location.pathname);

        // Get all checked category type checkboxes
        const categoryTypeCheckboxes = document.querySelectorAll('input[name="category_type"]:checked');
        if (categoryTypeCheckboxes.length > 0) {
            categoryTypeCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('category_type', checkbox.value);
            });
        }

        // Get all checked category checkboxes
        const categoryCheckboxes = document.querySelectorAll('input[name="category_id[]"]:checked');
        if (categoryCheckboxes.length > 0) {
            categoryCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('category_id[]', checkbox.value);
            });
        }

        // Get all checked level checkboxes
        const levelCheckboxes = document.querySelectorAll('input[name="level[]"]:checked');
        if (levelCheckboxes.length > 0) {
            levelCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('level[]', checkbox.value);
            });
        }

        // Get all checked price checkboxes
        const priceCheckboxes = document.querySelectorAll('input[name="price[]"]:checked');
        if (priceCheckboxes.length > 0) {
            priceCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('price[]', checkbox.value);
            });
        }

        // Get selected sort radio
        const sortRadio = document.querySelector('input[name="sort"]:checked');
        if (sortRadio) {
            url.searchParams.set('sort', sortRadio.value);
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

                // Reinitialize appointment buttons after AJAX load
                initAppointmentButtons();
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

    // Function to reinitialize appointment buttons after AJAX load
    function initAppointmentButtons() {
        // Check if appointment-auth-check.js has initialized a function for this
        if (typeof initializeAppointmentButtons === 'function') {
            initializeAppointmentButtons();
        } else {
            // Fallback initialization
            const appointmentBtns = document.querySelectorAll('.appointment-btn');
            if (appointmentBtns.length > 0) {
                appointmentBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        // If user is not logged in, show login modal
                        if (document.body.classList.contains('user-guest')) {
                            e.preventDefault();
                            const targetUrl = this.getAttribute('href');
                            localStorage.setItem('redirectAfterLogin', targetUrl);

                            // Show login modal if it exists
                            const authModal = document.getElementById('authModal');
                            if (authModal) {
                                const bsModal = new bootstrap.Modal(authModal);
                                bsModal.show();
                            }
                        }
                    });
                });
            }
        }
    }

    // Function to update browser URL without page reload
    function updateBrowserUrl() {
        const url = new URL(window.location.href);

        // Clear all existing parameters
        Array.from(url.searchParams.keys()).forEach(key => {
            url.searchParams.delete(key);
        });

        // Get all checked category type checkboxes
        const categoryTypeCheckboxes = document.querySelectorAll('input[name="category_type"]:checked');
        if (categoryTypeCheckboxes.length > 0) {
            categoryTypeCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('category_type', checkbox.value);
            });
        }

        // Get all checked category checkboxes
        const categoryCheckboxes = document.querySelectorAll('input[name="category_id[]"]:checked');
        if (categoryCheckboxes.length > 0) {
            categoryCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('category_id[]', checkbox.value);
            });
        }

        // Get all checked level checkboxes
        const levelCheckboxes = document.querySelectorAll('input[name="level[]"]:checked');
        if (levelCheckboxes.length > 0) {
            levelCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('level[]', checkbox.value);
            });
        }

        // Get all checked price checkboxes
        const priceCheckboxes = document.querySelectorAll('input[name="price[]"]:checked');
        if (priceCheckboxes.length > 0) {
            priceCheckboxes.forEach((checkbox, index) => {
                url.searchParams.append('price[]', checkbox.value);
            });
        }

        // Get selected sort radio
        const sortRadio = document.querySelector('input[name="sort"]:checked');
        if (sortRadio) {
            url.searchParams.set('sort', sortRadio.value);
        }

        // Update or remove page parameter
        if (currentPage > 1) {
            url.searchParams.set('page', currentPage);
        }

        // Update browser history without reloading
        window.history.pushState({}, '', url);
    }

    // Initialize appointment buttons on page load
    initAppointmentButtons();

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
        // Update sort radio if available
        const sortRadio = document.querySelector(`input[name="sortFilter"][value="${currentSort}"]`);
        if (sortRadio) {
            sortRadio.checked = true;
        }
    }
});
