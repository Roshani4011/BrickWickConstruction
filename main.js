document.addEventListener('DOMContentLoaded', function() {
    // Navigation toggle for mobile
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on a link
    const navItems = document.querySelectorAll('.nav-links li a');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (navToggle.classList.contains('active')) {
                navToggle.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    });
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Contact form popup
    const contactBtns = document.querySelectorAll('#contact-nav-btn, #contact-hero-btn');
    const contactPopup = document.getElementById('contact-popup');
    const closePopup = document.querySelector('.close-popup');
    
    contactBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            contactPopup.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    if (closePopup) {
        closePopup.addEventListener('click', function() {
            contactPopup.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
    
    // Close popup when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === contactPopup) {
            contactPopup.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Project details modal
    const projectBtns = document.querySelectorAll('.project-card button');
    const projectModal = document.getElementById('project-modal');
    const closeModal = document.querySelector('.close-modal');
    const modalContent = document.getElementById('modal-content');
    
    // Sample project data (in a real app, this would come from the database)
    const projects = {
        "1": {
            title: "Modern Apartment Renovation",
            description: "A complete interior redesign of a 2-bedroom apartment in downtown. The project involved removing walls to create an open concept living space, updating all fixtures, and installing custom cabinetry.",
            client: "Jane & John Smith",
            duration: "3 months",
            location: "Downtown Metro",
            services: ["Interior Design", "Renovation", "Custom Furniture"],
            images: ["/api/placeholder/800/500", "/api/placeholder/800/500", "/api/placeholder/800/500"]
        },
        "2": {
            title: "Corporate Office Design",
            description: "A modern workspace design for a tech startup with 50 employees. The design focuses on collaboration spaces, private meeting pods, and adaptable workstations.",
            client: "TechNow Inc.",
            duration: "2 months",
            location: "Tech District",
            services: ["Commercial Design", "Space Planning", "Furniture Selection"],
            images: ["/api/placeholder/800/500", "/api/placeholder/800/500"]
        },
        "3": {
            title: "Luxury Villa Construction",
            description: "Custom-built 5-bedroom villa with swimming pool, outdoor kitchen, and smart home integration. Built using sustainable materials and energy-efficient systems.",
            client: "Rodriguez Family",
            duration: "10 months",
            location: "Hillside Estates",
            services: ["Architecture", "Construction", "Landscape Design", "Smart Home Integration"],
            images: ["/api/placeholder/800/500", "/api/placeholder/800/500", "/api/placeholder/800/500"]
        }
    };
    
    projectBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const projectId = this.closest('.project-card').getAttribute('data-id');
            const project = projects[projectId];
            
            if (project) {
                let imagesHTML = '';
                project.images.forEach(img => {
                    imagesHTML += `<img src="${img}" alt="${project.title}" class="modal-img">`;
                });
                
                let servicesHTML = '';
                project.services.forEach(service => {
                    servicesHTML += `<span class="project-tag">${service}</span>`;
                });
                
                modalContent.innerHTML = `
                    <h2>${project.title}</h2>
                    <div class="project-images">${imagesHTML}</div>
                    <div class="project-details">
                        <p>${project.description}</p>
                        <div class="project-meta">
                            <div class="meta-item">
                                <h4>Client</h4>
                                <p>${project.client}</p>
                            </div>
                            <div class="meta-item">
                                <h4>Duration</h4>
                                <p>${project.duration}</p>
                            </div>
                            <div class="meta-item">
                                <h4>Location</h4>
                                <p>${project.location}</p>
                            </div>
                        </div>
                        <div class="project-services">
                            <h4>Services Provided</h4>
                            <div class="tags">${servicesHTML}</div>
                        </div>
                        <button class="btn btn-primary contact-from-modal">Request Similar Project</button>
                    </div>
                `;
                
                projectModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                
                // Add event listener to the "Request Similar Project" button
                const contactFromModalBtn = document.querySelector('.contact-from-modal');
                if (contactFromModalBtn) {
                    contactFromModalBtn.addEventListener('click', function() {
                        projectModal.style.display = 'none';
                        contactPopup.style.display = 'block';
                    });
                }
            }
        });
    });
    
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            projectModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === projectModal) {
            projectModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Testimonial slider
    const prevBtn = document.getElementById('prev-testimonial');
    const nextBtn = document.getElementById('next-testimonial');
    const testimonials = document.querySelectorAll('.testimonial');
    let currentTestimonial = 0;
    
    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            if (i === index) {
                testimonial.style.display = 'block';
            } else {
                testimonial.style.display = 'none';
            }
        });
    }
    
    // Initialize testimonial display
    if (testimonials.length > 0) {
        showTestimonial(currentTestimonial);
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
                showTestimonial(currentTestimonial);
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                currentTestimonial = (currentTestimonial + 1) % testimonials.length;
                showTestimonial(currentTestimonial);
            });
        }
    }
    
    // Contact form submission
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(contactForm);
            const formDataObject = {};
            formData.forEach((value, key) => {
                formDataObject[key] = value;
            });
            
            // Simulate form submission
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            // In a real application, you would use fetch to send data to the server
            // fetch('api/submit_contact.php', {
            //     method: 'POST',
            //     body: JSON.stringify(formDataObject),
            //     headers: {
            //         'Content-Type': 'application/json'
            //     }
            // })
            
            // For demo purposes, simulate a successful submission after 1.5 seconds
            setTimeout(function() {
                contactForm.innerHTML = `
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <h3>Thank You!</h3>
                        <p>Your message has been sent successfully. We'll get back to you soon.</p>
                    </div>
                `;
                
                // Close the popup after 3 seconds
                setTimeout(function() {
                    contactPopup.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    
                    // Reset the form for future submissions
                    contactForm.innerHTML = document.getElementById('contact-form-template').innerHTML;
                }, 3000);
            }, 1500);
        });
    }
});