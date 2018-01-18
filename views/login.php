<?php
$this->load->view('header');
?>

<div class="signin-form">

    <div class="container col-md-6">

        <h2 class="form-signin-heading text-center">Welcome to Pictures ! Please Login to continue !</h2><hr />

        <div class="row" id="alerts">
        </div>
        <form class="form-signin" method="post" id="login-form">


            <div id="error">
                <!-- error will be shown here ! -->
            </div>

            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email address" name="user_email" id="user_email" />
                <span id="check-e"></span>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" name="password" id="password" />
            </div>

            <hr />

            <div class="form-group">
                <button type="submit" class="btn btn-default" name="btn-login" id="btn-login">
                    <span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In
                </button>
            </div>

        </form>

    </div>

    <div class="col-md-6 text-center" id="with-bg" style="background-image: url(<?php echo base_url(); ?>/data/images_site/image.png); height: 450px; width: 450px;">
    </div>

</div>
<?php $this->load->view('footer'); ?>
</body>
