<?php $this->load->view('header'); ?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/css/page.css">
    <div class="container">
        <div class="row">
            <h1 class="text-center">Gallery</h1>

            <?php if ($results != []): ?>
                <?php foreach ($results as $data): ?>
                    <div class="col-lg-3 col-sm-4 col-xs-6 gallery_image" data-username="<?= $data->username; ?>"
                         data-image-id="<?= $data->id;; ?>">
                        <a description="<?= $data->description; ?>" title="<?= $data->title; ?>" href="#">
                            <img class="thumbnail img-responsive"
                                 src="<?= base_url() . 'data/images/' . $data->file_name . '' . $data->file_ext ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    </div>
    <div tabindex="-1" class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h2 class="modal-username"
                        style="font-family: Comic Sans MS cursive sans-serif; text-align:center; margin: 0px; padding: 0px">
                        Heading</h2>
                    <h3 class="modal-title" style="font-family:verdana;">Heading</h3>
                </div>
                <div class="modal-body">
                    <div class="image-placeholder"></div>
                    <p class="modal-description" style="font-family:verdana;"></p>

                    <input name="image_id" value="" id="image_id_modal" hidden>
                    <?php if (!empty($this->session->userdata('user'))): ?>
                        <div id="comment-message" class="form-row">
                            <textarea maxlength="250" name="comment" placeholder="Comment" id="comment"></textarea>
                        </div>
                        <input type="submit" name="submit" id="commentSubmit" value="Submit Comment">
                        <div id="comentsAndUsers"></div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?= $links; ?>

<?php $this->load->view('footer'); ?>