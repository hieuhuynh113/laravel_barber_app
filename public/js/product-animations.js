/**
 * Product Animations JavaScript
 * Thêm các hiệu ứng và animation cho trang sản phẩm
 */

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo biến
    let isFilterSidebarVisible = true;
    const filterSidebar = document.querySelector('.filter-sidebar');
    const productsContainer = document.getElementById('products-container');
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const closeFilterSidebarBtn = document.getElementById('closeFilterSidebar');
    
    // Thêm class product-item cho các sản phẩm để áp dụng animation
    const productItems = document.querySelectorAll('.col-md-6.col-lg-4.mb-4');
    if (productItems.length > 0) {
        productItems.forEach(item => {
            item.classList.add('product-item');
        });
    }
    
    // Thêm badge "Mới" cho sản phẩm mới (3 sản phẩm đầu tiên)
    const addProductBadges = () => {
        const productCards = document.querySelectorAll('.product-card');
        if (productCards.length > 0) {
            // Thêm badge "Mới" cho 3 sản phẩm đầu tiên
            for (let i = 0; i < Math.min(3, productCards.length); i++) {
                const badgeNew = document.createElement('div');
                badgeNew.className = 'product-badge badge-new';
                badgeNew.innerHTML = '<i class="fas fa-bolt me-1"></i> Mới';
                
                // Kiểm tra xem đã có badge chưa
                if (!productCards[i].querySelector('.product-badge')) {
                    productCards[i].appendChild(badgeNew);
                }
            }
            
            // Thêm badge "Bán chạy" cho sản phẩm thứ 4, 5, 6 (nếu có)
            for (let i = 3; i < Math.min(6, productCards.length); i++) {
                const badgeBestseller = document.createElement('div');
                badgeBestseller.className = 'product-badge badge-bestseller';
                badgeBestseller.innerHTML = '<i class="fas fa-fire me-1"></i> Bán chạy';
                
                // Kiểm tra xem đã có badge chưa
                if (!productCards[i].querySelector('.product-badge')) {
                    productCards[i].appendChild(badgeBestseller);
                }
            }
        }
    };
    
    // Thêm icon cho giá sản phẩm
    const addPriceIcons = () => {
        const priceElements = document.querySelectorAll('.product-card .price');
        priceElements.forEach(price => {
            // Kiểm tra xem đã có icon chưa
            if (!price.querySelector('.price-icon')) {
                const priceText = price.innerHTML;
                price.innerHTML = `<i class="fas fa-tag me-1 price-icon"></i> ${priceText}`;
            }
        });
    };
    
    // Thêm class cho icon trong nút chi tiết
    const addButtonIconClass = () => {
        const detailButtons = document.querySelectorAll('.product-card .btn-outline-primary .fa-arrow-right');
        detailButtons.forEach(icon => {
            icon.classList.add('btn-icon-animate');
        });
    };
    
    // Thêm hiệu ứng cho các mục trong bộ lọc
    const filterSections = document.querySelectorAll('.filter-section');
    if (filterSections.length > 0) {
        filterSections.forEach((section, index) => {
            // Thêm hiệu ứng xuất hiện với độ trễ
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                section.style.transition = 'all 0.5s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 100 * (index + 1));
        });
    }
    
    // Thêm hiệu ứng cho các checkbox và radio
    const filterInputs = document.querySelectorAll('.filter-section input[type="checkbox"], .filter-section input[type="radio"]');
    if (filterInputs.length > 0) {
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    // Thêm hiệu ứng khi được chọn
                    this.closest('.form-check').classList.add('filter-selected');
                    
                    // Thêm hiệu ứng pulse
                    const label = this.closest('.form-check').querySelector('.form-check-label');
                    label.classList.add('pulse-animation');
                    
                    // Xóa class sau khi animation kết thúc
                    setTimeout(() => {
                        label.classList.remove('pulse-animation');
                    }, 500);
                } else {
                    // Xóa hiệu ứng khi bỏ chọn
                    this.closest('.form-check').classList.remove('filter-selected');
                }
            });
        });
    }
    
    // Thêm hiệu ứng cho nút tìm kiếm
    const searchButton = document.getElementById('searchButton');
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            this.classList.add('search-button-clicked');
            setTimeout(() => {
                this.classList.remove('search-button-clicked');
            }, 300);
        });
    }
    
    // Thêm hiệu ứng cho nút xóa bộ lọc
    const clearAllFiltersBtn = document.getElementById('clearAllFilters');
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function() {
            // Thêm hiệu ứng khi nhấn nút
            this.classList.add('clear-button-clicked');
            
            // Thêm hiệu ứng reset cho các checkbox và radio
            filterInputs.forEach(input => {
                if (input.checked) {
                    const label = input.closest('.form-check').querySelector('.form-check-label');
                    label.classList.add('reset-animation');
                    
                    setTimeout(() => {
                        label.classList.remove('reset-animation');
                    }, 500);
                }
                
                input.closest('.form-check').classList.remove('filter-selected');
            });
            
            // Xóa hiệu ứng sau khi animation kết thúc
            setTimeout(() => {
                this.classList.remove('clear-button-clicked');
            }, 300);
        });
    }
    
    // Thêm hiệu ứng skeleton loading khi tải dữ liệu
    function showSkeletonLoading() {
        // Xóa nội dung hiện tại
        if (productsContainer) {
            productsContainer.innerHTML = '';
            productsContainer.className = 'row'; // Đảm bảo class row được giữ lại
            
            // Tạo 6 skeleton cards
            for (let i = 0; i < 6; i++) {
                const skeletonCol = document.createElement('div');
                skeletonCol.className = 'col-md-6 col-lg-4 mb-4';
                
                const skeletonCard = document.createElement('div');
                skeletonCard.className = 'skeleton-product';
                skeletonCard.innerHTML = `
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-badge"></div>
                        <div class="skeleton-title"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-text"></div>
                    </div>
                    <div class="skeleton-footer">
                        <div class="skeleton-price"></div>
                        <div class="skeleton-button"></div>
                    </div>
                `;
                
                skeletonCol.appendChild(skeletonCard);
                productsContainer.appendChild(skeletonCol);
            }
        }
    }
    
    // Ghi đè hàm fetchItems trong filter.js
    if (typeof window.originalFetchItems === 'undefined' && typeof fetchItems === 'function') {
        // Lưu hàm gốc
        window.originalFetchItems = fetchItems;
        
        // Ghi đè hàm fetchItems
        window.fetchItems = function(checkLoading = true) {
            if (checkLoading && isLoading) return;
            isLoading = true;
            
            // Hiển thị skeleton loading thay vì loading indicator
            showSkeletonLoading();
            
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
            
            // Get selected level radio
            const levelRadio = document.querySelector('input[name="level"]:checked');
            if (levelRadio) {
                url.searchParams.set('level', levelRadio.value);
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
            
            // Add format parameter for AJAX request
            url.searchParams.set('format', 'json');
            
            // Fetch data with AJAX
            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Thêm độ trễ nhỏ để hiển thị skeleton loading
                setTimeout(() => {
                    // Update container with new HTML
                    if (productsContainer) {
                        productsContainer.innerHTML = data.html;
                        
                        // Thêm class product-item cho các sản phẩm mới
                        const newProductItems = productsContainer.querySelectorAll('.col-md-6.col-lg-4.mb-4');
                        newProductItems.forEach(item => {
                            item.classList.add('product-item');
                        });
                        
                        // Thêm các hiệu ứng cho sản phẩm mới
                        addProductBadges();
                        addPriceIcons();
                        addButtonIconClass();
                        
                        // Thêm hiệu ứng xuất hiện cho các mục sản phẩm
                        const productItems = productsContainer.querySelectorAll('.product-item');
                        productItems.forEach((item, index) => {
                            item.style.opacity = '0';
                            item.style.transform = 'translateY(20px)';
                            
                            setTimeout(() => {
                                item.style.transition = 'all 0.5s ease';
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }, 100 * (index + 1));
                        });
                    }
                    
                    // Update pagination
                    const paginationContainer = document.querySelector('.pagination-container');
                    if (paginationContainer && data.pagination) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                    
                    // Update count display if exists
                    const countDisplay = document.querySelector('.count-display');
                    if (countDisplay && data.count) {
                        countDisplay.textContent = `Hiển thị ${data.count.visible} / ${data.count.total} sản phẩm`;
                    }
                    
                    // Hide loading indicator
                    loadingIndicator.classList.remove('active');
                    isLoading = false;
                }, 800); // Độ trễ 800ms để hiển thị skeleton loading
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                loadingIndicator.classList.remove('active');
                isLoading = false;
            });
        };
    }
    
    // Thêm hiệu ứng cho active filters
    function enhanceActiveFilters() {
        const activeFiltersContainer = document.querySelector('.active-filters');
        if (activeFiltersContainer) {
            const activeFilterTags = activeFiltersContainer.querySelectorAll('.active-filter-tag');
            activeFilterTags.forEach((tag, index) => {
                // Thêm hiệu ứng xuất hiện với độ trễ
                tag.style.opacity = '0';
                tag.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    tag.style.transition = 'all 0.5s ease';
                    tag.style.opacity = '1';
                    tag.style.transform = 'translateX(0)';
                }, 100 * (index + 1));
                
                // Thêm sự kiện cho nút xóa
                const removeBtn = tag.querySelector('.remove-filter');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        tag.style.transition = 'all 0.3s ease';
                        tag.style.opacity = '0';
                        tag.style.transform = 'translateX(-20px)';
                        
                        setTimeout(() => {
                            tag.remove();
                        }, 300);
                    });
                }
            });
        }
    }
    
    // Ghi đè hàm updateActiveFilters trong filter.js nếu tồn tại
    if (typeof window.originalUpdateActiveFilters === 'undefined' && typeof updateActiveFilters === 'function') {
        // Lưu hàm gốc
        window.originalUpdateActiveFilters = updateActiveFilters;
        
        // Ghi đè hàm updateActiveFilters
        window.updateActiveFilters = function() {
            // Gọi hàm gốc
            window.originalUpdateActiveFilters();
            
            // Thêm hiệu ứng cho active filters
            enhanceActiveFilters();
        };
    }
    
    // Thêm hiệu ứng cho phân trang
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            
            // Thêm hiệu ứng khi nhấn nút phân trang
            paginationLink.classList.add('pagination-link-clicked');
            
            setTimeout(() => {
                paginationLink.classList.remove('pagination-link-clicked');
                
                // Tiếp tục xử lý phân trang như bình thường
                const url = new URL(paginationLink.href);
                currentPage = url.searchParams.get('page') || 1;
                
                // Gọi hàm fetchItems với hiệu ứng skeleton loading
                fetchItems(false);
            }, 300);
        }
    });
    
    // Thêm hiệu ứng cho các thẻ sản phẩm khi tải trang
    function animateProductCards() {
        const productItems = document.querySelectorAll('.product-item');
        productItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100 * (index + 1));
        });
    }
    
    // Thêm các hiệu ứng cho sản phẩm
    addProductBadges();
    addPriceIcons();
    addButtonIconClass();
    
    // Gọi hàm animateProductCards khi trang được tải
    animateProductCards();
    
    // Thêm hiệu ứng cho active filters khi trang được tải
    enhanceActiveFilters();
    
    // Thêm hiệu ứng cho empty state
    const emptyState = document.querySelector('.alert.alert-info.text-center');
    if (emptyState) {
        emptyState.classList.add('empty-state');
        
        // Thay đổi icon
        const icon = emptyState.querySelector('.fa-info-circle');
        if (icon) {
            icon.classList.remove('fa-info-circle');
            icon.classList.add('fa-search', 'empty-icon');
        }
        
        // Thêm nút đặt lại bộ lọc
        if (!emptyState.querySelector('#resetFilters')) {
            const resetButton = document.createElement('button');
            resetButton.id = 'resetFilters';
            resetButton.className = 'btn btn-outline-primary mt-3';
            resetButton.innerHTML = '<i class="fas fa-undo-alt me-2"></i>Đặt lại bộ lọc';
            
            resetButton.addEventListener('click', function() {
                // Kích hoạt nút xóa tất cả bộ lọc
                document.getElementById('clearAllFilters').click();
            });
            
            emptyState.appendChild(resetButton);
        }
    }
});
