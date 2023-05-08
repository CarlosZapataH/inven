
$(document).ready(function() {
    "use strict";
    // resize window
    function resizeWindow() {
        var timesRun = 0;
        var interval = setInterval(function(){
            timesRun += 1;
            if(timesRun === 5){
                clearInterval(interval);
            }
            window.dispatchEvent(new Event('resize'));
        }, 62.5);
    }

    //  navbar toogler
    $('.navbar-toggler').on('click', function(){

        if ($(this).hasClass('left-sidebar-toggler')) {
            $('body').toggleClass('left-sidebar-hidden');
            resizeWindow();
        }

        if ($(this).hasClass('right-sidebar-toggler')) {
            $('body').toggleClass('right-sidebar-hidden');
            resizeWindow();
        }

        if ($(this).hasClass('mobile-leftside-toggler')) {
            $('body').toggleClass('mobile-leftside-show');
            resizeWindow();
        }

        if ($(this).hasClass('mobile-rightside-toggler')) {
            $('body').toggleClass('mobile-rightside-show');
            resizeWindow();
        }

    });

    // search toggle
    $('.search-toggle').on('click', function() {
        $('.search-container').toggleClass('open');
        $('.custom-search').focus();
    });

    //nav accordion
    $('#nav-accordion').dcAccordion({
        eventType: 'click',
        autoClose: false,
        saveState: true,
        disableLink: true,
        speed: 'fast',
        showCount: false,
        autoExpand: true,
        cookie: 'dcjq-accordion-1',
        classExpand: 'dcjq-current-parent'
    });
});
