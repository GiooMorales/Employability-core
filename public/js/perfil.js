document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    const tabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const contentId = tab.getAttribute('data-tab');
            document.getElementById(contentId).classList.add('active');
        });
    });

    // Profile Picture Preview
    const profilePictureInput = document.getElementById('profile-picture');
    const profilePicturePreview = document.querySelector('.profile-avatar-large');

    if (profilePictureInput && profilePicturePreview) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicturePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Cover Image Preview
    const coverImageInput = document.getElementById('cover-image');
    const coverImagePreview = document.querySelector('.profile-cover');

    if (coverImageInput && coverImagePreview) {
        coverImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverImagePreview.style.backgroundImage = `url(${e.target.result})`;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Form Validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Delete Account Confirmation
    const deleteAccountForm = document.getElementById('delete-account-form');
    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
                this.submit();
            }
        });
    }

    // Tooltips
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            tooltip.style.left = rect.left + (rect.width - tooltip.offsetWidth) / 2 + 'px';
            
            document.body.appendChild(tooltip);
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

    // Skills Input
    const skillsInput = document.getElementById('skills-input');
    const skillsContainer = document.querySelector('.skills-container');
    
    if (skillsInput && skillsContainer) {
        skillsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const skill = this.value.trim();
                if (skill) {
                    addSkill(skill);
                    this.value = '';
                }
            }
        });
    }

    function addSkill(skill) {
        const skillElement = document.createElement('div');
        skillElement.className = 'skill-tag';
        skillElement.innerHTML = `
            ${skill}
            <span class="skill-level">Intermediário</span>
            <button class="remove-skill" onclick="this.parentElement.remove()">×</button>
        `;
        skillsContainer.appendChild(skillElement);
    }

    // Experience Form
    const addExperienceBtn = document.getElementById('add-experience');
    const experienceForm = document.getElementById('experience-form');
    
    if (addExperienceBtn && experienceForm) {
        addExperienceBtn.addEventListener('click', function() {
            experienceForm.style.display = 'block';
        });
    }

    // Education Form
    const addEducationBtn = document.getElementById('add-education');
    const educationForm = document.getElementById('education-form');
    
    if (addEducationBtn && educationForm) {
        addEducationBtn.addEventListener('click', function() {
            educationForm.style.display = 'block';
        });
    }

    // Project Form
    const addProjectBtn = document.getElementById('add-project');
    const projectForm = document.getElementById('project-form');
    
    if (addProjectBtn && projectForm) {
        addProjectBtn.addEventListener('click', function() {
            projectForm.style.display = 'block';
        });
    }

    // Certificate Form
    const addCertificateBtn = document.getElementById('add-certificate');
    const certificateForm = document.getElementById('certificate-form');
    
    if (addCertificateBtn && certificateForm) {
        addCertificateBtn.addEventListener('click', function() {
            certificateForm.style.display = 'block';
        });
    }

    // Form Cancel Buttons
    const cancelButtons = document.querySelectorAll('.cancel-form');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form) {
                form.style.display = 'none';
                form.reset();
            }
        });
    });

    // Password Toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });

    // Share Profile
    const shareBtn = document.querySelector('.share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Meu Perfil',
                    url: url
                }).catch(console.error);
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert('Link do perfil copiado para a área de transferência!');
                }).catch(console.error);
            }
        });
    }

    // Stats Click
    const stats = document.querySelectorAll('.stat');
    stats.forEach(stat => {
        stat.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            const tab = document.querySelector(`[data-tab="${tabId}"]`);
            if (tab) {
                tab.click();
            }
        });
    });
}); 