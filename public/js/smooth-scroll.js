document.addEventListener('DOMContentLoaded', function() {
    // Xử lý cuộn mượt cho các liên kết có href bắt đầu bằng #
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (!targetElement) return;
            
            // Cuộn đến phần tử đích với hiệu ứng mượt
            window.scrollTo({
                top: targetElement.offsetTop - 100, // Trừ 100px để có khoảng cách từ đầu trang
                behavior: 'smooth'
            });
        });
    });
});
