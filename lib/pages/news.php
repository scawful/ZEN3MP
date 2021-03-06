<div id="profileBox" class="boxes">
  <div class="titleBar">Support</div>
    <div class="article">
      <p>Zeniea is self-hosted on a Vultr cloud server in Chicago by <a href="/scawful">scawful</a>, so any support for the website is greatly appreciated. </p>

      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_donations" />
        <input type="hidden" name="business" value="ZNKWXNSABZM56" />
        <input type="hidden" name="currency_code" value="USD" />
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
      </form>


    </div>
</div>

<div id="sideBox" class="boxes">
  <div class="titleBar">Ads served by Google</div>
  <br />
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- TallBoi -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-8036786549441922"
       data-ad-slot="1140152702"
       data-ad-format="auto"
       data-full-width-responsive="true"></ins>
  <script>
       (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>




<div id="mainBox" class="boxes">
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" href="#site_news" aria-controls="site_news" data-toggle="tab" aria-selected="true">Site News</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#feedback" aria-controls="feedback" data-toggle="tab" aria-selected="true">Changelog</a>
    </li>
  </ul>



  <div class="tab-content" id="myTabContent">

    <div role="tabpanel" class="tab-pane fade show active" id="site_news" aria-labelledby="site_news">
      <div class="posts_area">
      </div>
      <img id="loading" src="img/icons/loading.gif">

      <!-- ZenieaBanner
      <div class="post">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" class="avatar">
        	<div class="feed-post-header"><strong>Advertisement</strong></div>
              <div class="postTime">Provided by Google&trade;</div>

        			<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-8036786549441922"
             data-ad-slot="1712720581"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>

      </div>
      -->
    </div>


    <div role="tabpanel" class="tab-pane fade" id="feedback" aria-labelledby="feedback">
      <div class="article">
        <h3>ZEN3MP</h3>
        <ul class="list-group">

          <li class="list-group-item list-group-item-primary"><span class="badge badge-dark">v0.42</span> </li>
          <li class="list-group-item list-group-item-light"><span class="badge badge-dark">v0.41</span> Changelog created, discussion boards page made, world databaste structure laid out, </li>
          <li class="list-group-item list-group-item-primary"><span class="badge badge-dark">v0.40</span> New styles Avalon, AcidTech, proSilver, and Pinkie. Item store skeleton and database structure. </li>
          <li class="list-group-item list-group-item-light"><span class="badge badge-dark">v0.30</span>-<span class="badge badge-dark">v0.39</span> Profile improvements, settings page, list all users page, search users </li>
          <li class="list-group-item list-group-item-primary"><span class="badge badge-dark">v0.20</span>-<span class="badge badge-dark">v0.29</span> User profiles, friend request, remove friends, respond to requests</li>
          <li class="list-group-item list-group-item-light"><span class="badge badge-dark">v0.10</span>-<span class="badge badge-dark">v0.19</span> Notification system, messaging, individual post page </li>
          <li class="list-group-item list-group-item-primary"><span class="badge badge-dark">v0.01</span>-<span class="badge badge-dark">v0.09</span> Social database structure created, posting forms, login and registration forms </li>
        </ul>
    </div>
  </div>

  </div>
</div>


<!-- Javascript for News Posts -->
<script>
   $(function(){

       var userLoggedIn = '<?php echo $userLoggedIn; ?>';
       var inProgress = false;

       loadPosts(); //Load first posts

       $(window).scroll(function() {
           var bottomElement = $(".status_post").last();
           var noMorePosts = $('.posts_area').find('.noMorePosts').val();

           // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
           if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
               loadPosts();
           }
       });

       function loadPosts() {
           if(inProgress) { //If it is already in the process of loading some posts, just return
               return;
           }

           inProgress = true;
           $('#loading').show();

           var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

           $.ajax({
               url: "lib/forms/ajax_load_news_posts.php",
               type: "POST",
               data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
               cache:false,

               success: function(response) {
                   $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage

                   $('#loading').hide();
                   $(".posts_area").append(response);

                   inProgress = false;
               }
           });
       }

       //Check if the element is in view
       function isElementInView (el) {
             if(el == null) {
                return;
            }

           var rect = el.getBoundingClientRect();

           return (
               rect.top >= 0 &&
               rect.left >= 0 &&
               rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
               rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
           );
       }
   });

  </script>
