<html>
  <head>
  </head>
  <body> 
  	   Hi {{$user_name}},
             Please visit this <a href="{{$link}}">link</a> to update your password. <br>
             This link is valid for the next {{config('settings.RESET-PASSWORD-LIFE')}} hours.
  </body>
</html>