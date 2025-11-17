/**
 * Button Component
 * Reusable button component for React/Vue/Alpine.js
 */

export class Button {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
        // Add click handler or other initialization
        this.element.addEventListener('click', this.handleClick.bind(this));
    }

    handleClick(event) {
        // Add any custom click handling logic
    }

    destroy() {
        // Cleanup if needed
    }
}

// Initialize buttons on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-button-component]').forEach((button) => {
        new Button(button);
    });
});

