// Enhanced JavaScript with GSAP animations
document.addEventListener('DOMContentLoaded', function() {
    // Initialize GSAP
    gsap.registerPlugin(ScrollTrigger);

    // Initialize based on current page
    if (window.location.search.includes('page=home') || !window.location.search) {
        initializeHomeAnimations();
    }

    // Initialize admin/siswa pages
    if (window.location.search.includes('page=admin') || window.location.search.includes('page=siswa') || window.location.search.includes('page=dashboard')) {
        initializeDashboardAnimations();
    }

    // Global smooth scroll for anchor links
    initializeSmoothScroll();
    
    // Initialize modals
    initializeModals();
    
    // Initialize particles
    createParticles();
});

function initializeHomeAnimations() {
    // Hero section animations dengan efek yang lebih menarik
    const heroTl = gsap.timeline();
    heroTl.fromTo('.hero h1', 
        { y: 100, opacity: 0, rotationX: 10 },
        { y: 0, opacity: 1, rotationX: 0, duration: 1.2, ease: "power3.out" }
    )
    .fromTo('.hero p', 
        { y: 50, opacity: 0, scale: 0.9 },
        { y: 0, opacity: 1, scale: 1, duration: 1, ease: "power2.out" },
        "-=0.8"
    )
    .fromTo('.hero .btn-primary', 
        { y: 30, opacity: 0, scale: 0.8, rotationY: 20 },
        { y: 0, opacity: 1, scale: 1, rotationY: 0, duration: 0.8, stagger: 0.2, ease: "back.out(1.7)" },
        "-=0.5"
    );

    // Counter animation with GSAP
    gsap.utils.toArray('.counter').forEach(counter => {
        const target = +counter.getAttribute('data-count');
        gsap.to(counter, {
            textContent: target,
            duration: 2.5,
            ease: "power2.out",
            scrollTrigger: {
                trigger: counter,
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none none"
            },
            onUpdate: function() {
                counter.textContent = Math.floor(this.targets()[0].textContent);
            }
        });
    });

    // Slide in animations for sections dengan efek yang lebih halus
    gsap.utils.toArray('.slide-in').forEach(element => {
        gsap.fromTo(element, {
            y: 80,
            opacity: 0,
            rotationX: 5,
            scale: 0.95
        }, {
            y: 0,
            opacity: 1,
            rotationX: 0,
            scale: 1,
            duration: 1,
            ease: "power3.out",
            scrollTrigger: {
                trigger: element,
                start: "top 85%",
                end: "bottom 20%",
                toggleActions: "play none none none"
            }
        });
    });

    // Card hover animations
    gsap.utils.toArray('.card-hover').forEach(card => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -10,
                rotationY: 5,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                rotationY: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    });

    // Timeline animations dengan stagger effect
    gsap.utils.toArray('.timeline-item').forEach((item, index) => {
        gsap.fromTo(item, {
            y: 100,
            opacity: 0,
            rotationY: 10,
            scale: 0.9
        }, {
            y: 0,
            opacity: 1,
            rotationY: 0,
            scale: 1,
            duration: 0.8,
            delay: index * 0.2,
            ease: "power3.out",
            scrollTrigger: {
                trigger: item,
                start: "top 90%",
                end: "bottom 20%",
                toggleActions: "play none none none"
            },
            onComplete: () => {
                item.classList.add('animated');
            }
        });
    });

    // Gallery item animations
    gsap.utils.toArray('.gallery-item').forEach((item, index) => {
        gsap.fromTo(item, {
            y: 50,
            opacity: 0,
            scale: 0.8
        }, {
            y: 0,
            opacity: 1,
            scale: 1,
            duration: 0.6,
            delay: index * 0.1,
            ease: "back.out(1.7)",
            scrollTrigger: {
                trigger: item,
                start: "top 85%",
                end: "bottom 20%",
                toggleActions: "play none none none"
            }
        });
    });

    // Nav link animations
    gsap.utils.toArray('.nav-link').forEach(link => {
        link.addEventListener('mouseenter', () => {
            gsap.to(link, {
                y: -2,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        link.addEventListener('mouseleave', () => {
            gsap.to(link, {
                y: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    });
}

function initializeDashboardAnimations() {
    // Dashboard specific animations
    gsap.fromTo('.admin-header, .bg-white', 
        { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.8, stagger: 0.1, ease: "power3.out" }
    );

    // Stats cards animation
    gsap.utils.toArray('.stats-card').forEach((card, index) => {
        gsap.fromTo(card, 
            { scale: 0.8, opacity: 0, rotationY: 10 },
            { scale: 1, opacity: 1, rotationY: 0, duration: 0.6, delay: index * 0.1, ease: "back.out(1.7)" }
        );
    });
}

function initializeSmoothScroll() {
    // Enhanced smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            if (href.includes('#')) {
                e.preventDefault();
                const targetId = href.split('#')[1];
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const headerHeight = document.querySelector('header') ? document.querySelector('header').offsetHeight : 0;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
                    
                    // GSAP smooth scroll dengan easing yang lebih halus
                    gsap.to(window, {
                        duration: 1.2,
                        scrollTo: { y: targetPosition, autoKill: false },
                        ease: "power2.inOut"
                    });
                }
            }
        });
    });
}

function initializeModals() {
    // Modal functionality
    const modal = document.getElementById('modalJurusan');
    const closeModalBtn = document.querySelector('.close-modal');
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Keyboard support for modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

function createParticles() {
    const particlesContainer = document.querySelector('.particles');
    if (!particlesContainer) return;
    
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        const size = Math.random() * 6 + 2;
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        const delay = Math.random() * 6;
        const duration = Math.random() * 8 + 4;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}%`;
        particle.style.top = `${posY}%`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.animationDuration = `${duration}s`;
        
        particlesContainer.appendChild(particle);
    }
}

function showJurusanDetail(jurusanId) {
    // Show loading state dengan animasi
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <div class="loading mx-auto mb-4"></div>
            <p class="text-gray-600">Memuat data jurusan...</p>
        </div>
    `;
    
    const modal = document.getElementById('modalJurusan');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Animate modal opening
    gsap.fromTo('.modal-content',
        { scale: 0.8, opacity: 0, rotationY: 10 },
        { scale: 1, opacity: 1, rotationY: 0, duration: 0.5, ease: "back.out(1.7)" }
    );

    // Fetch jurusan data
    fetch(`api/get_jurusan.php?id=${jurusanId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(jurusan => {
            if (jurusan.error) {
                throw new Error(jurusan.error);
            }
            
            modalContent.innerHTML = `
                <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center bg-gradient-to-r from-blue-600 to-orange-500 bg-clip-text text-transparent">${jurusan.nama_jurusan}</h2>
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="lg:w-2/5">
                        <div class="rounded-2xl overflow-hidden shadow-2xl transform transition-all duration-500 hover:scale-105">
                            ${jurusan.gambar ? 
                                `<img src="uploads/${jurusan.gambar}" alt="${jurusan.nama_jurusan}" class="w-full h-80 object-cover">` :
                                `<img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                                     alt="${jurusan.nama_jurusan}" class="w-full h-80 object-cover">`
                            }
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border-l-4 border-blue-500 transform transition-all duration-300 hover:scale-105">
                                <p class="text-sm text-gray-600 font-semibold">Kuota Tersedia</p>
                                <p class="text-2xl font-bold text-gray-800">${jurusan.kuota} <span class="text-sm text-gray-500">Siswa</span></p>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border-l-4 border-orange-500 transform transition-all duration-300 hover:scale-105">
                                <p class="text-sm text-gray-600 font-semibold">Rating Program</p>
                                <p class="text-2xl font-bold text-gray-800">4.8<span class="text-sm text-gray-500">/5</span></p>
                                <div class="flex space-x-1 mt-1">
                                    ${Array(5).fill().map((_, i) => 
                                        `<i class="fas fa-star text-yellow-500 text-sm ${i < 4 ? 'text-yellow-500' : 'text-yellow-300'}"></i>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-3/5">
                        <div class="bg-gray-50 rounded-2xl p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
                            <h3 class="text-xl font-bold mb-3 text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Deskripsi Program
                            </h3>
                            <p class="text-gray-600 leading-relaxed text-lg">${jurusan.deskripsi}</p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-blue-50 to-orange-50 rounded-2xl p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
                            <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                                <i class="fas fa-star text-orange-500 mr-2"></i>
                                Keunggulan Program
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    Pengajar profesional berpengalaman industri
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    Kurikulum sesuai kebutuhan industri terkini
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    Fasilitas praktik lengkap dan modern
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    Jaringan perusahaan mitra yang luas
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    Sertifikasi kompetensi nasional
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-blue-50 rounded-2xl p-6 transform transition-all duration-300 hover:shadow-lg">
                            <h3 class="text-xl font-bold mb-3 text-gray-800 flex items-center">
                                <i class="fas fa-briefcase text-blue-500 mr-2"></i>
                                Prospek Karir Lulusan
                            </h3>
                            <p class="text-gray-600 leading-relaxed">
                                Lulusan program ini memiliki peluang karir yang sangat luas di berbagai sektor industri, 
                                dengan posisi-posisi strategis dan peluang pengembangan karir yang menjanjikan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 text-center">
                    <button onclick="closeModal()" class="bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg shadow-md">
                        <i class="fas fa-times mr-2"></i>Tutup Detail
                    </button>
                </div>
            `;

            // Animate modal content dengan stagger effect
            gsap.fromTo('.modal-content > *', 
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: "power3.out" }
            );
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-gray-600 mb-4">${error.message}</p>
                    <button onclick="closeModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                        Tutup
                    </button>
                </div>
            `;
        });
}

function closeModal() {
    const modal = document.getElementById('modalJurusan');
    gsap.to('.modal-content', {
        scale: 0.8,
        y: 50,
        opacity: 0,
        rotationY: -10,
        duration: 0.3,
        ease: "power2.in",
        onComplete: () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            // Reset modal content transform
            gsap.set('.modal-content', { clearProps: "all" });
        }
    });
}

// Utility function for showing alerts
function showAlert(icon, title, message, timer = 3000) {
    return Swal.fire({
        icon: icon,
        title: title,
        text: message,
        timer: timer,
        showConfirmButton: false,
        background: '#ffffff',
        color: '#374151',
        customClass: {
            popup: 'rounded-2xl shadow-2xl'
        }
    });
}

// Utility function for confirmation dialogs
function showConfirm(title, text, confirmButtonText = 'Ya', cancelButtonText = 'Batal') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0066CC',
        cancelButtonColor: '#6B7280',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        background: '#ffffff',
        color: '#374151',
        customClass: {
            popup: 'rounded-2xl shadow-2xl'
        }
    });
}

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    if (hero) {
        const rate = scrolled * -0.5;
        hero.style.transform = `translateY(${rate}px)`;
    }
});

// Add intersection observer for additional animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            gsap.to(entry.target, {
                y: 0,
                opacity: 1,
                rotationX: 0,
                scale: 1,
                duration: 0.8,
                ease: "power3.out"
            });
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all elements with data-animate attribute
document.querySelectorAll('[data-animate]').forEach(el => {
    observer.observe(el);
});