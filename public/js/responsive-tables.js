/**
 * Responsive Tables JavaScript Helper
 * Automatically adds data-label attributes to table cells for mobile card-style display
 * Mobile-First Responsive Design Implementation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Function to make tables responsive
    function makeTablesResponsive() {
        // Get all tables in the document
        const tables = document.querySelectorAll('.table, table');
        
        tables.forEach(table => {
            // Get all header cells
            const headers = table.querySelectorAll('thead th');
            
            // If no headers found, skip this table
            if (headers.length === 0) return;
            
            // Extract header text content
            const headerLabels = Array.from(headers).map(header => {
                return header.textContent.trim();
            });
            
            // Get all body rows
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                
                cells.forEach((cell, index) => {
                    // Add data-label attribute if header exists for this column
                    if (headerLabels[index]) {
                        cell.setAttribute('data-label', headerLabels[index]);
                    }
                });
            });
        });
    }
    
    // Run on initial load
    makeTablesResponsive();
    
    // Re-run when content is dynamically loaded (for AJAX/SPA)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                makeTablesResponsive();
            }
        });
    });
    
    // Observe the entire document for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Handle window resize for dynamic adjustments
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Add any resize-specific logic here
            adjustTableFontSizes();
        }, 250);
    });
    
    // Function to adjust font sizes based on viewport
    function adjustTableFontSizes() {
        const tables = document.querySelectorAll('.table, table');
        const viewportWidth = window.innerWidth;
        
        tables.forEach(table => {
            if (viewportWidth < 600) {
                // Mobile: smaller font
                table.style.fontSize = '0.85rem';
            } else if (viewportWidth < 1024) {
                // Tablet: medium font
                table.style.fontSize = '0.9rem';
            } else {
                // Desktop: default font
                table.style.fontSize = '1rem';
            }
        });
    }
    
    // Initial font size adjustment
    adjustTableFontSizes();
    
    // Handle table actions on mobile (expand/collapse for complex tables)
    function enhanceMobileTableInteraction() {
        const tables = document.querySelectorAll('.table-responsive');
        
        tables.forEach(wrapper => {
            const table = wrapper.querySelector('table');
            if (!table) return;
            
            // Check if table is wider than viewport on mobile
            if (window.innerWidth < 600) {
                const tableWidth = table.scrollWidth;
                const wrapperWidth = wrapper.clientWidth;
                
                if (tableWidth > wrapperWidth) {
                    // Add scroll indicator
                    wrapper.classList.add('has-horizontal-scroll');
                }
            }
        });
    }
    
    enhanceMobileTableInteraction();
    
    // Smooth scroll for tables on mobile
    const tableContainers = document.querySelectorAll('.table-responsive');
    tableContainers.forEach(container => {
        if ('ontouchstart' in window) {
            container.style.webkitOverflowScrolling = 'touch';
        }
    });
    
    // Export for global use
    window.makeTablesResponsive = makeTablesResponsive;
    
    // Add utility function to check if mobile view
    window.isMobileView = function() {
        return window.innerWidth < 600;
    };
    
    // Add utility function to check if tablet view
    window.isTabletView = function() {
        return window.innerWidth >= 600 && window.innerWidth < 1024;
    };
    
    // Add utility function to check if desktop view
    window.isDesktopView = function() {
        return window.innerWidth >= 1024;
    };
    
    // Accessibility improvements
    function enhanceTableAccessibility() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            // Add role if not present
            if (!table.getAttribute('role')) {
                table.setAttribute('role', 'table');
            }
            
            // Add aria-label if caption is present
            const caption = table.querySelector('caption');
            if (caption) {
                table.setAttribute('aria-label', caption.textContent);
            }
            
            // Ensure headers have scope attribute
            const headers = table.querySelectorAll('thead th');
            headers.forEach(header => {
                if (!header.getAttribute('scope')) {
                    header.setAttribute('scope', 'col');
                }
            });
        });
    }
    
    enhanceTableAccessibility();
    
    console.log('✅ Responsive Tables JavaScript loaded and initialized');
    console.log('📊 Current viewport:', window.innerWidth + 'px', 
                window.isMobileView() ? '(Mobile)' : 
                window.isTabletView() ? '(Tablet)' : '(Desktop)');
});
