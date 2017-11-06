$(document).ready(function () {
    CycoSidebarToggle.init();
    //Show/hide the left sidebar.
    $('#hide-left-sidebar').click(function () {
        CycoSidebarToggle.toggleSidebarExpanded('left');
    });
    //Show/hide the right sidebar.
    $('#hide-right-sidebar').click(function () {
        CycoSidebarToggle.toggleSidebarExpanded('right');
    });
});

var CycoSidebarToggle = {
    /**
     * Relative URL to where the sidebar icons are stored.
     */
    PATH_TO_SIDEBAR_ICONS: '/images/octicons/',
    /**
     * Initial state of both sidebars. Properties left and right of the object.
     */
    expanded: {},
    /**
     * Is localStorage available?
     */
    isLocalStorageAvailable: false,
    /**
     * Test whether localStorage is available.
     * @returns {boolean} True if available.
     */
    testLocalStorageAvailable: function () {
        var available = true;
        try {
            window.localStorage.setItem('dogs', 'are the best');
            window.localStorage.removeItem('dogs');
        }
        catch (e) {
            available = false;
        }
        return available;
    },
    /**
     * Init the sidebar functions.
     */
    init: function(){
        this.isLocalStorageAvailable = this.testLocalStorageAvailable();
        if ( this.isLocalStorageAvailable ) {
            //Init expanded to yes, if no keys are stored (will be if this is the first time accessing the site).
            if ( ! window.localStorage.getItem('leftSidebarExpanded' ) ) {
                window.localStorage.setItem('leftSidebarExpanded', 'yes');
            }
            if ( ! window.localStorage.getItem('rightSidebarExpanded' ) ) {
                window.localStorage.setItem('rightSidebarExpanded', 'yes');
            }
            //Get the stored values for expanded for left and right.
            this.expanded.left = (window.localStorage.getItem('leftSidebarExpanded') === 'yes');
            this.setSidebarExpanded('left', this.expanded.left);
            this.expanded.right = (window.localStorage.getItem('rightSidebarExpanded') === 'yes');
            this.setSidebarExpanded('right', this.expanded.right);
        }
        else {
            //No localStorage.
            //Sidebars will be visible. The HTML/CSS makes them visible.
            this.expanded.left = true;
            this.expanded.right = true;
        }
    },
    /**
     * Is a sidebar expanded?
     * @param {string} sidebarName left or right.
     * @returns {boolean} True if expanded, else false.
     */
    isSidebarExpanded: function(sidebarName){
        return this.expanded[sidebarName];
    },
    setSidebarExpanded: function(sidebarName, expanded) {
        if ( sidebarName !== 'left' && sidebarName !== 'right' ) {
            throw 'setSidebarExpanded: unexpected sidebar: ' + sidebarName;
        }
        var sidebarIcon = $('#hide-' + sidebarName + '-sidebar');
        var sidebar = $('#' + sidebarName + '-sidebar');
        this.expanded[sidebarName] = expanded;
        if (expanded) {
            sidebar.removeClass('collapse');
            if (sidebarName === 'left') {
                sidebarIcon.attr('src', this.PATH_TO_SIDEBAR_ICONS + 'triangle-left.svg');
            }
            else {
                sidebarIcon.attr('src', this.PATH_TO_SIDEBAR_ICONS + 'triangle-right.svg');
            }
        }
        else {
            sidebar.addClass('collapse');
            if (sidebarName === 'left') {
                sidebarIcon.attr('src', this.PATH_TO_SIDEBAR_ICONS + 'triangle-right.svg');
            }
            else {
                sidebarIcon.attr('src', this.PATH_TO_SIDEBAR_ICONS + 'triangle-left.svg');
            }
        }
        if ( this.isLocalStorageAvailable ) {
            //Put the new state into localStorage.
            window.localStorage.setItem(sidebarName + 'SidebarExpanded', expanded ? 'yes' : 'no');
        }
    },
    /**
     * Toggle the state of a sidebar
     * @param {string} sidebarName The sidebar to toggle.
     */
    toggleSidebarExpanded: function(sidebarName) {
        var expanded = this.isSidebarExpanded(sidebarName);
        this.setSidebarExpanded(sidebarName, ! expanded);
    }
};

