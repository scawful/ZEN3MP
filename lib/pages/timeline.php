<!-- Profile Page Template -->
<div id="mainBox" class="boxes">
<div class="titleBar">Timeline</div>

  <div class="post_column">

    <div class="alert alert-primary alert-dismissible fade show" role="alert">
      <strong>Welcome to Zeniea!</strong> If you're interested check out these experimental pages.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <p>
        <ul style="list-style-type: square;">
          <li><a href="board.php">Discussion Boards</a></li>
        </ul>
      </p>

    </div>

      <form class="post_form" action="/" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="text" class="form-control" name="post_text" id="post_text" placeholder="Type something here..." style="width: 90%; float: left; overflow: scroll;">
        <button type="submit" name="post" id="post_button" value="POST" class="btn btn-post" style="float: right;">POST</button>
        <br /><br />
        <div class="custom-file">
          <label class="custom-file-label" for="customFile">Choose file (optional)</label>
          <input type="file" class="custom-file-input" id="customFile" name="fileToUpload">
        </div>
      </form>

    </div>

  <div class="posts_area"></div>
  <img id="loading" src="img/icons/loading.gif">

</div>

  <div id="sideBox" class="boxes">
    <div class="titleBar">Trending</div>
    <ul class="trending-list-group">
      <?php
      $query = mysqli_query($connect_social, "SELECT * FROM trends ORDER BY hits DESC LIMIT 10");

      foreach ($query as $row) {

        $word = $row['title'];
        $word_dot = strlen($word) >= 14 ? "..." : "";

        $trimmed_word = str_split($word, 14);
        $trimmed_word = $trimmed_word[0];

        echo "<li class='trending-list-group-item'>";
        echo $trimmed_word . $word_dot;
        echo "</li><br>";

      }
       ?>
     </ul>
  </div>

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
               url: "lib/forms/ajax_load_posts.php",
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
