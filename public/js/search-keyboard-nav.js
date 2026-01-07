/**
 * Search Keyboard Navigation Utility
 * Enables arrow key navigation and Enter selection in search results
 */
const SearchKeyboardNav = (function () {
    const instances = [];

    /**
     * Initialize keyboard navigation for a search component
     * @param {Object} options Configuration options
     * @param {string} options.inputSelector - CSS selector for the search input
     * @param {string} options.resultsSelector - CSS selector for the results container
     * @param {string} options.itemSelector - CSS selector for individual result items (relative to results container)
     * @param {string} options.actionSelector - CSS selector for the clickable action element (relative to item)
     * @param {string} [options.highlightClass='kb-highlighted'] - CSS class for highlighted state
     */
    function init(options) {
        const config = {
            inputSelector: options.inputSelector,
            resultsSelector: options.resultsSelector,
            itemSelector: options.itemSelector,
            actionSelector: options.actionSelector,
            highlightClass: options.highlightClass || 'kb-highlighted'
        };

        let currentIndex = -1;

        const input = document.querySelector(config.inputSelector);
        if (!input) {
            console.warn('SearchKeyboardNav: Input not found:', config.inputSelector);
            return;
        }

        // Reset index when input changes
        input.addEventListener('input', () => {
            currentIndex = -1;
            clearHighlight();
        });

        // Handle keyboard navigation
        input.addEventListener('keydown', handleKeydown);

        function handleKeydown(e) {
            if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
                return;
            }

            const results = document.querySelector(config.resultsSelector);
            if (!results) return;

            const items = results.querySelectorAll(config.itemSelector);
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, items.length - 1);
                updateHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, 0);
                updateHighlight(items);
            } else if (e.key === 'Enter') {
                if (currentIndex >= 0 && currentIndex < items.length) {
                    e.preventDefault();
                    const item = items[currentIndex];
                    const action = item.querySelector(config.actionSelector);
                    if (action) {
                        action.click();
                    }
                }
            }
        }

        function updateHighlight(items) {
            // Clear all highlights
            items.forEach(item => item.classList.remove(config.highlightClass));

            // Add highlight to current item
            if (currentIndex >= 0 && currentIndex < items.length) {
                const currentItem = items[currentIndex];
                currentItem.classList.add(config.highlightClass);

                // Scroll into view if needed
                currentItem.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }

        function clearHighlight() {
            const results = document.querySelector(config.resultsSelector);
            if (!results) return;

            const items = results.querySelectorAll(config.itemSelector);
            items.forEach(item => item.classList.remove(config.highlightClass));
        }

        function reset() {
            currentIndex = -1;
            clearHighlight();
        }

        // Store instance for potential reset
        instances.push({ config, reset });
    }

    /**
     * Reset all navigation states
     */
    function resetAll() {
        instances.forEach(instance => instance.reset());
    }

    return {
        init: init,
        resetAll: resetAll
    };
})();
