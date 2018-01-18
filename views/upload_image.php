<?php $this->load->view('header'); ?>
<form action="<?= base_url(); ?>image/upload" method="post" enctype="multipart/form-data">
    <h3>Select image to upload:</h3>

    <input type="text" name="title" placeholder="Title">
    <input type="text" name="description" placeholder="Description">
    <input type="submit" value="Upload Image" name="submit">
    <input type="file" name="image" id="image">
</form>
<?php $this->load->view('footer'); ?>