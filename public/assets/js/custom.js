import $ from "jquery";

// Set up CSRF token for all AJAX requests
const csrfToken = $('meta[name="csrf-token"]').attr('content');

if (csrfToken) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
} else {
    console.error('CSRF token not found. Please include it in your Blade layout.');
}

// Utility methods for button states
window.showButtonLoading = function (button) {
    const $button = $(button);
    $button.data('original-text', $button.html()); // Save the original text
    $button.prop('disabled', true).html('Loading...');
};

window.resetButtonState = function (button) {
    const $button = $(button);
    $button.prop('disabled', false).html($button.data('original-text')); // Restore original text
};
