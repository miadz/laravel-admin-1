<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
<<<<<<< HEAD
    <div class="pull-left hidden-xs">
        <strong>Version</strong>&nbsp;&nbsp; {!! config('admin.version') !!}
=======
    <div class="pull-right hidden-xs">
        @if(config('admin.show_environment'))
            <strong>Env</strong>&nbsp;&nbsp; {!! env('APP_ENV') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('admin.show_version'))
        <strong>Version</strong>&nbsp;&nbsp; {!! \Encore\Admin\Admin::VERSION !!}
        @endif

>>>>>>> upstream/master
    </div>
    <!-- Default to the left -->
    <strong>Powered by <a href="https://github.com/z-song/laravel-admin" target="_blank">laravel-admin</a></strong>
</footer>