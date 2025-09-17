// Account Page JavaScript
class StdAccountManager {
    constructor() {
        this.form = document.querySelector('.std-account-form');
        this.currentPasswordInput = document.getElementById('std-current-password');
        this.newPasswordInput = document.getElementById('std-new-password');
        this.confirmPasswordInput = document.getElementById('std-confirm-password');
        this.saveBtn = document.querySelector('.std-save-btn');
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupPasswordValidation();
    }
    
    setupEventListeners() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleFormSubmission(e));
        
        // Password validation on input
        [this.currentPasswordInput, this.newPasswordInput, this.confirmPasswordInput].forEach(input => {
            input.addEventListener('input', () => this.validatePasswordField(input));
            input.addEventListener('blur', () => this.validatePasswordField(input));
        });
        
        // Sidebar navigation
        this.setupSidebarNavigation();
        
        // Logout functionality
        this.setupLogout();
    }
    
    setupPasswordValidation() {
        // Add password strength indicator
        this.newPasswordInput.addEventListener('input', () => {
            this.updatePasswordStrength(this.newPasswordInput.value);
        });
    }
    
    validatePasswordField(input) {
        const value = input.value.trim();
        const fieldName = input.id.replace('std-', '').replace('-', ' ');
        
        // Remove existing validation states
        this.removeValidationState(input);
        
        // Validate based on field type
        if (input === this.currentPasswordInput) {
            if (value && value.length < 6) {
                this.showError(input, 'Current password must be at least 6 characters');
                return false;
            }
        } else if (input === this.newPasswordInput) {
            if (value && value.length < 8) {
                this.showError(input, 'New password must be at least 8 characters');
                return false;
            }
            if (value && !this.isStrongPassword(value)) {
                this.showError(input, 'Password must contain uppercase, lowercase, number, and special character');
                return false;
            }
        } else if (input === this.confirmPasswordInput) {
            if (value && value !== this.newPasswordInput.value) {
                this.showError(input, 'Passwords do not match');
                return false;
            }
        }
        
        if (value) {
            this.showSuccess(input);
        }
        
        this.updateSaveButtonState();
        return true;
    }
    
    isStrongPassword(password) {
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        
        return hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChar;
    }
    
    updatePasswordStrength(password) {
        if (!password) return;
        
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;
        
        // Visual feedback
        const inputWrapper = this.newPasswordInput.closest('.std-input-wrapper');
        inputWrapper.style.borderColor = strength >= 4 ? '#27ae60' : strength >= 2 ? '#f39c12' : '#e74c3c';
    }
    
    showError(input, message) {
        const inputWrapper = input.closest('.std-input-wrapper');
        const existingError = inputWrapper.querySelector('.std-error-message');
        
        if (existingError) {
            existingError.remove();
        }
        
        input.style.borderColor = '#e74c3c';
        input.style.backgroundColor = '#fdf2f2';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'std-error-message';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        inputWrapper.appendChild(errorDiv);
    }
    
    showSuccess(input) {
        const inputWrapper = input.closest('.std-input-wrapper');
        const existingError = inputWrapper.querySelector('.std-error-message');
        
        if (existingError) {
            existingError.remove();
        }
        
        input.style.borderColor = '#27ae60';
        input.style.backgroundColor = '#f0f9f4';
    }
    
    removeValidationState(input) {
        const inputWrapper = input.closest('.std-input-wrapper');
        const existingError = inputWrapper.querySelector('.std-error-message');
        
        if (existingError) {
            existingError.remove();
        }
        
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }
    
    updateSaveButtonState() {
        const currentPasswordValid = this.currentPasswordInput.value.trim().length >= 6;
        const newPasswordValid = this.newPasswordInput.value.trim().length >= 8 && this.isStrongPassword(this.newPasswordInput.value);
        const confirmPasswordValid = this.confirmPasswordInput.value === this.newPasswordInput.value;
        
        const isFormValid = currentPasswordValid && newPasswordValid && confirmPasswordValid;
        
        this.saveBtn.disabled = !isFormValid;
        
        if (isFormValid) {
            this.saveBtn.style.opacity = '1';
            this.saveBtn.style.cursor = 'pointer';
        } else {
            this.saveBtn.style.opacity = '0.6';
            this.saveBtn.style.cursor = 'not-allowed';
        }
    }
    
    async handleFormSubmission(e) {
        e.preventDefault();
        
        // Validate all fields
        const currentValid = this.validatePasswordField(this.currentPasswordInput);
        const newValid = this.validatePasswordField(this.newPasswordInput);
        const confirmValid = this.validatePasswordField(this.confirmPasswordInput);
        
        if (!currentValid || !newValid || !confirmValid) {
            this.showNotification('Please fix the errors above before saving', 'error');
            return false;
        }
        
        // Show loading state
        this.showLoadingState();
        
        try {
            // Simulate API call
            await this.saveChanges();
            
            // Show success message
            this.showNotification('Account settings saved successfully!', 'success');
            
            // Reset form
            setTimeout(() => {
                this.resetForm();
            }, 2000);
            
        } catch (error) {
            this.showNotification('An error occurred. Please try again.', 'error');
            this.hideLoadingState();
        }
        
        return false;
    }
    
    async saveChanges() {
        const formData = {
            currentPassword: this.currentPasswordInput.value,
            newPassword: this.newPasswordInput.value,
            confirmPassword: this.confirmPasswordInput.value
        };
        
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Log form data (in real app, send to server)
        console.log('Account settings update:', formData);
        
        // Simulate random success/failure for demo
        if (Math.random() > 0.1) { // 90% success rate
            return { success: true };
        } else {
            throw new Error('Network error');
        }
    }
    
    showLoadingState() {
        this.saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        this.saveBtn.disabled = true;
    }
    
    hideLoadingState() {
        this.saveBtn.innerHTML = '<i class="fas fa-save"></i> Save changes';
        this.saveBtn.disabled = false;
    }
    
    resetForm() {
        this.form.reset();
        [this.currentPasswordInput, this.newPasswordInput, this.confirmPasswordInput].forEach(input => {
            this.removeValidationState(input);
        });
        this.updateSaveButtonState();
    }
    
    setupSidebarNavigation() {
        const sidebarLinks = document.querySelectorAll('.std-sidebar-link:not(.std-logout)');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all links
                sidebarLinks.forEach(l => l.classList.remove('std-active'));
                
                // Add active class to clicked link
                link.classList.add('std-active');
                
                // Show notification (in real app, navigate to different pages)
                const pageName = link.querySelector('span').textContent;
                this.showNotification(`Navigating to ${pageName}...`, 'info');
            });
        });
    }
    
    setupLogout() {
        const logoutLink = document.querySelector('.std-sidebar-link.std-logout');
        
        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            
            if (confirm('Are you sure you want to logout?')) {
                this.showNotification('Logging out...', 'info');
                
                // Simulate logout
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1000);
            }
        });
    }
    
    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.std-notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `std-notification std-${type}-notification`;
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        
        notification.innerHTML = `
            <i class="${icons[type]}"></i>
            <span>${message}</span>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize account manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new StdAccountManager();
});

// Add notification animation styles
const notificationStyles = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);
