<html lang="en">
@include('layouts.header')
<head>


	<title>Search</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="{{url('/')}}/public/css/notifi-widget.css">
    <script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>

<style type="text/css">
.form-container {
    width: 60%
}
.dropdown dd,
.dropdown dt {
  margin: 0px;
  padding: 0px;
}

.dropdown ul {
  margin: -1px 0 0 0;
}

.dropdown dd {
  position: relative;
}

.dropdown a,
.dropdown a:visited {
  color: #fff;
  text-decoration: none;
  outline: none;
  font-size: 12px;
}

.dropdown dt a {
  /*background-color: #4F6877;*/
  display: block;
  padding: 8px 20px 5px 10px;
  min-height: 25px;
  /*line-height: 24px;
  overflow: hidden;
  border: 0;
  width: 272px;*/
  border: 1px solid #ccc;
    height: 40px;
    border-radius: 5px;
    width: 100%;
    color: #666 !important;
    font-size: 14px !important;
}

.dropdown dt a span,
.multiSel span {
  cursor: pointer;
  display: inline-block;
  padding: 0 3px 2px 0;
}

.dropdown dd ul {
  background-color: #eee;
  border: 1px solid #ccc;
  color: #666;
  display: none;
  left: 0px;
  padding: 2px 15px 2px 5px;
  position: absolute;
  top: 2px;
  width: 100%;
  list-style: none;
  height: 160px;
  overflow: auto;
  font-size: 14px !important;
  z-index: 9999;
}

.dropdown dd ul li{
	padding: 10px;
	font-size: 16px;
}

.dropdown span.value {
  display: none;
}

.dropdown dd ul li a {
  padding: 5px;
  display: block;
}

.dropdown dd ul li a:hover {
  background-color: #fff;
}

.dropdown dd ul li input{
	margin-right: 5px;
    vertical-align: middle;
}

form{
	border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
}

.overlay{background: rgba(0,0,0,0.7); width: 100%; height: 100%; position: fixed; top: 0;
         z-index: 1111;
         }
         .loader-main{width: 100px; height: 100px; position: absolute; margin-left: -50px; margin-top: -50px; top: 50%; left: 50%;}
         .loader-main img{max-width: 100%;}


</style>
        

</head>

<body>
	
	@if(isset($totalUnlockAbility))
	<div class="notiPop">
		<div class="popupInner">
			<div class="popupImg">
				D
			</div>
			<div class="popupBody">
				<p>You have unlocked <span id="notipop-num"></span> leads today.</p>
				<p>You can unlocked upto <span id="notipop-total-num">{{$totalUnlockAbility}}</span> leads per day.</p>
			</div>
		</div>
		<div class="popupClose"><span id="notipopup-close" class="glyphicon glyphicon-remove"></span></div>
	</div>
	@endif

	<div id="ajax-loader" style="display: none;">
		<div class="overlay">
		   <div class="loader-main">
			  <img src="{{url('/')}}/images/loader.gif">
		   </div>
		</div>
	</div>

		<div class="container">
            
            <p><strong>CHANGE PASSWORD</strong></p>
			<div class="form-container centered">
                <div class="errorMsg">
                    @if(Session::has('fail'))
                        <div class="alert alert-danger fade in alert-dismissible" style="margin-top:18px;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            <strong>Error!</strong> {{Session::get('fail')}}
                        </div>
                        @php Session::forget('fail') @endphp
                    @elseif(Session::has('success'))
                        <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            <strong>Success!</strong> {{Session::get('success')}}
                        </div>
                        @php Session::forget('success') @endphp
                    @endif
                    
                </div>
                <form action="{{route('changePasswordPost')}}" method="POST" class="change-password form-group" id="changePasswordForm">
                    {{csrf_field()}}
                    <div class="">
                            <label>Email : </label>
                            <input id="email" name="email" value="{{$user->email}}" class="form-control" readonly>
                            <span id="email_err" style="color: red"></span>
                            <br>
                    </div>
                    <div class="">
                            <label>Old Password : </label>
                            <input type="password" name="o_pass" id="o_pass" class="form-control">
                            <span id="o_pass_err" style="color: red"></span>
                            <br>
                    </div>
                    <div class="">
                            <label>New Password : </label>
                            <input type="password" name="pass" id="pass" class="form-control">
                            <span id="pass_err" style="color: red"></span>
                            <br>
                    </div>
                    <div class="">
                            <label>Confirm Password : </label>
                            <input type="password" name="c_pass" id="c_pass" class="form-control">
                            <span id="c_pass_err" style="color: red"></span>
                            <br>
                    </div>
                    <button class="btn btn-md btn-success" id="submitChangePasswordForm" type="submit">Submit</button>
                </form>
            </div>
		</div>
</body>


    <script type="text/javascript">
        var EMAIL = "{{$user->email}}";

        var checkPass = function(pass) {
            var pattern = /^.{6,}$/;
            return pattern.test(pass);
        }
        var clearAll = function() {
            $('#email_err').text('');
            $('#pass_err').text('');
            $('#c_pass_err').text('');
        }
        var checkChangePasswordForm = function() {
            // Check the form
            var flag = true;
            var email = $('#email').val();
            var pass = $('#pass').val();
            var c_pass = $('#c_pass').val();
            if(email !== EMAIL) {
                var msg = 'Email did not match with your email';
                $('#email_err').text(msg);
                return {msg : msg, flag: false}
            } else if(!checkPass(pass)) {
                var msg = 'Password should be minimum of 6 characters.';
                $('#pass_err').text(msg);
                return {msg : msg, flag: false}
            } else if(pass !== c_pass) {
                var msg = 'Password and confirm password should match.'
                $('#c_pass_err').text(msg);
                return {msg : msg, flag: false}
            }
            return {msg : 'success', flag: true};
        }
        $(document).ready(function() {
           $('#submitChangePasswordForm').click(function(e) {
                e.preventDefault();
                clearAll();
                var ob = checkChangePasswordForm();
                if(ob.flag) {
                    $('#changePasswordForm').submit();
                }
           });
        });
    </script>
</html>