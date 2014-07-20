<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Call Me Maybe</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimal-ui">
    <meta name="mobile-web-app-capable" content="yes">
    <link href='http://fonts.googleapis.com/css?family=Bitter:700|Open+Sans:400,400italic|Source+Code+Pro' rel='stylesheet' type='text/css'>
    <link href="<?=base_url('css/style.css')?>" media="all" rel="stylesheet" type="text/css" />
  </head>
  <body id="top">


    <header id="header" itemscope itemtype="http://schema.org/Organization">
      <h1 itemprop="name">Call Me Maybe</h1>
    </header>

    <div class="wrapper">

      <p id="intro">Tired of waiting on hold for a customer service rep? Call Me Maybe does the waiting for you and calls you back when a real live person is on the line.</p>

      <form id="app" class="animated fadeIn" action="<?=base_url('index.php/user/call')?>" method="post">
        <ul class="form-fields">
           <li id="li_phone">
              <label>How can we reach you?</label>
              <input type="tel" id="phone" name="phone" class="text-input" placeholder="(800) 555-5555"/>
           </li>
           <li id="li_email">
              <label>Where can we email you the recorded conversation?</label>
              <input data-next="#outbound" id="email" type="email" name="email" class="text-input" placeholder="your-email@example.com"/>
           </li>
           <li id="li_outbound">
              <label>Who can we call for you?</label>
              <input type="text" name="outbound" id="outbound" class="text-input typeahead" autocomplete="off" placeholder="Name of a company&hellip;" style="background-color: #111;"/>
              
           </li>
        </ul>
        <img src="<?=base_url('images/myloadingimage.gif')?>" style="display: none;" id="loading_image">
      </form>

    </div>

    <script src="<?=base_url('js/jquery-1.11.0.min.js')?>" charset="utf-8"></script>
    <script src="<?=base_url('js/jquery.smooth-scroll.min.js')?>" charset="utf-8"></script>
    <script src="<?=base_url('js/typeahead.bundle.min.js')?>" charset="utf-8"></script>
    <script src="<?=base_url('js/jquery.maskedinput.min.js')?>" type="text/javascript"></script>
    <script src="<?=base_url('js/script.js')?>" charset="utf-8"></script>
    
  </body>
</html>
