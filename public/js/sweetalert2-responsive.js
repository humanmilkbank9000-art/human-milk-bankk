/**
 * SweetAlert2 Responsive Configuration Helper
 * Provides mobile-optimized default configurations
 */

// Detect device type
const isMobile = () => window.innerWidth <= 600;
const isTablet = () => window.innerWidth > 600 && window.innerWidth <= 1024;
const isDesktop = () => window.innerWidth > 1024;

/**
 * Get responsive SweetAlert2 configuration
 * Automatically adjusts based on screen size
 */
const getResponsiveSwalConfig = (customConfig = {}) => {
    const baseConfig = {
        // Responsive width
        width: isMobile() ? '95vw' : isTablet() ? '80vw' : '32rem',
        
        // Button configuration
        buttonsStyling: true,
        confirmButtonColor: '#ec4899',
        cancelButtonColor: '#6b7280',
        denyButtonColor: '#dc2626',
        
        // Animation
        showClass: {
            popup: 'swal2-show',
            backdrop: 'swal2-backdrop-show'
        },
        hideClass: {
            popup: 'swal2-hide',
            backdrop: 'swal2-backdrop-hide'
        },
        
        // Mobile-specific adjustments
        ...(isMobile() && {
            heightAuto: false,
            allowOutsideClick: true,
            allowEscapeKey: true,
        }),
        
        // Accessibility
        focusConfirm: true,
        returnFocus: true,
    };
    
    return { ...baseConfig, ...customConfig };
};

/**
 * Show success message
 */
const showSuccess = (title, text = '', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        timer: options.timer || null,
        ...options
    }));
};

/**
 * Show error message
 */
const showError = (title, text = '', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        ...options
    }));
};

/**
 * Show warning message
 */
const showWarning = (title, text = '', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        icon: 'warning',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        ...options
    }));
};

/**
 * Show info message
 */
const showInfo = (title, text = '', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        icon: 'info',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        ...options
    }));
};

/**
 * Show confirmation dialog
 */
const showConfirmation = (title, text = '', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        icon: 'question',
        title: title,
        text: text,
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Yes',
        cancelButtonText: options.cancelButtonText || 'No',
        reverseButtons: isMobile(), // Put cancel first on mobile
        ...options
    }));
};

/**
 * Show loading dialog
 */
const showLoading = (title = 'Please wait...', text = '') => {
    return Swal.fire(getResponsiveSwalConfig({
        title: title,
        text: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    }));
};

/**
 * Show input dialog
 */
const showInput = (title, options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        title: title,
        input: options.inputType || 'text',
        inputLabel: options.inputLabel || '',
        inputPlaceholder: options.inputPlaceholder || '',
        inputValue: options.inputValue || '',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Submit',
        cancelButtonText: options.cancelButtonText || 'Cancel',
        inputValidator: options.inputValidator || null,
        ...options
    }));
};

/**
 * Show textarea dialog
 */
const showTextarea = (title, options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        title: title,
        input: 'textarea',
        inputLabel: options.inputLabel || '',
        inputPlaceholder: options.inputPlaceholder || '',
        inputValue: options.inputValue || '',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Submit',
        cancelButtonText: options.cancelButtonText || 'Cancel',
        inputValidator: options.inputValidator || null,
        inputAttributes: {
            'aria-label': options.ariaLabel || 'Type your message here',
            style: 'min-height: 120px;'
        },
        ...options
    }));
};

/**
 * Show toast notification
 */
const showToast = (title, icon = 'success', options = {}) => {
    const Toast = Swal.mixin({
        toast: true,
        position: isMobile() ? 'bottom' : 'top-end',
        showConfirmButton: false,
        timer: options.timer || 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    return Toast.fire({
        icon: icon,
        title: title,
        ...options
    });
};

/**
 * Show delete confirmation with specific styling
 */
const showDeleteConfirmation = (itemName = 'this item', options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        title: 'Are you sure?',
        text: `You won't be able to revert this! Delete ${itemName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        reverseButtons: isMobile(),
        ...options
    }));
};

/**
 * Show multi-step form (wizard)
 */
const showMultiStepForm = (steps = [], options = {}) => {
    const queue = steps.map((step, index) => ({
        title: step.title,
        text: step.text || '',
        input: step.input || 'text',
        inputLabel: step.inputLabel || '',
        inputPlaceholder: step.inputPlaceholder || '',
        inputValidator: step.inputValidator || null,
        showCancelButton: true,
        progressSteps: steps.map((_, i) => i + 1),
        currentProgressStep: index,
        ...step.config
    }));
    
    return Swal.queue(queue.map(config => getResponsiveSwalConfig(config)));
};

/**
 * Show timer-based alert
 */
const showTimedAlert = (title, text, duration = 3000, options = {}) => {
    return Swal.fire(getResponsiveSwalConfig({
        title: title,
        text: text,
        timer: duration,
        timerProgressBar: true,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        ...options
    }));
};

/**
 * Update existing SweetAlert
 */
const updateSwal = (config) => {
    Swal.update(getResponsiveSwalConfig(config));
};

/**
 * Close SweetAlert programmatically
 */
const closeSwal = () => {
    Swal.close();
};

// Export for use in modules or make available globally
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        getResponsiveSwalConfig,
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showConfirmation,
        showLoading,
        showInput,
        showTextarea,
        showToast,
        showDeleteConfirmation,
        showMultiStepForm,
        showTimedAlert,
        updateSwal,
        closeSwal,
        isMobile,
        isTablet,
        isDesktop
    };
}

// Make available globally for non-module scripts
window.SwalHelpers = {
    getResponsiveSwalConfig,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirmation,
    showLoading,
    showInput,
    showTextarea,
    showToast,
    showDeleteConfirmation,
    showMultiStepForm,
    showTimedAlert,
    updateSwal,
    closeSwal,
    isMobile,
    isTablet,
    isDesktop
};
