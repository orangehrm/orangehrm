$(document).ready(function () {
    const menuPadding = 50;

    $(window).on('resize', function () {
        unsetMenuOverflow();
        checkMenuOverflowAndApply();
    });

    checkMenuOverflowAndApply();

    function checkMenuOverflowAndApply() {
        const menuWidth = $('#mainMenuFirstLevelUnorderedList').width();
        const scrollWidth = $('#mainMenuFirstLevelUnorderedList')[0].scrollWidth;
        if (Math.round(menuWidth + menuPadding) < scrollWidth) {
            applyMenuOverflow();
            scrollToCurrentMenuItem();
        } else {
            unsetMenuOverflow();
        }
    }

    function scrollToCurrentMenuItem() {
        const menuItem = $('#mainMenuFirstLevelUnorderedList').find('.current');
        if (menuItem.length !== 0) {
            if (menuItem.position().left > $('#mainMenuRightArrow').position().left) {
                handleMainMenuRightArrowClick();
            } else if (menuItem.position().left < $('#mainMenuLeftArrow').position().left) {
                handleMainMenuLeftArrowClick();
            }
        }
    }

    function applyMenuOverflow() {
        $('#mainMenu').addClass('menu-overflow');
        $('#mainMenuFirstLevelUnorderedList').removeClass('main-menu-first-level-unordered-list-width');
        $('#mainMenuFirstLevelUnorderedList').addClass('main-menu-first-level-unordered-list-grid');
        $('#mainMenu').append('<div id="mainMenuLeftArrow" class="menu-left-arrow"><</div>');
        $('#mainMenu').append('<div id="mainMenuRightArrow" class="menu-right-arrow">></div>');
        $('#mainMenu').on('click', '.menu-left-arrow', handleMainMenuLeftArrowClick);
        $('#mainMenu').on('click', '.menu-right-arrow', handleMainMenuRightArrowClick);

    }

    function unsetMenuOverflow() {
        $('#mainMenu').removeClass('menu-overflow');
        $('#mainMenuFirstLevelUnorderedList').removeClass('main-menu-first-level-unordered-list-grid');
        $('#mainMenuFirstLevelUnorderedList').addClass('main-menu-first-level-unordered-list-width');
        $('#mainMenu').off('click', '.menu-left-arrow', handleMainMenuLeftArrowClick);
        $('#mainMenu').off('click', '.menu-right-arrow', handleMainMenuRightArrowClick);
        $('#mainMenuLeftArrow').remove();
        $('#mainMenuRightArrow').remove();
    }

    function handleMainMenuLeftArrowClick() {
        $('.main-menu-first-level-unordered-list').animate({
            scrollLeft: -getMenuOverflow()
        }, 250);
    }

    function handleMainMenuRightArrowClick() {
        $('.main-menu-first-level-unordered-list').animate({
            scrollLeft: getMenuOverflow()
        }, 250);
    }

    function getMenuOverflow() {
        const menuWidth = $('#mainMenuFirstLevelUnorderedList').width();
        const scrollWidth = $('#mainMenuFirstLevelUnorderedList')[0].scrollWidth;
        return scrollWidth - menuWidth;
    }
});
