jQuery(document).ready(function ($) {
    $(".events-calendar-modules-for-divi_dismiss_notice").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var wrapper=$this.parents(".events-calendar-modules-for-divi-review-notice-wrapper");
        var ajaxURL=wrapper.data("ajax-url");
        var ajaxCallback=wrapper.data("ajax-callback");         
        var nonce = wrapper.data('nonce');
        $.post(ajaxURL, { 
            'action': ajaxCallback,
            'security': nonce
        }, function(data) {
            if (data.success) {
                wrapper.slideUp('fast');
            }
        }, "json");
    });
});