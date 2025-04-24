/**
 * Login Modal Script
 *
 * Script để xử lý hiển thị modal đăng nhập/đăng ký khi người dùng nhấn vào nút đăng nhập
 * Xử lý AJAX cho đăng nhập, đăng ký và xác thực OTP
 */
document.addEventListener('DOMContentLoaded', function() {
    // Lấy các phần tử cần thiết
    const headerLoginBtn = document.getElementById('loginButton');
    const authModal = document.getElementById('authModal');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const otpForm = document.getElementById('otpVerificationForm');
    const showRegisterFormLink = document.getElementById('showRegisterForm');
    const showLoginFormLink = document.getElementById('showLoginForm');
    const modalTitle = document.querySelector('#authModal .modal-title');
    const authAlert = document.getElementById('authAlert');
    const authAlertMessage = document.getElementById('authAlertMessage');

    // Form elements
    const ajaxLoginForm = document.getElementById('ajaxLoginForm');
    const ajaxRegisterForm = document.getElementById('ajaxRegisterForm');
    const ajaxOtpForm = document.getElementById('ajaxOtpForm');
    const resendOtpLink = document.getElementById('resendOtp');
    const otpCountdown = document.getElementById('otpCountdown');
    const otpEmailInput = document.getElementById('otp_email');

    // Spinners
    const loginSpinner = document.getElementById('loginSpinner');
    const registerSpinner = document.getElementById('registerSpinner');
    const verifySpinner = document.getElementById('verifySpinner');

    // Kiểm tra xem các phần tử có tồn tại không
    if (!headerLoginBtn || !authModal) return;

    // Khởi tạo modal Bootstrap
    let bsModal = null;
    if (typeof bootstrap !== 'undefined') {
        bsModal = new bootstrap.Modal(authModal);

        // Xử lý sự kiện khi modal bị đóng
        authModal.addEventListener('hidden.bs.modal', function () {
            // Dừng đồng hồ đếm ngược OTP khi modal bị đóng
            stopOtpExpiryTimer();
        });
    }

    // Hiển thị thông báo lỗi
    function showError(message) {
        if (authAlert && authAlertMessage) {
            authAlertMessage.textContent = message;
            authAlert.classList.remove('d-none', 'alert-success');
            authAlert.classList.add('alert-danger', 'show');
        }
    }

    // Hiển thị thông báo thành công
    function showSuccess(message) {
        if (authAlert && authAlertMessage) {
            authAlertMessage.textContent = message;
            authAlert.classList.remove('d-none', 'alert-danger');
            authAlert.classList.add('alert-success', 'show');
        }
    }

    // Ẩn thông báo
    function hideAlert() {
        if (authAlert) {
            authAlert.classList.add('d-none');
        }
    }

    // Hiển thị form đăng nhập
    function showLoginFormUI() {
        if (loginForm && registerForm && otpForm) {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            otpForm.style.display = 'none';
            if (modalTitle) modalTitle.textContent = 'Đăng nhập';

            // Dừng đồng hồ đếm ngược OTP nếu đang chạy
            stopOtpExpiryTimer();
        }
        hideAlert();
    }

    // Hiển thị form đăng ký
    function showRegisterFormUI() {
        if (loginForm && registerForm && otpForm) {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            otpForm.style.display = 'none';
            if (modalTitle) modalTitle.textContent = 'Đăng ký';

            // Dừng đồng hồ đếm ngược OTP nếu đang chạy
            stopOtpExpiryTimer();
        }
        hideAlert();
    }

    // Hàm dừng đồng hồ đếm ngược OTP
    function stopOtpExpiryTimer() {
        if (otpExpiryInterval) {
            clearInterval(otpExpiryInterval);
            otpExpiryInterval = null;
        }
    }

    // Hiển thị form OTP
    function showOtpFormUI(email) {
        if (loginForm && registerForm && otpForm) {
            loginForm.style.display = 'none';
            registerForm.style.display = 'none';
            otpForm.style.display = 'block';
            if (modalTitle) modalTitle.textContent = 'Xác thực OTP';
            if (otpEmailInput) otpEmailInput.value = email;

            // Hiển thị form OTP và ẩn form hết hạn
            const otpExpiredForm = document.getElementById('otpExpiredForm');
            const ajaxOtpForm = document.getElementById('ajaxOtpForm');
            const resendOtpContainer = document.getElementById('resendOtpContainer');

            if (otpExpiredForm && ajaxOtpForm && resendOtpContainer) {
                otpExpiredForm.classList.add('d-none');
                ajaxOtpForm.classList.remove('d-none');
                resendOtpContainer.classList.remove('d-none');
            }

            // Bắt đầu đếm ngược thời gian hết hạn OTP (5 phút)
            startOtpExpiryTimer();
        }
    }

    // Biến lưu trữ interval của đồng hồ đếm ngược
    let otpExpiryInterval = null;

    // Hàm bắt đầu đếm ngược thời gian hết hạn OTP (5 phút)
    function startOtpExpiryTimer() {
        // Xóa interval cũ nếu có
        if (otpExpiryInterval) {
            clearInterval(otpExpiryInterval);
        }

        const otpExpiryTimer = document.getElementById('otpExpiryTimer');
        if (!otpExpiryTimer) return;

        // Đặt thời gian ban đầu là 5 phút (300 giây)
        let timeLeft = 300;
        updateTimerDisplay(timeLeft, otpExpiryTimer);

        // Cập nhật đồng hồ mỗi giây
        otpExpiryInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay(timeLeft, otpExpiryTimer);

            // Thêm class cảnh báo khi còn ít thời gian
            if (timeLeft <= 60) {
                otpExpiryTimer.classList.add('warning');
            }

            // Thêm class nguy hiểm khi còn rất ít thời gian
            if (timeLeft <= 30) {
                otpExpiryTimer.classList.remove('warning');
                otpExpiryTimer.classList.add('danger');
            }

            // Khi hết thời gian
            if (timeLeft <= 0) {
                clearInterval(otpExpiryInterval);
                handleOtpExpired();
            }
        }, 1000);
    }

    // Hàm cập nhật hiển thị đồng hồ
    function updateTimerDisplay(seconds, timerElement) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    // Hàm xử lý khi OTP hết hạn
    function handleOtpExpired() {
        const otpExpiredForm = document.getElementById('otpExpiredForm');
        const ajaxOtpForm = document.getElementById('ajaxOtpForm');
        const resendOtpContainer = document.getElementById('resendOtpContainer');
        const otpInput = document.getElementById('otp');

        if (otpExpiredForm && ajaxOtpForm && resendOtpContainer) {
            // Hiển thị form hết hạn và ẩn form OTP
            otpExpiredForm.classList.remove('d-none');
            ajaxOtpForm.classList.add('d-none');
            resendOtpContainer.classList.add('d-none');

            // Reset trạng thái input OTP
            if (otpInput) {
                otpInput.value = '';
                otpInput.classList.remove('is-valid', 'is-invalid');
            }
        }
    }

    // Xử lý sự kiện click vào nút đăng nhập trên header
    headerLoginBtn.addEventListener('click', function() {
        showLoginFormUI();

        // Hiển thị modal
        if (bsModal) {
            bsModal.show();
        } else {
            // Fallback nếu không có Bootstrap
            authModal.style.display = 'block';
            authModal.classList.add('show');
            document.body.classList.add('modal-open');

            // Tạo backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    });

    // Xử lý chuyển đổi giữa form đăng nhập và đăng ký
    if (showRegisterFormLink) {
        showRegisterFormLink.addEventListener('click', function() {
            showRegisterFormUI();
        });
    }

    if (showLoginFormLink) {
        showLoginFormLink.addEventListener('click', function() {
            showLoginFormUI();
        });
    }

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Xử lý đăng nhập bằng AJAX
    if (ajaxLoginForm) {
        const loginEmailInput = document.getElementById('login_email');
        const loginPasswordInput = document.getElementById('login_password');
        const emailFeedback = document.getElementById('emailFeedback');
        const passwordFeedback = document.getElementById('passwordFeedback');

        // Xử lý sự kiện input để reset trạng thái validation
        if (loginEmailInput) {
            loginEmailInput.addEventListener('input', function() {
                // Reset trạng thái validation khi người dùng nhập
                loginEmailInput.classList.remove('is-invalid', 'is-valid');
                hideAlert();
            });
        }

        if (loginPasswordInput) {
            loginPasswordInput.addEventListener('input', function() {
                // Reset trạng thái validation khi người dùng nhập
                loginPasswordInput.classList.remove('is-invalid', 'is-valid');
                hideAlert();
            });
        }

        ajaxLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Kiểm tra email
            const emailValue = loginEmailInput.value.trim();
            if (!emailValue || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                loginEmailInput.classList.add('is-invalid');
                loginEmailInput.classList.remove('is-valid');
                if (emailFeedback) {
                    emailFeedback.textContent = 'Vui lòng nhập email hợp lệ.';
                }
                return;
            }

            // Kiểm tra mật khẩu
            const passwordValue = loginPasswordInput.value.trim();
            if (!passwordValue) {
                loginPasswordInput.classList.add('is-invalid');
                loginPasswordInput.classList.remove('is-valid');
                if (passwordFeedback) {
                    passwordFeedback.textContent = 'Vui lòng nhập mật khẩu.';
                }
                return;
            }

            // Hiển thị spinner
            if (loginSpinner) loginSpinner.classList.remove('d-none');

            // Vô hiệu hóa nút đăng nhập để tránh submit nhiều lần
            const loginBtn = document.getElementById('loginButton');
            if (loginBtn) loginBtn.disabled = true;

            // Lấy dữ liệu form
            const formData = new FormData(this);

            // Gửi request AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ẩn spinner và kích hoạt lại nút
                if (loginSpinner) loginSpinner.classList.add('d-none');
                if (loginBtn) loginBtn.disabled = false;

                if (data.success) {
                    // Đăng nhập thành công
                    loginEmailInput.classList.add('is-valid');
                    loginEmailInput.classList.remove('is-invalid');
                    loginPasswordInput.classList.add('is-valid');
                    loginPasswordInput.classList.remove('is-invalid');

                    showSuccess('Đăng nhập thành công! Đang chuyển hướng...');

                    // Kiểm tra URL dự định truy cập hoặc URL chuyển hướng từ server
                    const intendedUrl = localStorage.getItem('intended_url');
                    const redirectUrl = data.redirect_url || null;

                    // Chuyển hướng sau 1 giây
                    setTimeout(() => {
                        if (intendedUrl) {
                            // Nếu có URL dự định truy cập (từ nút đặt lịch), ưu tiên sử dụng
                            localStorage.removeItem('intended_url');
                            window.location.href = intendedUrl;
                        } else if (redirectUrl) {
                            // Nếu có URL chuyển hướng từ server (dựa trên vai trò), sử dụng nó
                            window.location.href = redirectUrl;
                        } else {
                            // Nếu không có URL nào, tải lại trang
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    // Đăng nhập thất bại
                    loginEmailInput.classList.add('is-invalid');
                    loginEmailInput.classList.remove('is-valid');
                    loginPasswordInput.classList.add('is-invalid');
                    loginPasswordInput.classList.remove('is-valid');

                    if (emailFeedback) {
                        emailFeedback.textContent = 'Thông tin đăng nhập không chính xác.';
                    }
                    if (passwordFeedback) {
                        passwordFeedback.textContent = 'Thông tin đăng nhập không chính xác.';
                    }

                    showError(data.message || 'Thông tin đăng nhập không chính xác.');
                }
            })
            .catch(error => {
                // Ẩn spinner và kích hoạt lại nút
                if (loginSpinner) loginSpinner.classList.add('d-none');
                if (loginBtn) loginBtn.disabled = false;

                // Hiển thị lỗi
                loginEmailInput.classList.add('is-invalid');
                loginEmailInput.classList.remove('is-valid');
                loginPasswordInput.classList.add('is-invalid');
                loginPasswordInput.classList.remove('is-valid');

                showError('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                console.error('Login error:', error);
            });
        });
    }

    // Xử lý đăng ký bằng AJAX
    if (ajaxRegisterForm) {
        ajaxRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }

            // Kiểm tra mật khẩu và xác nhận mật khẩu
            const password = document.getElementById('register_password').value;
            const passwordConfirm = document.getElementById('password-confirm').value;

            if (password !== passwordConfirm) {
                showError('Mật khẩu xác nhận không khớp.');
                return;
            }

            // Hiển thị spinner
            if (registerSpinner) registerSpinner.classList.remove('d-none');

            // Lấy dữ liệu form
            const formData = new FormData(this);
            const email = document.getElementById('register_email').value;

            // Gửi request AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ẩn spinner
                if (registerSpinner) registerSpinner.classList.add('d-none');

                if (data.success) {
                    // Đăng ký thành công, chuyển sang form OTP
                    showSuccess('Mã OTP đã được gửi đến email của bạn.');
                    showOtpFormUI(email);
                    startOtpCountdown();
                } else {
                    // Đăng ký thất bại
                    showError(data.message || 'Đăng ký thất bại. Vui lòng kiểm tra lại thông tin.');
                }
            })
            .catch(error => {
                // Ẩn spinner
                if (registerSpinner) registerSpinner.classList.add('d-none');
                showError('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                console.error('Register error:', error);
            });
        });
    }

    // Xử lý xác thực OTP bằng AJAX
    if (ajaxOtpForm) {
        const otpInput = document.getElementById('otp');
        const otpFeedback = document.getElementById('otpFeedback');

        // Xử lý sự kiện input để reset trạng thái validation
        if (otpInput) {
            otpInput.addEventListener('input', function() {
                // Reset trạng thái validation khi người dùng nhập
                otpInput.classList.remove('is-invalid', 'is-valid');
                if (otpFeedback) {
                    otpFeedback.textContent = 'Vui lòng nhập mã OTP hợp lệ.';
                }
                hideAlert();
            });
        }

        ajaxOtpForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Kiểm tra OTP có đủ 6 ký tự không
            const otpValue = otpInput.value.trim();
            if (otpValue.length !== 6 || !/^\d+$/.test(otpValue)) {
                otpInput.classList.add('is-invalid');
                if (otpFeedback) {
                    otpFeedback.textContent = 'Mã OTP phải có 6 chữ số.';
                }
                return;
            }

            // Hiển thị spinner
            if (verifySpinner) verifySpinner.classList.remove('d-none');

            // Vô hiệu hóa nút xác thực để tránh submit nhiều lần
            const verifyButton = document.getElementById('verifyButton');
            if (verifyButton) verifyButton.disabled = true;

            // Lấy dữ liệu form
            const formData = new FormData(this);

            // Gửi request AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ẩn spinner và kích hoạt lại nút
                if (verifySpinner) verifySpinner.classList.add('d-none');
                if (verifyButton) verifyButton.disabled = false;

                if (data.success) {
                    // Xác thực thành công
                    otpInput.classList.add('is-valid');
                    otpInput.classList.remove('is-invalid');
                    showSuccess('Xác thực thành công! Đang chuyển hướng...');

                    // Lấy URL chuyển hướng từ server
                    const redirectUrl = data.redirect_url || null;

                    // Chuyển hướng sau 1 giây
                    setTimeout(() => {
                        if (redirectUrl) {
                            window.location.href = redirectUrl;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    // Xác thực thất bại
                    otpInput.classList.add('is-invalid');
                    otpInput.classList.remove('is-valid');
                    if (otpFeedback) {
                        otpFeedback.textContent = data.message || 'Mã OTP không hợp lệ. Vui lòng thử lại.';
                    }
                    showError(data.message || 'Mã OTP không hợp lệ. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                // Ẩn spinner và kích hoạt lại nút
                if (verifySpinner) verifySpinner.classList.add('d-none');
                if (verifyButton) verifyButton.disabled = false;

                // Hiển thị lỗi
                otpInput.classList.add('is-invalid');
                otpInput.classList.remove('is-valid');
                showError('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                console.error('OTP verification error:', error);
            });
        });
    }

    // Xử lý gửi lại mã OTP
    if (resendOtpLink) {
        resendOtpLink.addEventListener('click', function(e) {
            e.preventDefault();

            // Kiểm tra xem đang trong thời gian chờ không
            if (this.classList.contains('disabled')) {
                return;
            }

            resendOtpAction();
        });
    }

    // Xử lý nút "Gửi lại mã" trong form hết hạn
    const resendExpiredOtpBtn = document.getElementById('resendExpiredOtp');
    if (resendExpiredOtpBtn) {
        resendExpiredOtpBtn.addEventListener('click', function() {
            resendOtpAction();
        });
    }

    // Xử lý nút "Quay lại đăng ký" trong form hết hạn
    const backToRegisterBtn = document.getElementById('backToRegister');
    if (backToRegisterBtn) {
        backToRegisterBtn.addEventListener('click', function() {
            showRegisterFormUI();
        });
    }

    // Hàm gửi lại mã OTP
    function resendOtpAction() {
        const email = otpEmailInput.value;

        if (!email) {
            showError('Email không hợp lệ.');
            return;
        }

        // Gửi request AJAX để gửi lại OTP
        fetch(route('verification.resend'), {
            method: 'POST',
            body: JSON.stringify({ email: email }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Mã OTP mới đã được gửi đến email của bạn.');

                // Hiển thị lại form OTP
                const otpExpiredForm = document.getElementById('otpExpiredForm');
                const ajaxOtpForm = document.getElementById('ajaxOtpForm');
                const resendOtpContainer = document.getElementById('resendOtpContainer');

                if (otpExpiredForm && ajaxOtpForm && resendOtpContainer) {
                    otpExpiredForm.classList.add('d-none');
                    ajaxOtpForm.classList.remove('d-none');
                    resendOtpContainer.classList.remove('d-none');
                }

                // Bắt đầu đếm ngược thời gian gửi lại OTP
                startOtpCountdown();

                // Bắt đầu đếm ngược thời gian hết hạn OTP
                startOtpExpiryTimer();

                // Reset trạng thái input OTP
                const otpInput = document.getElementById('otp');
                if (otpInput) {
                    otpInput.value = '';
                    otpInput.classList.remove('is-valid', 'is-invalid');
                    otpInput.focus();
                }
            } else {
                showError(data.message || 'Không thể gửi lại mã OTP. Vui lòng thử lại sau.');
            }
        })
        .catch(error => {
            showError('Đã xảy ra lỗi. Vui lòng thử lại sau.');
            console.error('Resend OTP error:', error);
        });
    }

    // Xử lý đếm ngược thời gian gửi lại OTP
    function startOtpCountdown() {
        if (!resendOtpLink || !otpCountdown) return;

        let seconds = 60;
        resendOtpLink.classList.add('disabled');
        otpCountdown.classList.remove('d-none');
        otpCountdown.textContent = `(${seconds}s)`;

        const interval = setInterval(() => {
            seconds--;
            otpCountdown.textContent = `(${seconds}s)`;

            if (seconds <= 0) {
                clearInterval(interval);
                resendOtpLink.classList.remove('disabled');
                otpCountdown.classList.add('d-none');
            }
        }, 1000);
    }

    // Xử lý lưu URL dự định truy cập khi đăng nhập từ nút đặt lịch
    const appointmentButtons = document.querySelectorAll('.appointment-btn');
    appointmentButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Kiểm tra xem người dùng đã đăng nhập chưa
            const isLoggedIn = document.body.classList.contains('user-logged-in');

            if (!isLoggedIn) {
                e.preventDefault();

                // Lưu URL hiện tại vào localStorage
                const targetUrl = button.getAttribute('href');
                localStorage.setItem('intended_url', targetUrl);

                // Hiển thị modal đăng nhập
                showLoginFormUI();

                if (bsModal) {
                    bsModal.show();
                }

                return false;
            }
        });
    });

    // Hàm helper để tạo URL từ route name và params
    function route(name, params = {}) {
        // Đây là một cách đơn giản để xử lý route, trong thực tế bạn có thể cần một giải pháp phức tạp hơn
        if (name === 'verification.resend') {
            return '/register/verify/resend';
        }
        return '/';
    }
});
