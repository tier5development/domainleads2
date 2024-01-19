<style>
    .upper_banner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        background: #ef0c0c;
        z-index: 10;
        display: flex;
        align-items: center;
        padding: 10px 33px;
        color: white;
        font-size: 19px;
        justify-content: space-between;
        font-weight: 600;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .upper_banner p {
        margin: auto;
    }
    .upper_banner p a {
        color: #000000;
        text-decoration: underline;
        margin-left: 5px;
    }
    .upper_banner button {
        background: white;
        color: black;
        font-size: 21px;
        width: 40px;
        height: 38px;
        border: none;
        border-radius: 50%;
        padding: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>

@if (!session('banner_show'))
    <div class="upper_banner" >
        <p>Domainleads is closing down soon and going to DEMO only mode.</p>
        <button onclick="closeLandingBanner()">X</button>
    </div>
@endif

<script>
function closeLandingBanner(){
    console.log("clicked");
    const closeBannerURL = "{{ route('closeLandingBanner') }}";
    const token = "{{ csrf_token() }}";
    $.ajax({
        url: closeBannerURL,
        type: "POST",
        data: {
        _token: token
        },
        success: function(response) {
            console.log("success ::: ", response);
        },
        error: function(response) {
            console.error(response);
        },
        complete: function(response) {
            $('.upper_banner').css('display','none');
        }
    });
    // $('.landing_banner').css('display','none');
}
</script>