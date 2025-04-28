/**
 * Service Animations JavaScript
 * Thêm các hiệu ứng và animation cho trang dịch vụ
 */

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo biến
    let isFilterSidebarVisible = true;
    const filterSidebar = document.querySelector('.filter-sidebar');
    const servicesContainer = document.getElementById('services-container');
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const closeFilterSidebarBtn = document.getElementById('closeFilterSidebar');
    
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
        if (servicesContainer) {
            servicesContainer.innerHTML = '';
            
            // Tạo 3 skeleton cards
            for (let i = 0; i < 3; i++) {
                const skeletonCard = document.createElement('div');
                skeletonCard.className = 'skeleton-card';
                skeletonCard.innerHTML = `
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-price"></div>
                        <div class="skeleton-button"></div>
                    </div>
                `;
                servicesContainer.appendChild(skeletonCard);
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
                    if (servicesContainer) {
                        servicesContainer.innerHTML = data.html;
                        
                        // Thêm hiệu ứng xuất hiện cho các mục dịch vụ
                        const serviceItems = servicesContainer.querySelectorAll('.service-list-item');
                        serviceItems.forEach((item, index) => {
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
                        countDisplay.textContent = `Hiển thị ${data.count.visible} / ${data.count.total} dịch vụ`;
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
    
    // Thêm hiệu ứng cho các thẻ dịch vụ khi tải trang
    function animateServiceCards() {
        if (servicesContainer) {
            const serviceItems = servicesContainer.querySelectorAll('.service-list-item');
            serviceItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        }
    }
    
    // Gọi hàm animateServiceCards khi trang được tải
    animateServiceCards();
    
    // Thêm hiệu ứng cho active filters khi trang được tải
    enhanceActiveFilters();
});
