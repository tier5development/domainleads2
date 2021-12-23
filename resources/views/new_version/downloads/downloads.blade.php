<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Downloads'])
<body>
    @include('new_version.shared.loader')
    <div class="container">

        @include('new_version.section.user_panel_header', ['user' => $user])
        <section class="mainBody">
            {{-- Include common dashboard right panel --}}
            @include('new_version.shared.right-panel')
            <table>
                <tr>
                    <td>Filename</td>
                    <td>Action</td>
                </tr>
                @if(count($downloadData))
                    @foreach($downloadData as $download)
                    <tr>
                    <td>{{$download->file_name}}</td>
                    <td><a href="{{$download->file_path}}"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td> No file for download !</td>
                    <td></td>
                </tr>
                @endif                
            </table>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>
        
        {{-- Include footer --}}
        @include('new_version.shared.dashboard-footer')
    </div>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

    <script type="text/javascript">
        
    </script>
</body>
</html>