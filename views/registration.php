<?php
$this->load->view('header');
?>

    <div class="signin-form">

        <div class="col-md-6">

            <form class="form-signin" action="" method="post">

                <fieldset>

                    <div id="legend">

                        <legend class="text-center">Register</legend>

                    </div>

                    <?php echo validation_errors(); ?>

                    <?= $error ?>
                    <div class="form-group">

                        <!-- Username -->

                        <label class="control-label" for="username">Username</label>

                        <div class="controls">

                            <input type="text" id="username" name="username"
                                   value="<?php echo set_value('username'); ?>" placeholder="" class="form-control">

                            <p class="help-block">Username can contain any letters or numbers, without spaces</p>

                        </div>

                    </div>


                    <div class="form-group">
                        <!-- E-mail -->

                        <label class="control-label" for="email">E-mail</label>

                        <div class="controls">

                            <input type="text" id="email" name="email" value="<?php echo set_value('email'); ?>"
                                   placeholder="" class="form-control">

                            <p class="help-block">Please provide your E-mail</p>

                        </div>

                    </div>


                    <div class="form-group">
                        <!-- Password-->

                        <label class="control-label" for="password">Password</label>

                        <div class="controls">

                            <input type="password" id="password" name="password"
                                   value="<?php echo set_value('password'); ?>" placeholder="" class="form-control">

                            <p class="help-block">Password should be at least 8 characters, must contain at least 1 number, 1 lowercase and 1 uppercase letter and 1 special character,</p>

                        </div>

                    </div>


                    <div class="form-group">

                        <!-- Password -->
                        <label class="control-label" for="password">Password (Confirm)</label>

                        <div class="controls">

                            <input type="password" id="password_confirm" name="passwordRepeat"
                                   value="<?php echo set_value('passwordRepeat'); ?>" placeholder=""
                                   class="form-control">

                            <p class="help-block">Please confirm password</p>

                        </div>

                    </div>


                    <div class="form-group">

                        <!-- Button -->
                        <div class="controls">

                            <input type="submit" class="btn" name="submit"/>

                        </div>

                    </div>

                </fieldset>

            </form>

        </div>

    </div>

    <div class="col-md-6 text-center" id="with-bg"
         style="background-image: url(<?php echo base_url(); ?>/data/images_site/image.png); height: 450px; width: 450px;">
    </div>

    </div>

<?php
$this->load->view('footer');
?>