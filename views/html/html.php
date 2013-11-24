<!DOCTYPE html>
<html lang="<?php echo I18n::$lang; ?>">
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <base href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">
    <?php foreach ($styles as $file => $file_info) echo HTML::style($file, array('media' => $file_info['media'])), PHP_EOL; ?>
    <?php foreach ($scripts as $file) echo HTML::script($file), PHP_EOL; ?>
  </head>
  <body>
    <div id="page-wrapper" class="wrapper"><div id="page">
      
      <div id="header-wrapper" class="wrapper"><div id="header">
        <?php echo $header; ?>
      </div></div>
      
      <div id="navigation-wrapper" class="wrapper"><div id="navigation">
        <?php echo $navigation; ?>
      </div></div>
      
      <div id="main-wrapper" class="wrapper"><div id="main">
        
        <div id="sidebar-wrapper" class="wrapper"><div id="sidebar">
          <?php echo $sidebar; ?>
        </div></div>
        
        <div id="content-wrapper" class="wrapper"><div id="content">
          <?php if (is_array($content)): ?>
          
          <?php if (isset($content['title'])): ?>
          <div class="title">
            <h2><?php echo $content['title']; ?></h2>
          </div>
          <?php endif; ?>
          
          <?php if (isset($content['content'])): ?>
          <div class="content">
            <?php echo $content['content']; ?>
          </div>
          <?php endif;?>
          
          <?php else: ?>
          <?php echo $content; ?>
          <?php endif; ?>
        </div></div>
        
      </div></div>
      
      <div id="footer-wrapper" class="wrapper"><div id="footer">
        <?php echo $footer; ?>
      </div></div>
      
    </div></div>
  </body>
</html>