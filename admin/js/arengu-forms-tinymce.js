(function () {
    tinymce.PluginManager.add('arengu_forms_button', function (editor, url) {
        editor.addButton('arengu_forms_button', {
            title: 'Insert Arengu Form shortcode',
            icon: false,
            image: url + '/../img/arengu-tinymce-button.png',
            onclick: function () {
                editor.insertContent('\[arengu-form id=\"YOUR_FORM_ID\"]');
            }
        });
    });
})();
