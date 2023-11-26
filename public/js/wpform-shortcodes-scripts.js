let main;

class MainController {
    init() {
        this.submitWpform();
    }

    /**
     * Handles the form submission
     */
    submitWpform() {
        const form = document.querySelector('.wpform--holder form'),
            submitButton = form.querySelector('button[type="button"]');

        submitButton.addEventListener('click', (event) => {
            event.preventDefault();

            submitButton.disabled = true;

            let formData = new FormData(form);

            formData.append('action', form.getAttribute('data-action'));

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.displayFormMessage(data.data, false);
                } else {
                    this.clearErrors();
                    if (data.data.validationErrors) {
                        this.displayValidationErrors(data.data.errors);
                    } else if (data.data.errors) {
                        this.displayFormMessage(data.data.errors, false);
                    }
                }
                submitButton.disabled = false;
            })
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
                submitButton.disabled = false;
            });
        });
    }

    /**
     * Dispplay the validation errors upon form submission
     * @param {array} errors 
     */
    displayValidationErrors(errors) {
        for (const field in errors) {
            const inputField = document.getElementsByName(field)[0];
            if (inputField) {
                const errorSpan = document.createElement('span');
                errorSpan.className = 'error-message';
                errorSpan.innerText = errors[field].join('\n');
                inputField.parentNode.insertBefore(errorSpan, inputField.nextSibling);
            }
        }
    }

    /**
     * Display a form success/error message
     * @param {string} errorMessage 
     * @param {boolean} success 
     */
    displayFormMessage(errorMessage, success = true) {
        const messagsHolder = document.querySelector("#form-messages--holder");

        if(messagsHolder && errorMessage) {
            messagsHolder.className = (success === true) ? "wpform-success-message" : "wpform-error-message";
            messagsHolder.innerText = errorMessage;
            
            setTimeout(() => {
                messagsHolder.innerText = "";
            }, 3000);
        }
    }

    /**
     * Clears the validation errors
     */
    clearErrors() {
        document.querySelectorAll('.error-message').forEach(function(errorSpan) {
            errorSpan.remove();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
  main = new MainController();
  main.init();
});
