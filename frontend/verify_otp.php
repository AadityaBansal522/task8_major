<?php session_start(); ?>
<?php include 'includes/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    
    <div class="card shadow-sm p-4 border border-secondary" style="width: 100%; max-width: 420px; border-radius: 15px;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold"> Verify OTP</h3>
            <p class="text-muted">Enter the OTP sent to your email</p>
        </div>

        <div id="msg"></div>

        <div class="mb-3">
            <label class="form-label fw-semibold">OTP Code</label>
            <input type="text" id="otp" class="form-control text-center" 
                   placeholder="Enter 6-digit OTP" maxlength="6"
                   style="letter-spacing: 5px; font-size: 18px;">
        </div>

        
        <button id="verifyBtn" class="btn btn-success w-100 py-2 fw-semibold">
            Verify OTP
        </button>

    </div>

</div>

<style>
.card {
    border: none;
}

.form-control:focus {
    border-color: #0d6efd;
}

#verifyBtn {
    transition: 0.3s ease;
}

#verifyBtn:hover {
    transform: scale(1.02);
}
</style>

<script>
$('#verifyBtn').click(function(){

    $.ajax({
        url: 'verify_otp_ajax.php',
        type: 'POST',
        data: {
            otp: $('#otp').val()
        },
        dataType: 'json',

        success: function(res){
            if(res.status === 'success'){
                $('#msg').html('<div class="alert alert-success">'+res.message+'</div>');
            } else {
                $('#msg').html('<div class="alert alert-danger">'+res.message+'</div>');
            }
        },

        error: function(){
            $('#msg').html('<div class="alert alert-danger">Server error</div>');
        }
    });

});
</script>