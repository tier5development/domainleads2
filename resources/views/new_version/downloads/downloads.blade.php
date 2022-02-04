<!DOCTYPE html>
<html lang="en">
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Font awesome icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.0/css/font-awesome.css" integrity="sha512-72McA95q/YhjwmWFMGe8RI3aZIMCTJWPBbV8iQY3jy1z9+bi6+jHnERuNrDPo/WGYEzzNs4WdHNyyEr/yXJ9pA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@include('new_version.section.user_panel_head', ['title' => 'Downloads'])
<body>
    @include('new_version.shared.loader')
    <div class="container">

        @include('new_version.section.user_panel_header', ['user' => $user])
        <section class="mainBody" style="height: auto;">
            @include('new_version.shared.right-panel')
            <table class="table">
            <thead>
                <tr>
                    <td>Filename</td>
                    <td>Created at</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                @if(count($downloadData))
                    @foreach($downloadData as $download)
                    <tr>
                    <td> {{$download->file_name}} </td>
                    <td> {{$download->created_at}} </td>
                    <td> <a href="{{$download->file_path}}"> <i class="fa fa-download" aria-hidden="true"></i> </a> </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td> No file for download !</td>
                    <td></td>
                </tr>
                @endif
            </tbody>                
            </table>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>
        
        {{-- Include footer --}}
        @include('new_version.shared.dashboard-footer')
    </div>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>