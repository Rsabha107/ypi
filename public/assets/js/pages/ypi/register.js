document.addEventListener("DOMContentLoaded", () => {
    console.log('Register.js loaded');
    
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
    );

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    console.log('CSRF token:', csrf ? 'Found' : 'NOT FOUND');

    const input = document.querySelector("#qid_files");
    const registerBtn = document.getElementById("registerBtn");
    const serverIdInput = document.getElementById("qid_server_id");
    const emailInput = document.getElementById("add_email");
    console.log('Email input element:', emailInput);

    // Get all required form fields
    const nameInput = document.getElementById("add_name");
    const qidInput = document.getElementById("add_qid");
    const phoneInput = document.getElementById("add_phone");
    const passwordInput = document.getElementById("password");
    const passwordConfirmInput = document.getElementById("password_confirmation");
    const termsCheckbox = document.getElementById("termsService");

    // Declare pond variable (will be initialized later)
    let pond = null;
    
    // Flag to track if email already exists
    let emailAlreadyExists = false;
    
    // Flag to prevent duplicate toastr displays
    let emailExistsToastrShown = false;

    // Disable register button initially
    registerBtn.disabled = true;
    registerBtn.classList.add('disabled');

    // Function to check if all required fields are filled
    const checkFormValidity = () => {
        const allFieldsFilled = 
            nameInput?.value.trim() !== '' &&
            emailInput?.value.trim() !== '' &&
            qidInput?.value.trim() !== '' &&
            phoneInput?.value.trim() !== '' &&
            passwordInput?.value.trim() !== '' &&
            passwordConfirmInput?.value.trim() !== '' &&
            termsCheckbox?.checked === true;

        // Check if any files are being uploaded
        const isUploading = pond && pond.getFiles().some((f) => f.status === FilePond.FileStatus.PROCESSING);
        
        // Enable button only if all fields filled, files uploaded, not uploading, and email doesn't exist
        const hasFiles = pond && pond.getFiles().length > 0;
        const shouldEnable = allFieldsFilled && hasFiles && !isUploading && !emailAlreadyExists;

        registerBtn.disabled = !shouldEnable;
        registerBtn.classList.toggle('disabled', !shouldEnable);
        
        // console.log('Form validity check:', {
        //     allFieldsFilled,
        //     hasFiles,
        //     isUploading,
        //     emailAlreadyExists,
        //     shouldEnable
        // });
    };

    // Add event listeners to all required fields (except email - handled separately above)
    [nameInput, qidInput, phoneInput, passwordInput, passwordConfirmInput].forEach(field => {
        if (field) {
            field.addEventListener('input', checkFormValidity);
            field.addEventListener('blur', checkFormValidity);
        }
    });

    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', checkFormValidity);
    }

    // Email validation - check if user exists
    let emailCheckTimeout;
    if (emailInput) {
        console.log('Email input found, attaching blur event');
        
        // Reset emailAlreadyExists flag when user modifies email
        emailInput.addEventListener('input', function() {
            emailAlreadyExists = false;
            emailExistsToastrShown = false; // Reset toastr flag when email changes
            checkFormValidity();
        });
        
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            console.log('Email blur event triggered:', email);
            
            // Basic email validation
            if (!email || !email.includes('@')) {
                console.log('Email validation failed - empty or no @');
                emailAlreadyExists = false;
                checkFormValidity();
                return;
            }

            console.log('Checking email existence...');
            // Check if email exists
            fetch('/register/check-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.exists) {
                    // Set flag that email exists
                    emailAlreadyExists = true;
                    checkFormValidity(); // Re-check form validity to disable button
                    
                    // Check if toastr is available
                    if (typeof toastr === 'undefined') {
                        console.error('Toastr is not loaded!');
                        alert(data.message + '\n\nPlease login instead.');
                        return;
                    }
                    
                    // Only show toastr if not already shown
                    if (!emailExistsToastrShown) {
                        emailExistsToastrShown = true;
                        
                        // Configure toastr
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-center",
                            "timeOut": "0",
                            "extendedTimeOut": "0",
                            "tapToDismiss": false,
                            "escapeHtml": false
                        };
                        
                        // Show toastr with login link
                        toastr.warning(
                            data.message + ' <br><a href="' + data.login_url + '" class="btn btn-sm btn-light mt-2" style="color: #000; background-color: #fff; border: 1px solid #000;">Go to Login</a>',
                            'Email Already Registered'
                        );
                    }
                    
                    // Optional: Clear the email field
                    // emailInput.value = '';
                } else {
                    // Email doesn't exist, allow registration
                    emailAlreadyExists = false;
                    emailExistsToastrShown = false;
                    checkFormValidity(); // Re-check form validity to enable button if all other conditions met
                }
            })
            .catch(error => {
                console.error('Error checking email:', error);
                emailAlreadyExists = false;
                emailExistsToastrShown = false;
                checkFormValidity();
            });
        });
    } else {
        console.log('Email input NOT found');
    }

    if (!input) return;

    const setButtonDisabled = (disabled) => {
        registerBtn.disabled = disabled;
        registerBtn.classList.toggle("is-disabled", disabled);
        registerBtn.dataset.originalText ??= registerBtn.innerText;
        registerBtn.innerText = disabled
            ? "Uploading…"
            : registerBtn.dataset.originalText;
    };

    // Initialize FilePond
    pond = FilePond.create(document.querySelector("#qid_files"), {
        name: "qid_files[]",
        allowMultiple: true,
        maxFiles: 2,
        maxFileSize: "2MB",
        acceptedFileTypes: [
            "image/png",
            "image/jpeg",
            "image/jpg",
            "image/gif",
            "image/webp",
            "application/pdf",
        ],
        labelIdle:
            'Drag & Drop QID Image or <span class="filepond--label-action">Browse</span>',
        server: {
            process: {
                url: "/uploads/process",
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
            },
            revert: {
                url: "/uploads/revert",
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": csrf },
            },
        },
    });

    const anyUploading = () =>
        pond
            .getFiles()
            .some((f) => f.status === FilePond.FileStatus.PROCESSING);

    // Disable immediately when upload starts
    pond.on("processfilestart", () => {
        setButtonDisabled(true);
        checkFormValidity();
    });

    // Re-enable when upload completes and nothing else uploading
    pond.on("processfile", () => {
        if (!anyUploading()) {
            setButtonDisabled(false);
            checkFormValidity();
        }
    });

    // If upload is aborted or errors out
    pond.on("processfileabort", () => {
        setButtonDisabled(false);
        checkFormValidity();
    });
    
    pond.on("processfileerror", () => {
        setButtonDisabled(false);
        checkFormValidity();
    });

    // When files are added
    pond.on("addfile", () => {
        checkFormValidity();
    });

    // Removing file should re-enable (unless another upload still running)
    pond.on("removefile", () => {
        if (!anyUploading()) {
            setButtonDisabled(false);
        }
        checkFormValidity();
    });

        // On form submit -> validate
    const form = document.querySelector("#spinner-form"); // your form id

    form.addEventListener("submit", function (e) {
        // Check if email validation has been done
        const email = emailInput?.value.trim();
        
        // If email exists flag is set, prevent submission
        if (emailAlreadyExists) {
            e.preventDefault();
            
            // Only show toastr if not already shown
            if (!emailExistsToastrShown) {
                toastr.warning('This email is already registered. Please use a different email or login.', 'Cannot Register');
                emailExistsToastrShown = true;
            }
            return false;
        }

        // If email hasn't been validated yet (user didn't blur), check it now
        if (email && email.includes('@') && !emailAlreadyExists) {
            e.preventDefault(); // Prevent default submission
            
            // Show loading state
            registerBtn.disabled = true;
            registerBtn.innerText = "Validating email...";
            
            // Check email one final time
            fetch('/register/check-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    // Email exists, show error and keep form blocked
                    emailAlreadyExists = true;
                    registerBtn.innerText = "Register";
                    checkFormValidity();
                    
                    // Only show toastr if not already shown
                    if (!emailExistsToastrShown) {
                        emailExistsToastrShown = true;
                        
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-center",
                            "timeOut": "0",
                            "extendedTimeOut": "0",
                            "tapToDismiss": false,
                            "escapeHtml": false
                        };
                        
                        toastr.warning(
                            data.message + ' <br><a href="' + data.login_url + '" class="btn btn-sm btn-light mt-2" style="color: #000; background-color: #fff; border: 1px solid #000;">Go to Login</a>',
                            'Email Already Registered'
                        );
                    }
                } else {
                    // Email is valid, proceed with form submission
                    emailAlreadyExists = false;
                    emailExistsToastrShown = false;
                    registerBtn.innerText = "Register";
                    
                    // Check file count before final submission
                    const count = pond.getFiles().length;
                    if (count === 0) {
                        toastr.error("Please upload participant QID File before submitting.");
                        input.closest(".filepond--wrapper")?.scrollIntoView({ behavior: "smooth", block: "center" });
                        checkFormValidity();
                    } else {
                        // All good, submit the form
                        form.submit();
                    }
                }
            })
            .catch(error => {
                console.error('Error checking email on submit:', error);
                registerBtn.innerText = "Register";
                emailExistsToastrShown = false;
                checkFormValidity();
            });
            
            return false; // Prevent immediate submission
        }

        // Check file count
        const count = pond.getFiles().length;
        if (count === 0) {
            e.preventDefault();
            toastr.error("Please upload participant QID File before submitting.");
            input.closest(".filepond--wrapper")?.scrollIntoView({ behavior: "smooth", block: "center" });
            return false;
        }
    });
});
