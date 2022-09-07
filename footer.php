<script language=JavaScript>
    /* It's possible, but this would block the following commands:
     * F12
     * Ctrl + Shift + I
     * Ctrl + Shift + J
     * Ctrl + Shift + C
     * Ctrl + U
     */

    // Keys
    document.onkeydown = function(e) {
        if (event.keyCode == 123) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
            return false;
        }
        if (e.keyCode === 114 || (e.ctrlKey && e.keyCode === 70)) {
            return false;
        }
    }
</script>