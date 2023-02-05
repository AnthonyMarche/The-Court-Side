if (document.getElementById('newsletter-body')) {
    let newsletterBody = document.getElementById('newsletter-body');
    let submitButton = document.getElementById('newsletter_submit');
    let titleInput = document.getElementById('newsletter_title');
    let contentInput = CKEDITOR.instances['newsletter_content'];
    let loader = document.getElementById('adminLoader');

    titleInput.addEventListener("input", updateButtonState);
    contentInput.on('change', updateButtonState);

    function updateButtonState() {
        if (titleInput.value.length > 0 && contentInput.getData().length > 0) {
            submitButton.removeAttribute("disabled");
        } else {
            submitButton.setAttribute("disabled", "disabled");
        }
    }

    submitButton.addEventListener("click", async function () {
        loader.classList.remove('d-none');
        newsletterBody.classList.add('body-admin');
    });
}
