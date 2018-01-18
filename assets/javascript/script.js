$('document').ready(function () {

    // Events

    //When is submit submit the form
    $("#login-form").on('submit', function (event) {
        event.preventDefault()
        submitForm();
    });

    //When clicked getting image id and username and attaching them in the html
    $('.gallery_image').click(function () {
        var image_id = $(this).data('image-id');
        var username = $(this).data('username');
        $('#image_id_modal').attr('value', image_id);
        $('.modal-username').html(username);
    });

    //Clicking on picture
    $('.thumbnail').click(function () {


        $('.image-placeholder').empty();
        $('.modal-description').empty();

        //Getting the title and the description
        var title = $(this).parent('a').attr("title");
        var description = $(this).parent('a').attr("description");


        //Getting the image id
        var image_id = $(this).parent().parent().data('imageId');

        //Appending the comments for the picture
        appendComments(image_id);

        //Setting the title and the description in the modal picture
        $('.modal-title').html('Title: ' + '<br>' + title);
        $('.modal-description').html('Description: ' + description);
        $($(this).parents('div').html()).appendTo('.image-placeholder');
        $('#myModal').modal({show: true});
    });

    //Appending the comments for picture
    function appendComments(image_id) {

        //Get the contents
        $('#comentsAndUsers').html('');

        $.ajax({

            type: 'GET',
            url: '/codeigniter/image/' + image_id + '/comments',
            success: function (response) {
                response = JSON.parse(response);

                if (response.status == 'ok') {

                    //Appending the comments view from the controller
                    $('#comentsAndUsers').append(response.comments);

                    $('.commentDelete').click(function () {
                        var comment = $(this).parent().find('#comment-message textarea').val();
                        var image_id = $(this).parent().find('#image_id_modal').val();
                        var comment_id = $(this).data('commentId');

                        $.ajax({
                            type: 'POST',
                            url: '/codeigniter/admin/comment/' + comment_id +'/delete',

                            success: function (response) {
                                $('#comment_' + comment_id).remove()

                            }
                        });
                    });

                }

            }
        });
    }


    //When clicked getting the values of the comment and image id
    $('#commentSubmit').click(function () {
        var comment = $(this).parent().find('#comment-message textarea').val();
        var image_id = $(this).parent().find('#image_id_modal').val();

        $.ajax({
            type: 'POST',
            url: '/codeigniter/image/' + image_id + '/comment',
            data: {'comment': comment},
            beforeSend: function () {
                $('#alerts').html('');
            },
            success: function (response) {
                var response = JSON.parse(response);

                //If status = ok
                if (response.status) {

                    //Calling function appendComments to append the comments with image_id from the url
                    appendComments(image_id);
                    document.getElementById('comment').value = "";

                    //If status errors
                } else {

                    response.errors.forEach(function (error) {
                        var element = $('<div class="alert alert-warning" role="alert"></div>').append(error);
                        $('#alerts').append(element);
                    });

                    document.getElementById('comment').value = "";

                }

                window.setTimeout(function() {
                    $(".alert").fadeTo(200, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 5000);


            }
        });
    });



    $('#myModal').on('shown.bs.modal', function() {
        //When clicked getting the values of the comment and image id
        $('.commentDelete').click(function () {
            var comment = $(this).parent().find('#comment-message textarea').val();
            var image_id = $(this).parent().find('#image_id_modal').val();
            var comment_id = $(this).data('commentId');

            $.ajax({
                type: 'POST',
                url: '/codeigniter/admin/comment/' + comment_id +'/delete',

                success: function (response) {
                    $('#comment_' + comment_id).remove()

                    var response = JSON.parse(response);

                }
            });
        });
    }) ;


    //Open modal
    $('.edit_modal').click(function () {

        //Getting user id
        var userID = $(this).data('userid');

        $.ajax({
            type: 'GET',
            url: '/codeigniter/admin/user/' + userID + '/editModal',
            success: function (data) {
                //Giving the data to the modal
                $('#user_edit_modal').html(data);

                //Calling the save_user_edit function
                save_user_edit();
            }
        });

    });

    //Saving edited user
    function save_user_edit() {
        $('.save_user_edit').click(function () {

            //Getting the user id
            var userID = $('#userid').data('userid');

            //Getting the username
            var data = {
                'username': $('#username').val()
            };

            //Updating user - username
            $.ajax({
                type: 'POST',
                url: '/codeigniter/admin/user/' + userID + '/edit',
                data: data,
                success: function (data) {
                    location.href = "/codeigniter/admin/users/table";
                }
            });
        });
    }


    /* login submit */
    function submitForm() {

        //Serialize the data from the form
        var data = $("#login-form").serialize();

        $.ajax({

            type: 'POST',
            url: '/codeigniter/login',
            data: data,
            beforeSend: function () {
                $("#error").fadeOut();
                $("#btn-login").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; sending ...');
                $('#alerts').html('');
            },
            success: function (response) {
                response = JSON.parse(response);

                //If there is errors
                if (response.errors) {

                    response.errors.forEach(function (error) {
                        var element = $('<div class="alert alert-warning" role="alert"></div>').append(error);
                        $('#alerts').append(element);
                    });

                    //No errors
                } else {
                    location.href = '/codeigniter/home';
                }

                window.setTimeout(function() {
                    $(".alert").fadeTo(200, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 5000);
            }
        });

        $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
        return false;
    }

    /* login submit */


    //Loading the JS datatables
    $(document).ready(function () {
        $('#table_id').DataTable();
    });

});
