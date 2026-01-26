<?php
/**
 * Preloader Template Part
 * 
 * @package Adorkini
 */
?>
<div id="adorkini-preloader">
    <div class="spinner"></div>
</div>

<script>
    // Critical Preloader Logic - Inline to execute immediately
    (function() {
        var preloader = document.getElementById('adorkini-preloader');
        
        function hidePreloader() {
            if(preloader) {
                preloader.classList.add('hidden');
                // Optional: remove after transition
                setTimeout(function() {
                   // preloader.style.display = 'none'; 
                }, 400);
            }
        }

        // Hide on load
        window.addEventListener('load', hidePreloader);
        
        // Failsafe: Hide after 3 seconds max (e.g. slow network or FB browser issues)
        setTimeout(hidePreloader, 3000);
    })();
</script>
