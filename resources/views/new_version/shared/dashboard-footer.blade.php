<footer class="{{isset($class) && strlen($class) > 0 ? $class : 'footer'}}">
    &copy; 2017 Powered by Tier5 
    <span>
        <a href="{{route('privacyPolicy')}}">Privacy Policy</a>
        <a href="{{route('termsOfUse')}}">Terms of Use</a>
    </span>
</footer>