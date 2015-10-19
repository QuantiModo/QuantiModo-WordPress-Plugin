<?php

wp_enqueue_style("bootstrap", plugins_url('../../', __FILE__) . "css/bootstrap.min.css");
wp_enqueue_style("qm-ionic-styles", plugins_url('../../', __FILE__) . "css/qm-ionic-app.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("qm-ionic-app",
    plugins_url('../../', __FILE__) . "js/qm-ionic-app.js", "jquery", false, true);

?>
<p>Hello from ionic app</p>

<div>
    <div id="qm-ionic-button-holder">
        <img id="qm-ionic-app-show-hide" src="<?php echo plugins_url('../../', __FILE__) . "images/quantimodo.png" ?>">
        </img>
    </div>
    <div id="ionic-app-holder">
        <iframe src="http://qm-ionic.herokuapp.com" frameborder="0"></iframe>
    </div>
</div>



