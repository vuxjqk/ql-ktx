<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastContainer = document.getElementById('toast-container');

            window.showToast = (message, type = 'info', duration = 3000) => {
                const toast = document.createElement('div');
                toast.className =
                    'text-white w-64 flex items-center px-6 py-3 gap-2 shadow-sm rounded-lg -translate-x-full transform transition-transform opacity-0 transition-opacity duration-300 ease-out';

                const toastStyles = {
                    success: {
                        bg: 'bg-green-500',
                        icon: 'fas fa-check-circle'
                    },
                    error: {
                        bg: 'bg-red-500',
                        icon: 'fas fa-exclamation-circle'
                    },
                    info: {
                        bg: 'bg-blue-500',
                        icon: 'fas fa-info-circle'
                    },
                    warning: {
                        bg: 'bg-yellow-500',
                        icon: 'fas fa-exclamation-triangle'
                    }
                };

                const {
                    bg,
                    icon
                } = toastStyles[type] || toastStyles.info;

                toast.classList.add(bg);

                toast.innerHTML = `
                    <i class="${icon}"></i>
                    <span class="flex-1">${message}</span>
                    <button type="button" class="close-toast ms-2 text-gray-200 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="progress-bar w-full h-1 bg-white/50 absolute bottom-0 left-0" style="transition: width ${duration}ms linear"></div>
                `;

                toastContainer.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                });

                const progressBar = toast.querySelector('.progress-bar');
                let timeout;
                let startTime;

                const startTimer = () => {
                    startTime = Date.now();
                    timeout = setTimeout(() => {
                        toast.classList.remove('translate-x-0', 'opacity-100');
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, duration);

                    requestAnimationFrame(() => {
                        progressBar.style.transition = `width ${duration}ms linear`;
                        progressBar.style.width = '0%';
                    });
                }

                const pauseTimer = () => {
                    clearTimeout(timeout);
                    const elapsed = Date.now() - startTime;
                    duration -= elapsed;

                    const computedStyle = getComputedStyle(progressBar);
                    const currentWidth = computedStyle.width;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = currentWidth;
                }

                startTimer();

                toast.addEventListener('mouseenter', () => {
                    pauseTimer();
                });

                toast.addEventListener('mouseleave', () => {
                    startTimer();
                });

                toast.querySelector('.close-toast').addEventListener('click', () => {
                    clearTimeout(timeout);
                    toast.classList.remove('translate-x-0', 'opacity-100');
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                });
            }
        });
    </script>
@endPushOnce
