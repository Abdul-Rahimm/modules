(function ($, Drupal) {
    Drupal.behaviors.businessInfoForm = {
        attach: function (context, settings) {
            // Input mask for ABN field.
            $('#edit-abn', context).once('businessInfoMask').each(function () {
                $(this).on('input', function () {
                    this.value = this.value
                        .replace(/\D/g, '') // Remove non-numeric characters.
                        .replace(/(\d{2})(\d{3})(\d{3})(\d{3})/, '$1 $2 $3 $4') // Add space formatting.
                        .substring(0, 14); // Limit to 14 characters including spaces.
                });
            });

            // Input mask for ACN field.
            $('#edit-acn', context).once('businessInfoMask').each(function () {
                $(this).on('input', function () {
                    this.value = this.value
                        .replace(/\D/g, '') // Remove non-numeric characters.
                        .replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3') // Add space formatting.
                        .substring(0, 11); // Limit to 11 characters including spaces.
                });
            });

            // Input mask for Business Phone field.
            $('#edit-business-phone', context).once('businessInfoMask').each(function () {
                $(this).on('input', function () {
                    this.value = this.value
                        .replace(/\D/g, '') // Remove non-numeric characters.
                        .replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3') // Add space formatting.
                        .substring(0, 12); // Limit to 12 characters including spaces.
                });
            });

            // Real-time validation for Operational Hours format.
            $('#edit-operational-hours', context).once('businessInfoValidation').each(function () {
                $(this).on('input', function () {
                    if (!/^\d{1,2}:\d{2}(AM|PM)-\d{1,2}:\d{2}(AM|PM)$/.test(this.value)) {
                        $(this).addClass('error');
                    } else {
                        $(this).removeClass('error');
                    }
                });
            });

            // Real-time validation for Business Email.
            $('#edit-business-email', context).once('businessInfoValidation').each(function () {
                $(this).on('input', function () {
                    if (!/^[\w-.]+@[\w-]+\.[a-z]{2,4}$/.test(this.value)) {
                        $(this).addClass('error');
                    } else {
                        $(this).removeClass('error');
                    }
                });
            });
        }
    };
})(jQuery, Drupal);