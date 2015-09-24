<!DOCTYPE html>
<html><head>
	<meta http-equiv="content-type" content="text/html;">
    <meta charset="utf-8">
    <title><?php echo $title_page; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="eTunnel Config">
    <meta content="Gianluca Costa <g.costa@xplico.org>" name="author"/>
    
    <!-- Le styles -->
    <link href="<?php echo $ROOT_APP; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $ROOT_APP; ?>css/base_tmpl.css" type="text/css" media="all" rel="stylesheet" />
    <?php
        if (isset($custom_css)) {
            if (is_array($custom_css)) {
                foreach ($custom_css as $c_css) {
                    echo '<link href="'.$ROOT_APP.'css/'.$c_css.'" type="text/css" media="all" rel="stylesheet" />';
                }
            }
            else {
                echo '<link href="'.$ROOT_APP.'css/'.$custom_css.'" type="text/css" media="all" rel="stylesheet" />';
            }
        }
    ?>
    
    <!-- Le javascript
    ================================================== -->
    <script src="<?php echo $ROOT_APP; ?>js/jquery-1.11.2.min.js"></script>
    <script src="<?php echo $ROOT_APP; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo $ROOT_APP; ?>js/jquery.qtip.min.js"></script>
    <script src="<?php echo $ROOT_APP; ?>js/base_tmpl.js"></script>  
    <?php
        if (isset($custom_js)) {
             if (is_array($custom_js)) {
                foreach ($custom_js as $c_js) {
                    echo '<script type="text/javascript" src="'.$ROOT_APP.'js/'.$c_js.'"></script>';
                }
            }
            else {
                echo '<script type="text/javascript" src="'.$ROOT_APP.'js/'.$custom_js.'"></script>';
            }
        }
    ?>
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo $ROOT_APP; ?>js/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo $ROOT_APP; ?>images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $ROOT_APP; ?>images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $ROOT_APP; ?>images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $ROOT_APP; ?>images/apple-touch-icon-114x114.png">
  </head>

  <body class="<?php if (isset($body_class)) echo $body_class;?>" data-target="<?php if (isset($sidebar)) echo '#'.$sidebar;?>" data-spy="scroll">
	<div id="message_box">
		<div id="alert"><?php if (isset($esalert)) echo $esalert;?></div>
	</div>
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?php echo $ROOT_APP; ?>">eTunnel<small class="fsmall green">  <?php echo $app_version; ?></small></a>
            </div>
            <div class="navbar-collapse collapse">
                <?php if (isset($menu_left)) : ?>
                <ul class="nav navbar-nav">
                    <?php $i = 0; ?>
                    <?php foreach ($menu_left as $voce): ?>
                    <?php if ($i == $menu_left_active): ?>
                    <li class="active">
                    <?php else: ?>
                    <li>
                    <?php endif; ?>
                    <a title="<?php echo $voce['help']; ?>" href="<?php echo $voce['link']; ?>"><?php echo $voce['title']; ?></a>
                    </li>
                    <?php $i++; ?>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <?php if (isset($menu_right)) : ?>
                <ul class="nav navbar-nav navbar-right">
                    <?php $i = 0; ?>
                    <?php foreach ($menu_right as $voce): ?>
                    <?php if ($i == $menu_right_active): ?>
                    <li class="active">
                    <?php else: ?>
                    <li>
                    <?php endif; ?>
                    <a title="<?php echo $voce['help']; ?>" href="<?php echo $voce['link']; ?>"><?php echo $voce['title']; ?></a>
                    </li>
                    <?php $i++; ?>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div><!--/.nav-collapse -->
        </div>
    </div>

    <?php echo $page_content; ?>
    
    <div id="footer">
        <div class="container">
            <p>&copy; 2015 <a href="http://www.evolka.it">Evolka</a>. GNU Affero General Public License.</p>
        </div>
    </div>
    <?php if (!empty($php_errors)): ?>
    <div class="php_errors">
        <?php if (is_array($php_errors)) print_r($php_errors); else echo $php_errors; ?>
    </div>
    <?php endif; ?>
</body></html>
