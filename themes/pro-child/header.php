<?php

// =============================================================================
// HEADER.PHP
// -----------------------------------------------------------------------------
// The site header.
// =============================================================================

?>

<?php x_get_view( 'header', 'base' ); ?>
<script>
    (function () {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = 'https://app.termly.io/embed.min.js';
        s.id = '981b7149-96c2-43e9-80ff-2f02be47b364';
        s.setAttribute("data-name", "termly-embed-banner");
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
    })();
</script>
