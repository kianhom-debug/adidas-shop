$(document).ready(function() {
    // Trigger file upload when button is clicked
    $('#upload-btn').on('click', function() {
        $('#photo-upload').trigger('click');
    });
    
    // Handle file selection
    $('#photo-upload').on('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, PNG, GIF, and WEBP files are allowed');
            return;
        }
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        
        var formData = new FormData();
        formData.append('photo', file);
        
        $('#upload-progress').show();
        
        $.ajax({
            url: 'upload_photo.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#upload-progress').hide();
                if (response.success) {
                    if ($('#profile-photo').length) {
                        $('#profile-photo').attr('src', response.photo_url + '?t=' + Date.now());
                    } else {
                        location.reload();
                    }
                    alert('Profile photo updated!');
                } else {
                    alert(response.error);
                }
            },
            error: function() {
                $('#upload-progress').hide();
                alert('Upload failed. Please try again.');
            }
        });
    });
});