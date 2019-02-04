<html>
  <head>
  </head>
  <body> 
  	   Hi, {{ucwords($user->name)}}
            Please verify your email by clicking the link below.
        <br>
        <a href="{{route('verifyEmail', ['id' => $user->id])}}">VerifyEmail</a>
  </body>
</html>