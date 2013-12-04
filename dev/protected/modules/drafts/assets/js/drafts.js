(function ($) {
    $.fn.saveDraft = function (options) {
        var settings = $.extend({
            createUrl: '',
            getUrl: '',
            deleteUrl: '',
            timeOut: 0
        }, options);

        var form = $(this), formId = form.attr('id'), formData = {}, formFields = [], needDraft = true, localData, savedData, savedLocalData, loaded = false, xhr = {};

        xhr[formId] = null;

        window.onbeforeunload = function (e) {
            $.fn.saveDraft.save();
        }

        form.find('[type="submit"]').click(function (e) {
            //e.preventDefault();
            $.fn.saveDraft.save();
            //form.submit();
        });


        form.find(':input').each(function () {
            if ($(this).attr('name') != 'csrf') {
                if ($(this).attr('type') == 'checkbox' && !$(this).attr('checked')) {
                    needDraft = true;
                }
                else {
                    if ($(this).val() == '') {
                        needDraft = true;
                    }
                }
                formFields.push($(this));
            }
            $(this).change(function () {
                $.fn.saveDraft.save();
            });
        });

        if (needDraft) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: settings.getUrl,
                data: {formId: formId },
                /**
                 * do not show any errors if draft not loaded
                 */
                suppressErrors: true,
                success: function (data) {
                    savedData = data.data.data;
                    savedLocalData = $.fn.saveDraft.getLocalData();
                    if (data.data.deleted) {
                        $.fn.saveDraft.deleteDraft();
                    }
                    else {
                        /**
                         * if server data more than 10 seconds older we use local data
                         */
                        if (( savedLocalData.mtime - data.mtime ) > 10) {
                            $.fn.saveDraft.putFormData(savedLocalData);
                        }
                        else {
                            $.fn.saveDraft.putFormData(savedData);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    loaded = true;
                }
            });
        }

        $.fn.saveDraft.deleteDraft = function () {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: settings.deleteUrl,
                data: {formId: formId },
                success: function (data) {
                },
                error: function () {

                }
            });
            localStorage && localStorage.removeItem('draft' + formId);
        }

        $.fn.saveDraft.save = function () {
            if (!loaded) {
                return true;
            }
            formData = {};
            $.each(formFields, function () {
                /**
                 * if this is CSRF tag or submit button or hidden tag but not select2 hidden
                 * we skip it
                 */
                if (this.attr('name') == 'csrf' || this.attr('type') == 'submit' || ( this.attr('type') == 'hidden' && !this.hasClass('select2-offscreen') )) {
                    return true;
                }
                if (this.attr('type') == 'checkbox' && this.attr('checked') != 'checked') {
                    return true;
                }
                formData[this.attr('name')] = this.val();
            });

            if (xhr[formId] != null) {
                xhr[formId].abort();
            }

            xhr[formId] = $.ajax({
                type: 'post',
                dataType: 'json',
                url: settings.createUrl,
                data: {formId: formId, data: JSON.stringify(formData) },
                success: function (data) {
                }
            });

            formData['mtime'] = parseInt(new Date().getTime() / 1000);
            localStorage && localStorage.setItem('draft' + formId, JSON.stringify(formData));
        }

        $.fn.saveDraft.getLocalData = function () {
            try {
                return localStorage && JSON.parse(localStorage.getItem('draft' + formId));
            }
            catch (e) {
                return localData['mtime'] = 0;
            }
        }

        $.fn.saveDraft.putFormData = function (data) {
            form.find(':input').each(function () {
                var elem = $(this);
                $.each(data, function (key, val) {
                    if (elem.attr('name') == key) {
                        if (elem.attr('type') == 'checkbox') {
                            elem.attr('checked', 'checked');
                        }
                        else if (elem.get(0).tagName.toLowerCase() == 'textarea') {
                            elem.val(val);
                            /**
                             * костыль для imperavi
                             */
                            if (elem.redactor !== undefined) {
                                elem.redactor('set', val);
                            }
                        }
                        else {
                            elem.val(val);
                        }
                        elem.trigger('change');
                        return false;
                    }
                });
            });

            loaded = true;
        }
    }
}(jQuery));

