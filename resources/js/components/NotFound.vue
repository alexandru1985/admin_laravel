<template>
    <div class="container">
        <section class="content">
            <div class="error-page" style="margin-top:100px;">
                <h2 class="headline text-yellow"> 404</h2>

                <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>

                <p>
                    We could not find the page you were looking for.
                </p>
                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
            </section>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
