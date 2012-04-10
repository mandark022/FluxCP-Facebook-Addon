<?php if (!empty($error)): ?>
	<p class="red" style="font-weight: bold"><?php echo htmlspecialchars($error) ?></p>
<?php endif ?>
<center>
	  <div id="fb-root"></div>
      <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '<?php echo $api_id; ?>',
            status     : true, 
            cookie     : true,
            xfbml      : true,
            oauth      : true,
          });
        };
        (function(d){
           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js";
           d.getElementsByTagName('head')[0].appendChild(js);
         }(document));
      </script>
      <div 
        class="fb-registration" 
        data-fields="[
			{'name':'name'},
			{'name':'username', 'description':'Enter Username', 'type':'text'}, 
			{'name':'email'},
			{'name':'password'},
			{'name':'birthday'},
			{'name':'gender'},
			{'name':'captcha','description':'Enter Security Code'},
			<?php if (count($serverNames) === 1): ?>
				{'name':'server','description':'Server','type':'select','options':{<?php echo "'".$session->loginAthenaGroup->serverName."':'".$session->loginAthenaGroup->serverName."',"; ?>}},				
			<?php endif ?>	  
			<?php if (count($serverNames) > 1): ?>
				{'name':'server','description':'Server','type':'select','options':{<?php foreach ($serverNames as $serverName): echo "'".$serverNames."':'".$serverNames."',"; endforeach ?>}},
			<?php endif ?>
			
		  ]" 
        data-redirect-uri="http://www.agonyro.net/fluxcptest/?module=facebook&action=create&fb_return=return">
      </div>
</div>
</center>