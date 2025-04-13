/**
 * Appointment Auth Check
 *
 * Script để kiểm tra đăng nhập khi người dùng nhấn vào nút đặt lịch
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Appointment auth check script loaded');

    // Kiểm tra xem người dùng đã đăng nhập chưa
    const isLoggedIn = document.body.classList.contains('user-logged-in');
    console.log('User logged in:', isLoggedIn);

    // Lấy tất cả các nút đặt lịch
    const appointmentButtons = document.querySelectorAll('.appointment-btn');
    console.log('Found appointment buttons:', appointmentButtons.length);

    // Thêm sự kiện click cho các nút đặt lịch
    appointmentButtons.forEach((button, index) => {
        console.log(`Button ${index} href:`, button.getAttribute('href'));

        button.addEventListener('click', function(e) {
            console.log('Button clicked, logged in:', isLoggedIn);

            // Nếu người dùng chưa đăng nhập
            if (!isLoggedIn) {
                e.preventDefault();
                console.log('Preventing default navigation');

                // Lưu URL hiện tại vào localStorage
                const targetUrl = button.getAttribute('href');
                localStorage.setItem('intended_url', targetUrl);
                console.log('Saved intended URL:', targetUrl);

                // Hiển thị modal thông báo
                showLoginModal();

                return false;
            }
        });
    });

    // Hàm hiển thị modal thông báo
    function showLoginModal() {
        console.log('Showing login modal');

        // Kiểm tra xem modal đã tồn tại chưa
        let loginModal = document.getElementById('loginRequiredModal');

        // Nếu modal chưa tồn tại, tạo mới
        if (!loginModal) {
            console.log('Creating new modal');

            // Tạo modal
            const modalHTML = `
                <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="loginRequiredModalLabel">Yêu cầu đăng nhập</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-lock fa-3x text-primary mb-3"></i>
                                    <h4>Vui lòng đăng nhập</h4>
                                    <p>Bạn cần đăng nhập hoặc đăng ký để sử dụng tính năng đặt lịch.</p>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <a href="/login" class="btn btn-primary">Đăng nhập</a>
                                <a href="/register" class="btn btn-outline-primary">Đăng ký</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Thêm modal vào body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            loginModal = document.getElementById('loginRequiredModal');
        }

        try {
            // Hiển thị modal sử dụng Bootstrap
            if (typeof bootstrap !== 'undefined') {
                console.log('Using Bootstrap modal');
                const bsModal = new bootstrap.Modal(loginModal);
                bsModal.show();
            } else {
                // Fallback nếu không có Bootstrap
                console.log('Bootstrap not found, using fallback');
                loginModal.style.display = 'block';
                loginModal.classList.add('show');
                document.body.classList.add('modal-open');

                // Tạo backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            console.error('Error showing modal:', error);
        }
    }
});
