if (document.querySelector('.password-input')) {
    const errorContainer = document.querySelector('.errors-container');
    const passwordInputs = document.querySelectorAll('.password-input');
    const newPasswordInput = passwordInputs[0];
    const repeatPasswordInput = passwordInputs[1];
    const passwordRequirement = document.getElementById('password-requirement');

    const requirements = {
        'twelve-characters': /.{12,}/,
        'one-uppercase': /[A-Z]/,
        'one-lowercase': /[a-z]/,
        'one-number': /\d/,
        'one-special-character': /[!?@#$%^&*()+-]/,
    };

    // Determine the form to use
    const changePasswordForm = document.querySelector('form[name="change_password"]');
    const registrationForm = document.querySelector('form[name="registration_form"]');
    const resetPasswordForm = document.querySelector('form[name="reset_password"]');
    let passwordForm;

    if (changePasswordForm) {
        passwordForm = changePasswordForm;
    } else if (registrationForm) {
        passwordForm = registrationForm;
    } else if (resetPasswordForm) {
        passwordForm = resetPasswordForm;
    }

    let isPasswordValid = true;
    let isRepeatPasswordValid = true;

    // Event listener for new password input changes
    newPasswordInput.addEventListener('input', function () {
        isPasswordValid = true; // Reset to true on each input event

        for (const requirementId in requirements) {
            const regex = requirements[requirementId];
            const requirement = document.getElementById(requirementId);
            const icon = requirement.querySelector('i');

            if (!regex.test(this.value)) {
                isPasswordValid = false;
                requirement.classList.remove('text-success');
                requirement.classList.add('text-danger');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-xmark');
            } else {
                requirement.classList.remove('text-danger');
                requirement.classList.add('text-success');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-check');
            }
        }

        updateValidationBorder(newPasswordInput, isPasswordValid);
    });

    // Event listener for repeat password input changes
    repeatPasswordInput.addEventListener('input', function () {
        const repeatPasswordValue = this.value;
        const newPasswordValue = newPasswordInput.value;

        isRepeatPasswordValid = repeatPasswordValue === newPasswordValue;
        updateValidationBorder(repeatPasswordInput, isRepeatPasswordValid)
    });

    // Event listener for form submit
    passwordForm.addEventListener('submit', function (event) {
        if (!isPasswordValid || !isRepeatPasswordValid) {
            event.preventDefault();
            let errors = [];
            if (!isPasswordValid) {
                errors.push('Le mot de passe ne respecte pas les conditions requises')
            }
            if (!isRepeatPasswordValid) {
                errors.push('La confirmation du mot de passe est incorrect')
            }
            errorContainer.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
            errorContainer.classList.remove('d-none')
        }
    });

    // Function to update input border based on validation
    function updateValidationBorder(input, condition) {
        if (condition) {
            input.classList.remove('invalid-border');
            input.classList.add('valid-border');
        } else {
            input.classList.add('invalid-border');
            input.classList.remove('valid-border');
        }
    }

    // Event listener to toggle password requirement display
    document.addEventListener('click', function (event) {
        const clickedElement = event.target;

        if (
            clickedElement === newPasswordInput ||
            clickedElement === repeatPasswordInput ||
            clickedElement === passwordRequirement ||
            passwordRequirement.contains(clickedElement)
        ) {
            passwordRequirement.classList.remove('d-none');
            passwordRequirement.classList.add('d-block');
            return;
        }
        passwordRequirement.classList.remove('d-block');
        passwordRequirement.classList.add('d-none');
    });
}
