if ($(".validateform").length > 0) {
    $(".validateform").validate({
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            if (
                element.hasClass("select2-multi-select") ||
                element.hasClass("select2-single") ||
                element.hasClass("select2ajax")
                ) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass("select-multiple") ) {
                    error.insertAfter(element.next('.ms-options-wrap'));
                }else if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent());
                } else if (
                    element.prop("type") === "radio" &&
                    element.parent(".radio-inline").length
                ) {
                    error.insertAfter(element.parent().parent());
                } else if (
                    element.prop("type") === "checkbox" ||
                    element.prop("type") === "radio"
                ) {
                    error.appendTo(element.parent().parent());
                } else if(element.hasClass('.product-description' )) {
                    error.insertAfter(element.next('.mce-tinymce'));
                } else {
                    error.insertAfter(element);
                }
        },
        highlight: function (element) {
            $(element).parent().addClass("is-invalid");
            $(element).addClass("form-control-danger");
        },
        success: function (label, element) {
            $(element).parent().removeClass("is-invalid");
            $(element).removeClass("form-control-danger");
            label.remove();
        },
    });
}
if($(".registerValidateform").length > 0){
    $.validator.addMethod("strongPassword", function (value, element) {
        let minLength = value.length >= 8;
        let hasUpper = /[A-Z]/.test(value);
        let hasLower = /[a-z]/.test(value);
        let hasDigit = /[0-9]/.test(value);
        let hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(value);

        let conditionCount = [hasUpper, hasLower, hasDigit, hasSpecial]
            .filter(Boolean).length;

        return this.optional(element) || (minLength && conditionCount >= 3);
    }, "Password must be at least 8 characters long and contain at least 3 of the following: uppercase, lowercase, digit, special character.");
    $(".registerValidateform").validate({
        rules: {
            password: {
                required: true,
                strongPassword: true
            }
        },
        messages: {
            password: {
                required: "Please enter password"
            }
        },
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            if (
                element.hasClass("select2-multi-select") ||
                element.hasClass("select2-single") ||
                element.hasClass("select2ajax")
                ) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass("select-multiple") ) {
                    error.insertAfter(element.next('.ms-options-wrap'));
                }else if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent());
                } else if (
                    element.prop("type") === "radio" &&
                    element.parent(".radio-inline").length
                ) {
                    error.insertAfter(element.parent().parent());
                } else if (
                    element.prop("type") === "checkbox" ||
                    element.prop("type") === "radio"
                ) {
                    error.appendTo(element.parent().parent());
                } else if(element.hasClass('.product-description' )) {
                    error.insertAfter(element.next('.mce-tinymce'));
                } else {
                    error.insertAfter(element);
                }
        },
        highlight: function (element) {
            $(element).parent().addClass("is-invalid");
            $(element).addClass("form-control-danger");
        },
        success: function (label, element) {
            $(element).parent().removeClass("is-invalid");
            $(element).removeClass("form-control-danger");
            label.remove();
        },
    });
}
if($(".resetPasswordValidateform").length > 0){
    $.validator.addMethod("strongPassword", function (value, element) {
        let minLength = value.length >= 8;
        let hasUpper = /[A-Z]/.test(value);
        let hasLower = /[a-z]/.test(value);
        let hasDigit = /[0-9]/.test(value);
        let hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(value);

        let conditionCount = [hasUpper, hasLower, hasDigit, hasSpecial]
            .filter(Boolean).length;

        return this.optional(element) || (minLength && conditionCount >= 3);
    }, "Password must be at least 8 characters long and contain at least 3 of the following: uppercase, lowercase, digit, special character.");
    $(".resetPasswordValidateform").validate({
        rules: {
            password: {
                required: true,
                strongPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please enter your new password"
            },
            password_confirmation: {
                required: "Please confirm your new password",
                equalTo: "Passwords do not match"
            }
        },
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            if (
                element.hasClass("select2-multi-select") ||
                element.hasClass("select2-single") ||
                element.hasClass("select2ajax")
                ) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass("select-multiple") ) {
                    error.insertAfter(element.next('.ms-options-wrap'));
                }else if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent());
                } else if (
                    element.prop("type") === "radio" &&
                    element.parent(".radio-inline").length
                ) {
                    error.insertAfter(element.parent().parent());
                } else if (
                    element.prop("type") === "checkbox" ||
                    element.prop("type") === "radio"
                ) {
                    error.appendTo(element.parent().parent());
                } else if(element.hasClass('.product-description' )) {
                    error.insertAfter(element.next('.mce-tinymce'));
                } else {
                    error.insertAfter(element);
                }
        },
        highlight: function (element) {
            $(element).parent().addClass("is-invalid");
            $(element).addClass("form-control-danger");
        },
        success: function (label, element) {
            $(element).parent().removeClass("is-invalid");
            $(element).removeClass("form-control-danger");
            label.remove();
        },
    });
}
if($(".manageVolunteerForm").length > 0){
    $.validator.addMethod("strongPassword", function (value, element) {
        let minLength = value.length >= 8;
        let hasUpper = /[A-Z]/.test(value);
        let hasLower = /[a-z]/.test(value);
        let hasDigit = /[0-9]/.test(value);
        let hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(value);

        let conditionCount = [hasUpper, hasLower, hasDigit, hasSpecial]
            .filter(Boolean).length;

        return this.optional(element) || (minLength && conditionCount >= 3);
    }, "Password must be at least 8 characters long and contain at least 3 of the following: uppercase, lowercase, digit, special character.");
    $(".manageVolunteerForm").validate({
        rules: {
            password: {
                required: $("#password").length > 0,
                strongPassword: $("#password").length > 0
            }
        },
        messages: {
            password: {
                required: "Please enter password"
            }
        },
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            if (
                element.hasClass("select2-multi-select") ||
                element.hasClass("select2-single") ||
                element.hasClass("select2ajax")
                ) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass("select-multiple") ) {
                    error.insertAfter(element.next('.ms-options-wrap'));
                }else if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent());
                } else if (
                    element.prop("type") === "radio" &&
                    element.parent(".radio-inline").length
                ) {
                    error.insertAfter(element.parent().parent());
                } else if (
                    element.prop("type") === "checkbox" ||
                    element.prop("type") === "radio"
                ) {
                    error.appendTo(element.parent().parent());
                } else if(element.hasClass('.product-description' )) {
                    error.insertAfter(element.next('.mce-tinymce'));
                } else {
                    error.insertAfter(element);
                }
        },
        highlight: function (element) {
            $(element).parent().addClass("is-invalid");
            $(element).addClass("form-control-danger");
        },
        success: function (label, element) {
            $(element).parent().removeClass("is-invalid");
            $(element).removeClass("form-control-danger");
            label.remove();
        },
    });
}
/* -- Form - Validation -- */
window.addEventListener('load', function() {
var forms = document.getElementsByClassName('validateform');
var validation = Array.prototype.filter.call(forms, function(form) {
    form.addEventListener('submit', function(event) {
    if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
    }, false);
});
}, false);