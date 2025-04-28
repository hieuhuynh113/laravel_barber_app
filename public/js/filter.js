document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    let currentCategory = '';
    let currentLevel = '';
    let currentPrice = '';
    let currentSort = '';
    let currentSearch = '';
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

    // Initialize active filters display
    updateActiveFilters();

    // Auto filter handling
    const autoFilters = document.querySelectorAll('.auto-filter');
    if (autoFilters.length > 0) {
        autoFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                // Reset page to 1 when filter changes
                currentPage = 1;

                // Update active filters display
                updateActiveFilters();

                // Fetch items immediately when filter changes, không cuộn trang
                fetchItemsWithScroll(false);
            });
        });
    }

    // Search input handling
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');

    if (searchInput && searchButton) {
        // Set initial search value
        currentSearch = searchInput.value;

        // Handle search button click
        searchButton.addEventListener('click', function() {
            currentSearch = searchInput.value;
            currentPage = 1;
            updateActiveFilters();
            fetchItemsWithScroll(false);
        });

        // Handle Enter key press in search input
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                currentSearch = searchInput.value;
                currentPage = 1;
                updateActiveFilters();
                fetchItemsWithScroll(false);
            }
        });
    }

    // Mobile filter sidebar handling
    const showFilterSidebarBtn = document.getElementById('showFilterSidebar');
    const closeFilterSidebarBtn = document.getElementById('closeFilterSidebar');
    const filterSidebar = document.querySelector('.filter-sidebar');
    const filterBackdrop = document.querySelector('.filter-backdrop');

    if (showFilterSidebarBtn && filterSidebar && filterBackdrop) {
        showFilterSidebarBtn.addEventListener('click', function() {
            filterSidebar.classList.add('show');
            filterBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    }

    if (closeFilterSidebarBtn && filterSidebar && filterBackdrop) {
        closeFilterSidebarBtn.addEventListener('click', function() {
            filterSidebar.classList.remove('show');
            filterBackdrop.classList.remove('show');
            document.body.style.overflow = ''; // Enable scrolling
        });
    }

    if (filterBackdrop && filterSidebar) {
        filterBackdrop.addEventListener('click', function() {
            filterSidebar.classList.remove('show');
            filterBackdrop.classList.remove('show');
            document.body.style.overflow = ''; // Enable scrolling
        });
    }

    // Clear all filters button
    const clearAllFiltersBtn = document.getElementById('clearAllFilters');
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function() {
            // Clear search input
            if (searchInput) {
                searchInput.value = '';
                currentSearch = '';
            }

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

            // Update active filters display
            updateActiveFilters();

            // Fetch items with cleared filters, không cuộn trang
            fetchItemsWithScroll(false);
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

            // Lưu vị trí cuộn hiện tại trước khi thực hiện phân trang
            const currentScrollPosition = window.scrollY;

            // Đặt cờ để biết đây là thao tác phân trang
            const isPaginationAction = true;

            // Gọi fetchItems với tham số isPaginationAction
            fetchItemsWithScroll(isPaginationAction, currentScrollPosition);
        }
    });

    // Hàm fetchItems có thêm tham số để xử lý cuộn trang
    function fetchItemsWithScroll(isPagination = false, scrollPosition = 0) {
        if (isLoading) return;
        isLoading = true;

        // Show loading indicator
        loadingIndicator.classList.add('active');

        // Chỉ cuộn trang khi thực hiện phân trang
        if (isPagination && itemsContainer) {
            window.scrollTo({
                top: itemsContainer.offsetTop - 100,
                behavior: 'smooth'
            });
        }

        // Gọi hàm fetchItems gốc
        fetchItems(false);
    }

    // Function to fetch items via AJAX
    function fetchItems(checkLoading = true) {
        if (checkLoading && isLoading) return;
        isLoading = true;

        // Show loading indicator
        loadingIndicator.classList.add('active');

        // Build URL with filter parameters
        const url = new URL(window.location.origin + window.location.pathname);

        // Add search parameter if set
        if (currentSearch) {
            url.searchParams.set('search', currentSearch);
        }

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

        // Get selected time radio (for news page)
        const timeRadio = document.querySelector('input[name="time"]:checked');
        if (timeRadio && timeRadio.value) {
            url.searchParams.set('time', timeRadio.value);
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

        // Add search parameter if set
        if (currentSearch) {
            url.searchParams.set('search', currentSearch);
        }

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

        // Get selected time radio (for news page)
        const timeRadio = document.querySelector('input[name="time"]:checked');
        if (timeRadio && timeRadio.value) {
            url.searchParams.set('time', timeRadio.value);
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
        // Check if category checkboxes exist and set them
        if (Array.isArray(currentCategory)) {
            currentCategory.forEach(catId => {
                const checkbox = document.querySelector(`input[name="category_id[]"][value="${catId}"]`);
                if (checkbox) checkbox.checked = true;
            });
        } else {
            const checkbox = document.querySelector(`input[name="category_id[]"][value="${currentCategory}"]`);
            if (checkbox) checkbox.checked = true;
        }
    }

    // Set initial level (for services)
    if (urlParams.has('level') && currentPageType === 'services') {
        currentLevel = urlParams.get('level');
        // Check if level checkboxes exist and set them
        if (Array.isArray(currentLevel)) {
            currentLevel.forEach(level => {
                const checkbox = document.querySelector(`input[name="level[]"][value="${level}"]`);
                if (checkbox) checkbox.checked = true;
            });
        } else {
            const checkbox = document.querySelector(`input[name="level[]"][value="${currentLevel}"]`);
            if (checkbox) checkbox.checked = true;
        }
    }

    // Set initial price (for products)
    if (urlParams.has('price') && currentPageType === 'products') {
        currentPrice = urlParams.get('price');
        // Check if price checkboxes exist and set them
        if (Array.isArray(currentPrice)) {
            currentPrice.forEach(price => {
                const checkbox = document.querySelector(`input[name="price[]"][value="${price}"]`);
                if (checkbox) checkbox.checked = true;
            });
        } else {
            const checkbox = document.querySelector(`input[name="price[]"][value="${currentPrice}"]`);
            if (checkbox) checkbox.checked = true;
        }
    }

    // Set initial sort
    if (urlParams.has('sort')) {
        currentSort = urlParams.get('sort');
        // Update sort radio if available
        const sortRadio = document.querySelector(`input[name="sort"][value="${currentSort}"]`);
        if (sortRadio) {
            sortRadio.checked = true;
        }
    }

    // Set initial search
    if (urlParams.has('search')) {
        currentSearch = urlParams.get('search');
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = currentSearch;
        }
    }

    // Function to update active filters display
    function updateActiveFilters() {
        const activeFiltersContainer = document.getElementById('active-filters');
        if (!activeFiltersContainer) return;

        // Clear current active filters
        activeFiltersContainer.innerHTML = '';

        let hasActiveFilters = false;

        // Add search filter if active
        if (currentSearch) {
            hasActiveFilters = true;
            const searchFilter = document.createElement('span');
            searchFilter.className = 'active-filter-tag';
            searchFilter.innerHTML = `<i class="fas fa-search me-1"></i> ${currentSearch} <span class="remove-filter" data-filter-type="search"><i class="fas fa-times"></i></span>`;
            activeFiltersContainer.appendChild(searchFilter);
        }

        // Add category filters
        const categoryCheckboxes = document.querySelectorAll('input[name="category_id[]"]:checked');
        categoryCheckboxes.forEach(checkbox => {
            hasActiveFilters = true;
            const categoryName = checkbox.nextElementSibling.textContent.trim();
            const categoryFilter = document.createElement('span');
            categoryFilter.className = 'active-filter-tag';
            categoryFilter.innerHTML = `<i class="fas fa-tag me-1"></i> ${categoryName} <span class="remove-filter" data-filter-type="category" data-filter-id="${checkbox.value}"><i class="fas fa-times"></i></span>`;
            activeFiltersContainer.appendChild(categoryFilter);
        });

        // Add price filters
        const priceCheckboxes = document.querySelectorAll('input[name="price[]"]:checked');
        priceCheckboxes.forEach(checkbox => {
            hasActiveFilters = true;
            const priceName = checkbox.nextElementSibling.textContent.trim();
            const priceFilter = document.createElement('span');
            priceFilter.className = 'active-filter-tag';
            priceFilter.innerHTML = `<i class="fas fa-money-bill-wave me-1"></i> ${priceName} <span class="remove-filter" data-filter-type="price" data-filter-id="${checkbox.value}"><i class="fas fa-times"></i></span>`;
            activeFiltersContainer.appendChild(priceFilter);
        });

        // Add time filter if active (for news page)
        const timeRadio = document.querySelector('input[name="time"]:checked');
        if (timeRadio && timeRadio.value) {
            hasActiveFilters = true;
            const timeName = timeRadio.nextElementSibling.textContent.trim();
            const timeFilter = document.createElement('span');
            timeFilter.className = 'active-filter-tag';
            timeFilter.innerHTML = `<i class="fas fa-clock me-1"></i> ${timeName} <span class="remove-filter" data-filter-type="time"><i class="fas fa-times"></i></span>`;
            activeFiltersContainer.appendChild(timeFilter);
        }

        // Add sort filter if active
        const sortRadio = document.querySelector('input[name="sort"]:checked');
        if (sortRadio) {
            hasActiveFilters = true;
            const sortName = sortRadio.nextElementSibling.textContent.trim();
            const sortFilter = document.createElement('span');
            sortFilter.className = 'active-filter-tag';
            sortFilter.innerHTML = `<i class="fas fa-sort me-1"></i> ${sortName} <span class="remove-filter" data-filter-type="sort"><i class="fas fa-times"></i></span>`;
            activeFiltersContainer.appendChild(sortFilter);
        }

        // Add event listeners to remove buttons
        const removeButtons = document.querySelectorAll('.remove-filter');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filterType = this.dataset.filterType;
                const filterId = this.dataset.filterId;

                // Thêm hiệu ứng khi xóa bộ lọc
                const parentTag = this.closest('.active-filter-tag');
                if (parentTag) {
                    parentTag.style.transition = 'all 0.3s ease';
                    parentTag.style.opacity = '0';
                    parentTag.style.transform = 'translateX(-20px)';
                }

                if (filterType === 'search') {
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.value = '';
                        currentSearch = '';
                    }
                } else if (filterType === 'category') {
                    const checkbox = document.querySelector(`input[name="category_id[]"][value="${filterId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                } else if (filterType === 'price') {
                    const checkbox = document.querySelector(`input[name="price[]"][value="${filterId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                } else if (filterType === 'time') {
                    document.querySelectorAll('input[name="time"]').forEach(radio => {
                        radio.checked = false;
                    });
                    // Select the "All time" option if available
                    const allTimeRadio = document.querySelector('input[name="time"][value=""]');
                    if (allTimeRadio) {
                        allTimeRadio.checked = true;
                    }
                } else if (filterType === 'sort') {
                    document.querySelectorAll('input[name="sort"]').forEach(radio => {
                        radio.checked = false;
                    });
                }

                // Reset page to 1
                currentPage = 1;

                // Update active filters display
                updateActiveFilters();

                // Fetch items with updated filters, không cuộn trang
                fetchItemsWithScroll(false);
            });
        });

        // Show or hide the active filters container
        if (hasActiveFilters) {
            activeFiltersContainer.style.display = 'flex';
        } else {
            activeFiltersContainer.style.display = 'none';
        }
    }
});
