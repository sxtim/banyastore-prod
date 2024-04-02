"use strict";

/**
 * @function Tabs()
 *
 * Simple tabbed content UI component
 *
 * @param args {object} Settings for controlling the functionality of the component
 * @returns bindEventListeners {function} Event listeners for the component
 */

function Tabs(args) {
    // Scope-safe constructors
    if (!(this instanceof Tabs)) {
        return new Tabs();
    }

    /**
     * Default component settings
     *
     * @param container {string} Classname for container of the entire component
     * @param trigger {string} Element that toggles content
     * @param content {string} Classname for the content
     */
    var defaults = {
        container: '[data-tab-component]',
        trigger: '[role="tab"]',
        content: '[role="tabpanel"]'
    };

    // If there are no settings overrides
    var settings = (typeof args !== 'undefined') ? args : defaults;

    /**
     * @function toggle()
     *
     * Handles the displaying/hiding of content
     *
     * @returns null
     */
    var toggle = function() {
        var parent = this.closest(settings.container),
            target = this.getAttribute('aria-controls'),
            content = document.getElementById(target),
            toggles = parent.querySelectorAll(settings.trigger),
            all_content = parent.querySelectorAll(settings.content);

        // Update visibility
        for (var i = 0, len = toggles.length; i < len; i++) {
            toggles[i].setAttribute('aria-selected', 'false');
            all_content[i].setAttribute('aria-hidden', 'true');
        }

        this.setAttribute('aria-selected', 'true');
        content.setAttribute('aria-hidden', 'false');
    };

    /**
     * @function bindEventListeners()
     *
     * Attach event listeners
     *
     * @returns null
     */
    var bindEventListeners = function() {
        var trigger = document.querySelectorAll(settings.trigger);

        //
        // TODO
        // Use event delgation to add event handlers
        //
        for (var i = 0, len = trigger.length; i < len; i++) {
            trigger[i].addEventListener('click', function(event) {
                toggle.call(this);
            });

            trigger[i].addEventListener('keydown', function(event) {
                if (event.which == 13) {
                    toggle.call(this);
                }
            });
        };
    };

    return bindEventListeners();
}

// Create an instance of component
window.onload = function() {
    var tabs = new Tabs();
};


// Card comment more/hide text

//TODO Card comment more/hide text

// let cardComment = document.querySelector(".card-comment");
// let compStyles = window.getComputedStyle(cardComment);
// let cardCommentHeight = parseInt(compStyles.height, 10);
// console.log(cardCommentHeight);
//
// const cardCommentBtn = document.querySelector(".card-comment__btn");
//
//     function toggleDescriptionHeight(e) {
//         cardComment.classList.toggle('expanded');
//         if (e.target.textContent === 'Expand') {
//             e.target.textContent = 'Collapse';
//         } else {
//             e.target.textContent = 'Expand';
//         }
//     } // this function has been moved out of the if condition
//
//
//     if (cardCommentHeight > 100) { // the 'txt >= 100 &&' can go
//         cardCommentBtn.addEventListener('click', toggleDescriptionHeight);
//     } else {
//         showHideBtn.style.display = "none";
//     };