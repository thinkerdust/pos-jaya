<div class="nk-footer">
    <div class="container-fluid">
        <div class="nk-footer-wrap">
            <div class="nk-footer-copyright"> &copy; {{ date('Y') }} ForYou by Sinarmas Land.
            </div>
            <div class="nk-footer-links">
                
            </div>
        </div>
    </div>
</div>

<style>
    /*Select2 ReadOnly Start*/
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

/*Select2 ReadOnly End*/

</style>

<script>
    // Check if the user is offline
    if (!navigator.onLine) {
        // User is offline, show SweetAlert notification
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'You are currently offline. Please check your internet connection and try again.',
        });
    }

    window.addEventListener('online', () => {
        Swal.fire({
            icon: 'success',
            title: 'Great!',
            text: 'You are back online. Welcome back!',
        });
    });
</script>

