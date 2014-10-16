if (!RedactorPlugins) {
    var RedactorPlugins = {};
}

RedactorPlugins.quote = {
    init: function () {
        var button = this.buttonAdd('quote', 'Quote');
        var redactor = this;
        var selectedText;

        $('.re-icon.re-quote.redactor-btn-image').on('mouseup' , function() {

            if (window.getSelection) {
                selectedText = window.getSelection();
            } else if (document.getSelection) {
                selectedText = document.getSelection();
            } else if (document.selection) {
                selectedText = document.selection.createRange().text;
            }

            redactor.buttonActive('quote');
            if ( selectedText ) {
                redactor.insertHtml("<blockquote>" + selectedText + "</blockquote>\n");
            }
            redactor.buttonInactive('quote');
        });




        // make your added button as Font Awesome's icon
        //this.button.setAwesome('quote', 'fa-quote-left');

        //this.button.addCallback(button, this.quote.testButton);
    }
};