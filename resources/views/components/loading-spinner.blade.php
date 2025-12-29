<div {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm']) }} style="display: none;" id="global-loading-spinner">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
        <p class="mt-4 text-gray-600 font-medium">Loading...</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.getElementById('global-loading-spinner');
        
        // Show on form submit
        document.querySelectorAll('form').forEach(form => {
            // Skip forms that aren't GET search or important posts, 
            // or specific ones we want to exclude.
            // For now, let's target specific main forms by adding a class or handle generic
            if(!form.classList.contains('no-loader')) {
                form.addEventListener('submit', function() {
                    if (spinner) spinner.style.display = 'flex';
                });
            }
        });

        // Hide on page show (bfcache restore)
        window.addEventListener('pageshow', function(event) {
            if (spinner) spinner.style.display = 'none'; 
        });
    });
</script>
