<div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="signup-heading" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signup-heading">SignUp with PGLife</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="signup-form" class="form" role="form" method="post" action="api/signup_submit.php">
                    <!-- Full Name -->
                    <div class="input-group form-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control form-input" name="full_name" placeholder="Full Name" maxlength="30" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="input-group form-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-phone-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control form-input" name="phone" placeholder="Phone Number" maxlength="10" minlength="10" required>
                    </div>

                    <!-- Email -->
                    <div class="input-group form-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control form-input" name="email" placeholder="Email" required>
                    </div>

                    <!-- Password -->
                    <div class="input-group form-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control form-input" name="password" placeholder="Password" minlength="6" required>
                    </div>

                    <!-- College Name -->
                    <div class="input-group form-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-university"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control form-input" name="college_name" placeholder="College Name" maxlength="150" required>
                    </div>

                    <!-- Gender -->
                    <div class="form-group mb-4">
                        <span>I'm a</span>
                        <input type="radio" class="ml-3" id="gender-male" name="gender" value="male" /> Male
                        <input type="radio" class="ml-3" id="gender-female" name="gender" value="female" /> Female
                        <input type="radio" class="ml-3" id="gender-unisex" name="gender" value="unisex" /> Others
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-block btn-primary btn-submit">Create Account</button>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <span>Already have an account? 
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a>
                </span>
            </div>
        </div>
    </div>
</div>
